<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Ingo Renner <ingo@typo3.org>
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
 * A class to easily create a connection to a Solr server.
 *
 * Internally keeps track of already existing connections and makes sure that no
 * duplicate connections are created.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_ConnectionManager implements t3lib_Singleton {

		// TODO add parameterized singleton capabilities to t3lib_div::makeInstance()

	protected static $connections = array();

	/**
	 * Gets a Solr connection. Instead of generating a new connection with each
	 * call, connections are kept and checkt whether the requested connection
	 * already exists. If a connection already exists, it's reused.
	 *
	 * @param	string	Solr host (optional)
	 * @param	integer	Solr port (optional)
	 * @param	string	Solr path (optional)
	 * @param	string	Solr scheme, defaults to http, can be https (optional)
	 * @return	tx_solr_SolrService	A solr connection.
	 */
	public function getConnection($host = '', $port = '8080', $path = '/solr/', $scheme = 'http') {
		$connection = NULL;

		if (empty($host)) {
			t3lib_div::devLog(
				'tx_solr_ConnectionManager::getConnection() called with empty
				host parameter. Using configuration from TSFE, might be
				inaccurate. Always provide a host or use the getConnectionBy*
				methods.',
				'tx_solr',
				2
			);

			$solrConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['solr.'];

			$host   = $solrConfiguration['host'];
			$port   = $solrConfiguration['port'];
			$path   = $solrConfiguration['path'];
			$scheme = $solrConfiguration['scheme'];
		}

		$connectionHash = md5($scheme . '://' . $host . $port . $path);

		if (!isset(self::$connections[$connectionHash])) {
			$connection = t3lib_div::makeInstance(
				'tx_solr_SolrService',
				$host,
				$port,
				$path,
				$scheme
			);

			self::$connections[$connectionHash] = $connection;
		}

		return self::$connections[$connectionHash];
	}

	/**
	 * Gets a Solr connection for a page ID.
	 *
	 * @param	integer	A page ID.
	 * @param	integer	The language ID to get the connection for as the path may differ. Optional, defaults to 0.
	 * @param	string	$mount Comma list of MountPoint parameters
	 * @return	tx_solr_SolrService	A solr connection.
	 * @throws	tx_solr_NoSolrConnectionFoundException
	 */
	public function getConnectionByPageId($pageId, $language = 0, $mount = '') {
			// find the root page
		$pageSelect     = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine       = $pageSelect->getRootLine($pageId, $mount);
		$siteRootPageId = $this->getSiteRootPageIdFromRootLine($rootLine);

		try {
			$connection = $this->getConnectionByRootPageId($siteRootPageId, $language);
		} catch (tx_solr_NoSolrConnectionFoundException $nscfe) {
			throw t3lib_div::makeInstance(
				'tx_solr_NoSolrConnectionFoundException',
				$nscfe->getMessage() . ' Initial page used was [' . $pageId . ']',
				1275399922
			);
		}

		return $connection;
	}

	/**
	 * Gets a Solr connection for a root page ID.
	 *
	 * @param	integer	A root page ID.
	 * @param	integer	The language ID to get the connection for as the path may differ. Optional, defaults to 0.
	 * @return	tx_solr_SolrService	A solr connection.
	 * @throws	tx_solr_NoSolrConnectionFoundException
	 */
	public function getConnectionByRootPageId($pageId, $language = 0) {
		$solrConnection = NULL;
		$connectionKey  = $pageId . '|' . $language;

		$registry = t3lib_div::makeInstance('t3lib_Registry');
		$solrServers = $registry->get('tx_solr', 'servers');

		if (isset($solrServers[$connectionKey])) {
			$solrConnection = $this->getConnection(
				$solrServers[$connectionKey]['solrHost'],
				$solrServers[$connectionKey]['solrPort'],
				$solrServers[$connectionKey]['solrPath'],
				$solrServers[$connectionKey]['solrScheme']
			);
		} else {
			throw t3lib_div::makeInstance(
				'tx_solr_NoSolrConnectionFoundException',
				'Could not find a Solr connection for root page ['
					. $pageId . '] and language [' . $language . '].',
				1275396474
			);
		}

		return $solrConnection;
	}

	/**
	 * Gets all connections found.
	 *
	 * @return	array	An array of initialized Solr connections
	 */
	public function getAllConnections() {
		$connections = array();

		$registry = t3lib_div::makeInstance('t3lib_Registry');
		$solrServers = $registry->get('tx_solr', 'servers');

		foreach ($solrServers as $solrServer) {
			$connections[] = $this->getConnection(
				$solrServer['solrHost'],
				$solrServer['solrPort'],
				$solrServer['solrPath'],
				$solrServer['solrScheme']
			);
		}

		return $connections;
	}


	// updates


	/**
	 * Updates the connections in the registry when configuration cache is
	 * cleared.
	 *
	 * @param	array	An array of commands from TCEmain.
	 * @param	t3lib_TCEmain	Back reference to the TCEmain
	 */
	public function updateConnections(array $parameters, t3lib_TCEmain $tceMain) {
		$clearCacheCommand = $parameters['cacheCmd'];

		if ($clearCacheCommand == 'all' || $clearCacheCommand == 'temp_CACHED') {
			$solrConnections = $this->getConfiguredSolrConnections();
			$solrConnections = $this->filterDuplicateConnections($solrConnections);

			if (!empty($solrConnections)) {
				$registry = t3lib_div::makeInstance('t3lib_Registry');
				$registry->set('tx_solr', 'servers', $solrConnections);
			}
		}
	}

	/**
	 * Finds the configured Solr connections. Also respects multi-site
	 * environments.
	 *
	 * @return	array	An array with connections, each connection with keys rootPageTitle, rootPageUid, solrHost, solrPort, solrPath
	 */
	protected function getConfiguredSolrConnections() {
		$configuredSolrConnections = array();

			// find website roots and languages for this installation
		$rootPages = $this->getRootPages();
		$languages = $this->getSiteLanguages();

			// find solr configurations and add them as function menu entries
		foreach ($rootPages as $rootPage) {
			$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
			$rootLine   = $pageSelect->getRootLine($rootPage['uid']);

			foreach ($languages as $languageId) {
				t3lib_div::_GETset($languageId, 'L');
				$connectionKey = $rootPage['uid'] . '|' . $languageId;

				$tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext');
				$tmpl->tt_track = FALSE; // Do not log time-performance information
				$tmpl->init();
				$tmpl->runThroughTemplates($rootLine); // This generates the constants/config + hierarchy info for the template.
				$tmpl->generateConfig();

				list($solrSetup) = $tmpl->ext_getSetup($tmpl->setup, 'plugin.tx_solr.solr');
				list(, $solrEnabled) = $tmpl->ext_getSetup($tmpl->setup, 'plugin.tx_solr.enabled');
				$solrEnabled = !empty($solrEnabled) ? TRUE : FALSE;

				if (!empty($solrSetup) && $solrEnabled) {
					$configuredSolrConnections[$connectionKey] = array(
						'rootPageTitle' => $rootPage['title'],
						'rootPageUid'   => $rootPage['uid'],

						'solrScheme'    => $solrSetup['scheme'],
						'solrHost'      => $solrSetup['host'],
						'solrPort'      => $solrSetup['port'],
						'solrPath'      => $solrSetup['path'],

						'language'      => $languageId
					);
				}
			}
		}

		return $configuredSolrConnections;
	}

	/**
	 * Filters duplicate connections. When detecting the configured connections
	 * this is done with a little brute force by simply combining all root pages
	 * with all languages, this method filters out the duplicates.
	 *
	 * @param	array	An array of unfiltered connections, containing duplicates
	 * @return	array	An array with connections, no duplicates.
	 */
	protected function filterDuplicateConnections(array $connections) {
		$hashedConnections   = array();
		$filteredConnections = array();

			// array_unique() doesn't work on multi dimensional arrays, so we need flatter it first
		foreach ($connections as $key => $connection) {
			unset($connection['language']);
			$connectionHash = md5(implode('|', $connection));
			$hashedConnections[$key] = $connectionHash;
		}

		$hashedConnections = array_unique($hashedConnections);

		foreach ($hashedConnections as $key => $hash) {
			$filteredConnections[$key] = $connections[$key];
		}

		return $filteredConnections;
	}

	/**
	 * Finds the site's languages.
	 *
	 * @return	array	An array of language IDs
	 */
	protected function getSiteLanguages() {
		$languages = array(0);

		$languageRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid',
			'sys_language',
			'hidden = 0'
		);

		if (is_array($languageRecords)) {
			foreach ($languageRecords as $languageRecord) {
				$languages[] = $languageRecord['uid'];
			}
		}

		return $languages;
	}

	/**
	 * Gets the site's root pages. The "Is root of website" flag must be set,
	 * which usually is the case for pages with pid = 0.
	 *
	 * @return	array	An array of (partial) root page records, containing the uid and title fields
	 */
	protected function getRootPages() {
		$rootPages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title',
			'pages',
			'is_siteroot = 1 AND deleted = 0 AND hidden = 0 AND pid != -1'
		);

		return $rootPages;
	}

	/**
	 * Finds the page Id of the page marked as "Is site root" even if it's not
	 * on the root level (pid = 0).
	 *
	 * @param	array	A root line as generated by t3lib_pageSelect::getRootLine()
	 * @return	integer	The site root's page Id
	 */
	protected function getSiteRootPageIdFromRootLine(array $rootLine) {
		$siteRootPageId = 0;

		foreach ($rootLine as $page) {
			if ($page['is_siteroot']) {
				$siteRootPageId = $page['uid'];
				break;
			}
		}

		return $siteRootPageId;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_connectionmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_connectionmanager.php']);
}

?>