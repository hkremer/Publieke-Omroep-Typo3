<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2011 Ingo Renner <ingo@typo3.org>
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
 * A query specialized to get search suggestions
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_SuggestQuery extends tx_solr_Query {

	protected $configuration;
	protected $prefix;

	/**
	 * constructor for class tx_solr_SuggestQuery
	 */
	public function __construct($keywords) {
		parent::__construct('');

		$this->configuration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['suggest.'];

		$matches = array();
		preg_match('/^(:?(.* |))([^ ]+)$/', $keywords, $matches);
		$fullKeywords   = trim($matches[2]);
		$partialKeyword = trim($matches[3]);

		$this->setKeywords($fullKeywords);
		$this->prefix = $partialKeyword;
	}

	public function getQueryParameters() {
		$suggestParameters = array(
			'facet'          => 'on',
			'facet.prefix'   => $this->prefix,
			'facet.field'    => $this->configuration['suggestField'],
			'facet.limit'    => $this->configuration['numberOfSuggestions'],
			'facet.mincount' => '1',
			'fq'             => $this->filters,
			'fl'             => $this->configuration['suggestField']
		);

		return array_merge($suggestParameters, $this->queryParameters);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_suggestquery.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_suggestquery.php']);
}

?>