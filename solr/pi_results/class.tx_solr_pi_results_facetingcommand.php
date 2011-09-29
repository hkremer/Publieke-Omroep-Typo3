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
 * facets view command
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_pi_results_FacetingCommand implements tx_solr_PluginCommand {

	/**
	 * Search instance
	 *
	 * @var tx_solr_Search
	 */
	protected $search;

	/**
	 * Parent plugin
	 *
	 * @var	tx_solr_pi_results
	 */
	protected $parentPlugin;

	/**
	 * Configuration
	 *
	 * @var	array
	 */
	protected $configuration;

	/**
	 * Constructor for class tx_solr_pi_results_FacetingCommand
	 *
	 * @param	tslib_pibase	$parentPlugin parent plugin
	 */
	public function __construct(tslib_pibase $parentPlugin) {
		$this->search = t3lib_div::makeInstance('tx_solr_Search');

		$this->parentPlugin  = $parentPlugin;
		$this->configuration = $parentPlugin->conf;
	}

	public function execute() {
		$marker = array();

		if ($this->configuration['search.']['faceting'] && $this->search->getNumberOfResults()) {
			$marker['subpart_available_facets'] = $this->renderAvailableFacets();
			$marker['subpart_used_facets']      = $this->renderUsedFacets();

			$this->addFacetsJavascript();
		}

		if (count($marker) === 0) {
				// in case we didn't fill any markers - like when there are no
				// search results - we set markers to NULL to signal that we
				// want to have the subpart removed completely
			$marker = NULL;
		}

		return $marker;
	}

	protected function renderAvailableFacets() {
		$facetContent = '';

		$template = clone $this->parentPlugin->getTemplate();
		$template->workOnSubpart('available_facets');

		$configuredFacets = $this->configuration['search.']['faceting.']['facets.'];

		foreach ($configuredFacets as $facetName => $facetConfiguration) {

				// don't render facets that should not be included in available facets
			if (isset($facetConfiguration['includeInAvailableFacets']) && $facetConfiguration['includeInAvailableFacets'] == '0') {
				continue;
			}

			$facetName = substr($facetName, 0, -1);


			$facetRenderer = t3lib_div::makeInstance(
				'tx_solr_facet_FacetRenderer',
				$facetName,
				$template
			);
			$facetRenderer->setLinkTargetPageId($this->parentPlugin->getLinkTargetPageId());

			$facetContent .= $facetRenderer->renderFacet();
		}

		$template->addSubpart('single_facet', $facetContent);

		return $template->render();
	}

	protected function renderUsedFacets() {
		$template = clone $this->parentPlugin->getTemplate();
		$template->workOnSubpart('used_facets');
		$query = $this->search->getQuery();
		$query->setLinkTargetPageId($this->parentPlugin->getLinkTargetPageId());

		$resultParameters = t3lib_div::_GET('tx_solr');
		$filterParameters = array();
		if (isset($resultParameters['filter'])) {
			$filterParameters = (array) array_map('urldecode', $resultParameters['filter']);
		}

		$facetsInUse = array();
		foreach ($filterParameters as $filter) {
			list($filterName, $filterValue) = explode(':', $filter);

			$facetConfiguration = $this->configuration['search.']['faceting.']['facets.'][$filterName . '.'];

				// don't render facets that should not be included in used facets
			if (isset($facetConfiguration['includeInUsedFacets']) && $facetConfiguration['includeInUsedFacets'] == '0') {
				continue;
			}

			$usedFacetRenderer = t3lib_div::makeInstance(
				'tx_solr_facet_UsedFacetRenderer',
				$filterName,
				$filterValue,
				$filter ,
				$facetConfiguration,
				$this->parentPlugin->getTemplate(),
				$query
			);

			$facetToRemove = $usedFacetRenderer->render();

			$facetsInUse[] = $facetToRemove;
		}
		$template->addLoop('facets_in_use', 'remove_facet', $facetsInUse);

		$template->addVariable('remove_all_facets', array(
			'url'  => $query->getQueryUrl(array('filter' => array())),
			'text' => '###LLL:faceting_removeAllFilters###'
		));

		$content = '';
		if (count($facetsInUse)) {
			$content = $template->render();
		}

		return $content;
	}

	protected function addFacetsJavascript() {
		$jsFilePath = t3lib_extMgm::siteRelPath('solr') . 'resources/javascript/pi_results/results.js';

			// TODO make configurable once someone wants to use something other than jQuery
		$GLOBALS['TSFE']->additionalHeaderData[$this->parentPlugin->prefixId . '_faceting'] =
			'
			<script type="text/javascript">
			/*<![CDATA[*/

			var tx_solr_facetLabels = {
				\'showMore\' : \'' . $this->parentPlugin->pi_getLL('faceting_showMore') . '\',
				\'showFewer\' : \'' . $this->parentPlugin->pi_getLL('faceting_showFewer') . '\'
			};

			/*]]>*/
			</script>
			';

		if ($this->parentPlugin->conf['addDefaultJs']) {
			$GLOBALS['TSFE']->additionalHeaderData[$this->parentPlugin->prefixId . '_faceting'] .=
				'<script type="text/javascript" src="' . $jsFilePath . '"></script>';
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_facetingcommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_facetingcommand.php']);
}

?>