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
 * Additional fields indexer.
 *
 * Adds page document fields as configured in
 * plugin.tx_solr.index.additionalFields.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_AdditionalFieldsIndexer implements tx_solr_SubstitutePageIndexer {

	/**
	 * Returns a substitute document for the currently being indexed page.
	 *
	 * Uses the original document and adds fields as defined in
	 * plugin.tx_solr.index.additionalFields.
	 *
	 * @param	Apache_Solr_Document	The original page document.
	 * @return	Apache_Solr_Document	A Apache_Solr_Document object that replace the default page document
	 */
	public function getPageDocument(Apache_Solr_Document $pageDocument) {
		$substitutePageDocument = clone $pageDocument;
		$additionaFields = $this->getAdditionalFields();

		foreach ($additionaFields as $fieldName => $fieldValue) {
			if (!isset($pageDocument->{$fieldName})) {
					// making sure we only _add_ new fields
				$substitutePageDocument->setField($fieldName, $fieldValue);
			}
		}

		return $substitutePageDocument;
	}

	/**
	 * Gets the additional fields as an array mapping field names to values.
	 *
	 * @return	array	An array mapping additional field names to their values.
	 */
	protected function getAdditionalFields() {
		$additionalFields     = array();
		$additionalFieldNames = $this->getAdditionalFieldNames();

		foreach ($additionalFieldNames as $additionalFieldName) {
			$additionalFields[$additionalFieldName] = $this->getFieldValue($additionalFieldName);
		}

		return $additionalFields;
	}

	/**
	 * Gets a list of fields to index in addition to the default fields.
	 *
	 * @return	array	An array of additionally configured field names.
	 */
	protected function getAdditionalFieldNames() {
		$additionalFieldNames = array();
		$additionalFields = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['index.']['additionalFields.'];

		if (is_array($additionalFields)) {
			foreach ($additionalFields as $fieldName => $fieldValue) {
				if (is_array($fieldValue)) {
						// if its just the configuration array skip this field
					continue;
				}

				$additionalFieldNames[] = $fieldName;
			}
		}

		return $additionalFieldNames;
	}

	/**
	 * Uses the page's cObj instance to resolve the additional field's value.
	 *
	 * @param	string	$fieldName The name of the field to get.
	 * @return	string	The field's value.
	 */
	protected function getFieldValue($fieldName) {
		$fieldValue       = '';
		$additionalFields = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr.']['index.']['additionalFields.'];

			// support for cObject if the value is a configuration
		if (is_array($additionalFields[$fieldName . '.'])) {
			$fieldValue = $GLOBALS['TSFE']->cObj->cObjGetSingle(
				$additionalFields[$fieldName],
				$additionalFields[$fieldName . '.']
			);
		} else {
			$fieldValue = $additionalFields[$fieldName];
		}

		return $fieldValue;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_additionalfieldsindexer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/class.tx_solr_additionalfieldsindexer.php']);
}

?>