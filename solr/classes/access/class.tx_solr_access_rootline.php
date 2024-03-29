<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Ingo Renner <ingo@typo3.org>
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
 * "Access Rootline", represents all pages and specifically those setting
 * frontend user group access restrictions in a page's rootline.
 *
 * The access rootline only contains pages which set frontend user access
 * restrictions and extend them to sub-pages. The format is as follows:
 *
 * pageId1:group1,group2/pageId2:group3/c:group1,group4,groupN
 *
 * The single elements of the access rootline are separated by a slash
 * character. All but the last elements represent pages, the last element
 * defines the access restrictions applied to the page's content elements
 * and records shown on the page.
 * Each page element is composed by the page ID of the page setting frontend
 * user access restrictions, a colon, and a comma separated list of frontend
 * user group IDs restricting access to the page.
 * The content access element does not have a page ID, instead it replaces
 * the ID by a lower case C.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_access_Rootline {

	/**
	 * Delimiter for page and content access right elements in the rootline.
	 *
	 * @var	string
	 */
	const ELEMENT_DELIMITER = '/';

	/**
	 * Storage for access rootline elements
	 *
	 * @var	array
	 */
	protected $rootlineElements = array();

	/**
	 * Constructor, turns a string representation of an access rootline into an
	 * object representation.
	 *
	 * @param	string	$accessRootline Access Rootline String representation.
	 */
	public function __construct($accessRootline = NULL) {
		if (!is_null($accessRootline)) {
			$rawRootlineElements = explode(self::ELEMENT_DELIMITER, $accessRootline);

			foreach ($rawRootlineElements as $rawRootlineElement) {
				try {
					$this->push(t3lib_div::makeInstance(
						'tx_solr_access_RootlineElement',
						$rawRootlineElement
					));
				} catch (tx_solr_access_RootlineElementFormatException $e) {
					// just ignore the faulty element for now, might log this later
				}
			}
		}
	}

	/**
	 * Returns the string representation of the access rootline.
	 *
	 * @return	string	String representation of the access rootline.
	 */
	public function __toString() {
		$stringElements = array();

		foreach ($this->rootlineElements as $rootlineElement) {
			$stringElements[] = (string) $rootlineElement;
		}

		return implode(self::ELEMENT_DELIMITER, $stringElements);
	}

	/**
	 * Adds an Access Rootline Element to the end of the rootline.
	 *
	 * @param	tx_solr_access_RootlineElement	$rootlineElement Element to add.
	 */
	public function push(tx_solr_access_RootlineElement $rootlineElement) {
		$lastElementIndex = max(0, (count($this->rootlineElements) - 1));

		if (!empty($this->rootlineElements[$lastElementIndex])
			&& $this->rootlineElements[$lastElementIndex]->getType() == tx_solr_access_RootlineElement::ELEMENT_TYPE_CONTENT
		) {
			throw new RuntimeException(
				'Can not add an element to an Access Rootline whose\' last element is a content type element.',
				1294422132
			);
		}

		$this->rootlineElements[] = $rootlineElement;
	}

	/**
	 * Gets a the groups in the Access Rootline.
	 *
	 * @return	array	An array of sorted, unique user group IDs required to access a page.
	 */
	public function getGroups() {
		$groups = array();

		foreach ($this->rootlineElements as $rootlineElement) {
			$rootlineElementGroups = $rootlineElement->getGroups();
			$groups = array_merge($groups, $rootlineElementGroups);
		}

		$groups = $this->cleanGroupArray($groups);

		return $groups;
	}

	/**
	 * Gets the Access Rootline for a specific page Id.
	 *
	 * @param	integer	$pageId The page Id to generate the Access Rootline for.
	 * @return	tx_solr_access_Rootline	Access Rootline for the given page Id.
	 */
	public static function getAccessRootlineByPageId($pageId) {
		$accessRootline = t3lib_div::makeInstance('tx_solr_access_Rootline');

		$pageSelector = t3lib_div::makeInstance('t3lib_pageSelect');
		$pageSelector->init(FALSE);
		$rootline = $pageSelector->getRootLine($pageId);
		$rootline = array_reverse($rootline);

			// parent pages
		foreach ($rootline as $pageRecord) {
			if ($pageRecord['fe_group']
				&& $pageRecord['extendToSubpages']
				&& $pageRecord['uid'] != $pageId
			) {
				$accessRootline->push(t3lib_div::makeInstance(
					'tx_solr_access_RootlineElement',
					$pageRecord['uid'] . tx_solr_access_RootlineElement::PAGE_ID_GROUP_DELIMITER . $pageRecord['fe_group']
				));
			}
		}

			// current page
		$currentPageRecord = $pageSelector->getPage($pageId);
		if ($currentPageRecord['fe_group']) {
			$accessRootline->push(t3lib_div::makeInstance(
				'tx_solr_access_RootlineElement',
				$currentPageRecord['uid'] . tx_solr_access_RootlineElement::PAGE_ID_GROUP_DELIMITER . $currentPageRecord['fe_group']
			));
		}

		return $accessRootline;
	}

	/**
	 * Cleans an array of frontend user group IDs. Removes duplicates and sorts
	 * the array.
	 *
	 * @param	array	An array of frontend user group IDs
	 * @return	array	An array of cleaned frontend user group IDs, unique, sorted.
	 */
	public static function cleanGroupArray(array $groups) {
		$groups = array_unique($groups); // removes duplicates
		sort($groups, SORT_NUMERIC);     // sort

		return $groups;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/access/class.tx_solr_access_rootline.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/access/class.tx_solr_access_rootline.php']);
}

?>