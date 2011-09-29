<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2011 Ingo Renner <ingo.renner@dkd.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * General frontend page indexer.
 *
 * @author	Ingo Renner <ingo.renner@dkd.de>
 * @author	Daniel Poetzinger <poetzinger@aoemedia.de>
 * @author	Timo Schmidt <schmidt@aoemedia.de>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_Typo3PageIndexer {

	/**
	 * Solr server connection.
	 *
	 * @var	tx_solr_SolrService
	 */
	protected $solrConnection = NULL;

	/**
	 * Frontend page object (TSFE).
	 *
	 * @var	tslib_fe
	 */
	protected $page = NULL;

	/**
	 * Content extractor to extract content from TYPO3 pages
	 *
	 * @var	tx_solr_Typo3PageContentExtractor
	 */
	protected $contentExtractor = NULL;

	/**
	 * URL to be indexed as the page's URL
	 *
	 * @var	string
	 */
	protected $pageUrl = '';

	/**
	 * The page's access rootline
	 *
	 * @var	tx_solr_access_Rootline
	 */
	protected $pageAccessRootline = NULL;

	/**
	 * Indexer mode, either Frontend or IndexQueue
	 *
	 * @var	string
	 */
	protected $indexerMode = '';

	/**
	 * ID of the current page's Solr document.
	 *
	 * @var	string
	 */
	protected static $pageSolrDocumentId = '';

	/**
	 * The Solr document generated for the current page.
	 *
	 * @var	Apache_Solr_Document
	 */
	protected static $pageSolrDocument = NULL;


	/**
	 * Constructor for class tx_solr_Indexer
	 *
	 * @param	tslib_fe	$page The page to index
	 */
	public function __construct(tslib_fe $page) {
		$this->page        = $page;
		$this->indexerMode = tx_solr_IndexerSelector::INDEXER_STRATEGY_FRONTEND;
		$this->pageUrl     = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');

		try {
			$this->initializeSolrConnection();
		} catch (Exception $e) {
			$this->log($e->getMessage() . ' Error code: ' . $e->getCode(), 3);

				// TODO extract to a class "ExceptionLogger"
			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['logging.']['exceptions']) {
				t3lib_div::devLog('Exception while trying to index a page', 'tx_solr', 3, array(
					$e->__toString()
				));
			}
		}

		$this->contentExtractor = t3lib_div::makeInstance(
			'tx_solr_Typo3PageContentExtractor',
			$this->page->content,
			$this->page->renderCharset
		);

		$this->pageAccessRootline = t3lib_div::makeInstance(
			'tx_solr_access_Rootline',
			''
		);
	}

	/**
	 * Initializes the Solr server connection.
	 *
	 * @throws	Exception when no Solr connection can be established.
	 */
	protected function initializeSolrConnection() {
		$solr = t3lib_div::makeInstance('tx_solr_ConnectionManager')->getConnectionByPageId(
			$this->page->id,
			$this->page->sys_language_uid
		);

			// do not continue if no server is available
		if (!$solr->ping()) {
			throw new Exception(
				'No Solr instance available while trying to index a page.',
				1234790825
			);
		}

		$this->solrConnection = $solr;
	}

	/**
	 * Indexes a page.
	 *
	 * @return	boolean	TRUE after successfully indexing the page, FALSE on error
	 */
	public function indexPage() {
		$pageIndexed = FALSE;
		$documents   = array(); // this will become usefull as soon as when starting to index individual records instead of whole pages

		if (is_null($this->solrConnection)) {
				// intended early return as it doesn't make sense to continue
				// and waste processing time if the solr server isn't available
				// anyways
				// FIXME use an exception
			return $pageIndexed;
		}

		$pageDocument = $this->getPageDocument();
		$pageDocument = $this->substitutePageDocument($pageDocument);
		self::$pageSolrDocument = $pageDocument;
		$documents[]  = $pageDocument;
		$documents    = $this->getAdditionalDocuments($pageDocument, $documents);
		$this->processDocuments($documents);

		$pageIndexed = $this->addDocumentsToSolrIndex($documents);

		return $pageIndexed;
	}

	/**
	 * Given a page id, returns a document representing that page.
	 *
	 * @return	Apache_Solr_Document	A documment representing the page
	 */
	protected function getPageDocument() {
		$document   = t3lib_div::makeInstance('Apache_Solr_Document');
		$cHash      = $this->filterInvalidContentHash($this->page->cHash);
		$pageRecord = $this->page->page;

		self::$pageSolrDocumentId = $documentId = tx_solr_Util::getPageDocumentId(
			$this->page->id,
			$this->page->type,
			$this->page->sys_language_uid,
			$this->getDocumentIdGroups(),
			$cHash
		);
		$document->setField('id',          $documentId);
		$document->setField('site',        t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
		$document->setField('siteHash',    tx_solr_Util::getSiteHash());
		$document->setField('appKey',      'EXT:solr');
		$document->setField('type',        'pages');
		$document->setField('contentHash', $cHash);

			// system fields
		$document->setField('uid',      $this->page->id);
		$document->setField('pid',      $pageRecord['pid']);
		$document->setField('typeNum',  $this->page->type);
		$document->setField('created',  $pageRecord['crdate']);
		$document->setField('changed',  $pageRecord['tstamp']);
		$document->setField('language', $this->page->sys_language_uid);

			// access
		$document->setField('access',      (string) $this->pageAccessRootline);
		if ($this->page->page['endtime']) {
			$document->setField('endtime', $pageRecord['endtime']);
		}

			// content
		$document->setField('title',       $this->utf8encode($this->contentExtractor->getPageTitle()));
		$document->setField('subTitle',    $this->utf8encode($pageRecord['subtitle']));
		$document->setField('navTitle',    $this->utf8encode($pageRecord['nav_title']));
		$document->setField('author',      $this->utf8encode($pageRecord['author']));
		$document->setField('description', $this->utf8encode($pageRecord['description']));
		$document->setField('abstract',    $this->utf8encode($pageRecord['abstract']));
		$document->setField('content',     $this->contentExtractor->getIndexableContent());
		$document->setField('url',         $this->pageUrl);

			// keywords
		$keywords = array_unique(t3lib_div::trimExplode(
			',',
			$this->utf8encode($pageRecord['keywords'])
		));
		foreach ($keywords as $keyword) {
			$document->addField('keywords', $keyword);
		}

			// content from several tags like headers, anchors, ...
		$tagContent = $this->contentExtractor->getTagContent();
		foreach ($tagContent as $fieldName => $fieldValue) {
			$document->setField($fieldName, $fieldValue);
		}

		return $document;
	}

	/**
	 * Adds the collected documents to the Solr index.
	 *
	 * @param	array	$documents An array of Apache_Solr_Document objects.
	 */
	protected function addDocumentsToSolrIndex(array $documents) {
		$documentsAdded = FALSE;

		if (!count($documents)) {
			return $documentsAdded;
		}

		try {
			$this->log('Adding ' . count($documents) . ' documents.', 0, $documents);

				// chunk adds by 20
			$documentChunks = array_chunk($documents, 20);
			foreach ($documentChunks as $documentChunk) {
				$this->solrConnection->addDocuments($documentChunk);
			}

			$documentsAdded = TRUE;
		} catch (Exception $e) {
			$this->log($e->getMessage() . ' Error code: ' . $e->getCode(), 2);

			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['logging.']['exceptions']) {
				t3lib_div::devLog('Exception while adding documents', 'tx_solr', 3, array(
					$e->__toString()
				));
			}
		}

		return $documentsAdded;
	}

	/**
	 * Allows third party extensions to replace or modify the page document
	 * created by this indexer.
	 *
	 * @param	Apache_Solr_Document	$pageDocument The page document created by this indexer.
	 * @return	Apache_Solr_Document	An Apache Solr document representing the currently indexed page
	 */
	protected function substitutePageDocument(Apache_Solr_Document $pageDocument) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['Indexer']['indexPageSubstitutePageDocument'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['Indexer']['indexPageSubstitutePageDocument'] as $classReference) {
				$substituteIndexer = t3lib_div::getUserObj($classReference);

				if ($substituteIndexer instanceof tx_solr_SubstitutePageIndexer) {
					$substituteDocument = $substituteIndexer->getPageDocument($pageDocument);

					if ($substituteDocument instanceof Apache_Solr_Document) {
						$pageDocument = $substituteDocument;
					} else {
						// TODO throw an exception
					}
				} else {
					// TODO throw an exception
				}
			}
		}

		return $pageDocument;
	}

	/**
	 * Allows third party extensions to provide additional documents which
	 * should be indexed for the current page.
	 *
	 * @param	Apache_Solr_Document	$pageDocument The main document representing this page.
	 * @param	array	$existingDocuments An array of documents already created for this page.
	 * @return	array	An array of additional Apache_Solr_Document objects to index
	 */
	protected function getAdditionalDocuments(Apache_Solr_Document $pageDocument, array $existingDocuments) {
		$documents = $existingDocuments;

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['Indexer']['indexPageAddDocuments'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['Indexer']['indexPageAddDocuments'] as $classReference) {
				$additionalIndexer = t3lib_div::getUserObj($classReference);

				if ($additionalIndexer instanceof tx_solr_AdditionalIndexer) {
					$additionalDocuments = $additionalIndexer->getAdditionalDocuments($pageDocument, $documents);

					if (is_array($additionalDocuments)) {
						$documents = array_merge($documents, $additionalDocuments);
					}
				} else {
					// TODO throw an exception
				}
			}
		}

		return $documents;
	}

	/**
	 * Sends the given documents to the field processing service which takes
	 * care of manipulating fields as defined in the field's configuration.
	 *
	 * @param	array	An array of documents to manipulate
	 */
	protected function processDocuments(array $documents) {
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['index.']['fieldProcessingInstructions.'])) {
			$service = t3lib_div::makeInstance('tx_solr_fieldprocessor_Service');
			$service->processDocuments(
				$documents,
				$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['index.']['fieldProcessingInstructions.']
			);
		}
	}


	// Logging
	// TODO replace by a central logger


	/**
	 * Logs messages to devlog and TS log (admin panel)
	 *
	 * @param	string		Message to set
	 * @param	integer		Error number
	 * @return	void
	 */
	protected function log($message, $errorNum = 0, array $data = array()) {
		if (is_object($GLOBALS['TT'])) {
			$GLOBALS['TT']->setTSlogMessage('tx_solr: ' . $message, $errorNum);
		}

		if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['logging.']['indexing']) {
			if (!empty($data)) {
				$logData = array();
				foreach ($data as $value) {
					$logData[] = (array) $value;
				}
			}

			t3lib_div::devLog($message, 'tx_solr', $errorNum, $logData);
		}
	}


	// Misc


	/**
	 * Checks whether a given string is a valid cHash.
	 * If the hash is valid it will be returned as is, an empty string will be
	 * returned otherwise.
	 *
	 * @param	string	The cHash to check for validity
	 * @return	string	The passed cHash if valid, an empty string if invalid
	 * @see tslib_fe->makeCacheHash
	 */
	protected function filterInvalidContentHash($cHash) {
		$urlParameters   = t3lib_div::_GET();
		$cHashParameters = t3lib_div::cHashParams(t3lib_div::implodeArrayForUrl('', $urlParameters));

		if (SOLR_COMPAT) {
			$calculatedCHash = t3lib_div::shortMD5(serialize($cHashParameters));
		} else {
			$calculatedCHash = t3lib_div::calculateCHash($cHashParameters);
		}

		return ($calculatedCHash == $cHash) ? $cHash : '';
	}

	/**
	 * Gets the current indexer mode.
	 *
	 * @return	string	Either tx_solr_IndexerSelector::INDEXER_STRATEGY_FRONTEND or tx_solr_IndexerSelector::INDEXER_STRATEGY_QUEUE
	 */
	public function getIndexerMode() {
		return $this->indexerMode;
	}

	/**
	 * Sets the indexer mode.
	 *
	 * @param	string	$indexerMode Either tx_solr_IndexerSelector::INDEXER_STRATEGY_FRONTEND or tx_solr_IndexerSelector::INDEXER_STRATEGY_QUEUE
	 */
	public function setIndexerMode($indexerMode) {
		if (!tx_solr_IndexerSelector::indexerStrategyExists($indexerMode)) {
			throw new InvalidArgumentException(
				'Invalid indexer mode.',
				1295368419
			);
		}

		$this->indexerMode = $indexerMode;
	}

	/**
	 * Gets the current page's URL.
	 *
	 * Depending on the current indexer mode, Frontend or IndexQueue different
	 * ways of retrieving the URL are chosen.
	 *
	 * @return	string	URL of the current page.
	 */
	public function getPageUrl() {
		return $this->pageUrl;
	}

	/**
	 * Sets the URL to use for the page document.
	 *
	 * @param	string	$url The page's URL.
	 */
	public function setPageUrl($url) {
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$this->pageUrl = $url;
		}
	}

	/**
	 * Gets the page's access rootline.
	 *
	 * @return	tx_solr_access_Rootline The page's access rootline
	 */
	public function getPageAccessRootline() {
		return $this->pageAccessRootline;
	}

	/**
	 * Sets the page's access rootline.
	 *
	 * @param	tx_solr_access_Rootline	$accessRootline The page's access rootline
	 */
	public function setPageAccessRootline(tx_solr_access_Rootline $accessRootline) {
		$this->pageAccessRootline = $accessRootline;
	}

	/**
	 * Gets the current page's Solr document ID.
	 *
	 * @return	string|NULL	The page's Solr document ID or NULL in case no document was generated yet.
	 */
	public static function getPageSolrDocumentId() {
		return self::$pageSolrDocumentId;
	}

	/**
	 * Gets the Solr document generated for the current page.
	 *
	 * @return	Apache_Solr_Document|NULL The page's Solr document or NULL if it has not been generated yet.
	 */
	public static function getPageSolrDocument() {
		return self::$pageSolrDocument;
	}

	/**
	 * Gets a comma separated list of frontend user groups to use for the
	 * document ID.
	 *
	 * @return	string	A comma separated list of frontend user groups.
	 */
	protected function getDocumentIdGroups() {
		$groups = $this->pageAccessRootline->getGroups();
		$groups = tx_solr_access_Rootline::cleanGroupArray($groups);

		if (empty($groups)) {
			$groups[] = 0;
		}

		$groups = implode(',', $groups);

		return $groups;
	}

	/**
	 * Helper method to utf8 encode (and trim) a string.
	 *
	 * @param	string	$string String to utf8 encode, can be utf8 already, won't be touched then.
	 * @return	string	utf8 encoded string.
	 */
	protected function utf8encode($string) {
		$utf8EncodedString = $this->page->csConvObj->utf8_encode(
			trim($string),
			$this->page->renderCharset
		);

		return $utf8EncodedString;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_typo3pageindexer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_typo3pageindexer.php']);
}

?>