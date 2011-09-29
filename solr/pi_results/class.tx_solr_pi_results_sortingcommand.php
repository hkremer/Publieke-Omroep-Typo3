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
 * sorting view command
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_pi_results_SortingCommand implements tx_solr_PluginCommand {

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
	 * Constructor for class tx_solr_pi_results_SortingCommand
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

		if ($this->configuration['search.']['sorting'] != 0 && $this->search->getNumberOfResults()) {
			$marker['loop_sort|sort'] = $this->getSortingLinks();
		}

		if (count($marker) === 0) {
				// in case we didn't fill any markers - like when there are no
				// search results - we set markers to NULL to signal that we
				// want to have the subpart removed completely
			$marker = NULL;
		}

		return $marker;
	}

	protected function getSortingLinks() {
		$configuredSortingFields = $this->configuration['search.']['sorting.']['fields.'];
		$query = $this->search->getQuery();
		$query->setLinkTargetPageId($this->parentPlugin->getLinkTargetPageId());
		$sortingFields = array();

		$urlParameters = t3lib_div::_GP('tx_solr');
		$urlSortingParameter = $urlParameters['sort'];
		list($currentSortByField, $currentSortDirection) = explode(' ', $urlSortingParameter);

		foreach ($configuredSortingFields as $fieldName => $enabled) {
			if (substr($fieldName, -1) != '.' && $enabled) {

				$sortDirection = $this->configuration['search.']['sorting.']['defaultOrder'];
				$sortIndicator = $sortDirection;
				$sortParameter = $fieldName . ' ' . $sortDirection;

					// toggle sorting direction for the current sorting field
				if ($currentSortByField == $fieldName) {
					switch ($currentSortDirection) {
						case 'asc':
							$sortDirection = 'desc';
							$sortIndicator = 'asc';
							break;
						case 'desc':
							$sortDirection = 'asc';
							$sortIndicator = 'desc';
							break;
					}

					$sortParameter = $fieldName . ' ' . $sortDirection;
				}

				$temp = array(
					'link'       => $query->getQueryLink(
						'###LLL:' . $configuredSortingFields[$fieldName . '.']['label'] . '###',
						array('sort' => $sortParameter)
					),
					'url'        =>  $query->getQueryUrl(
						array('sort' => $sortParameter)
					),
					'field'      => $fieldName,
					'label'      => '###LLL:' . $configuredSortingFields[$fieldName . '.']['label'] . '###',
					'is_current' => $currentSortByField == $fieldName ? '1' : '0',
					'direction'  => $sortDirection,
					'indicator'  => $sortIndicator,
					'current_direction' => ' '
				);

					// set sort indicator for the current sorting field
				if ($currentSortByField == $fieldName) {
					$temp['selected']          = 'selected="selected"';
					$temp['current']           = 'current';
					$temp['current_direction'] = $sortIndicator;
				}

					// special case relevancy: just reset the search to normal behavior
				if ($fieldName == 'relevancy') {
					$temp['link'] = $query->getQueryLink(
						'###LLL:' . $configuredSortingFields[$fieldName . '.']['label'] . '###',
						array('sort' => NULL)
					);
					unset($temp['direction'], $temp['indicator']);
				}

				$sortingFields[] = $temp;
			}
		}

		return $sortingFields;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_sortingcommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_sortingcommand.php']);
}

?>