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
 * Tests the TYPO3 page content extractor
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_Typo3PageContentExtractorTestCase extends tx_phpunit_testcase {

	/**
	 * @test
	 */
	public function changesNbspToSpace() {
		$content = '<!-- TYPO3SEARCH_begin -->In Olten&nbsp;ist<!-- TYPO3SEARCH_end -->';
		$expectedResult = 'In Olten ist';

		$contentExtractor = t3lib_div::makeInstance(
			'tx_solr_Typo3PageContentExtractor',
			$content
		);
		$actualResult = $contentExtractor->getIndexableContent();

		$this->assertEquals($expectedResult, $actualResult);
	}

}

?>