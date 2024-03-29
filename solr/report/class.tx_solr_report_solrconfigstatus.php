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
 * Provides a status report about which solrconfig version is used and checks
 * whether it fits the recommended version shipping with the extension.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	solr
 */
class tx_solr_report_SolrconfigStatus implements tx_reports_StatusProvider {

	/**
	 * The solrconfig name property is constructed as follows:
	 *
	 * tx_solr	- The extension key
	 * x-y-z	- The extension version this schema is meant to work with
	 * YYYYMMDD	- The date the schema file was changed the last time
	 *
	 * Must be updated when changing the solrconfig.
	 *
	 * @var	string
	 */
	const RECOMMENDED_SOLRCONFIG_VERSION = 'tx_solr-1-5-0--20101129';

	/**
	 * Compiles a collection of solrconfig version checks against each configured
	 * Solr server. Only adds an entry if a solrconfig other than the
	 * recommended one was found.
	 *
	 * @see typo3/sysext/reports/interfaces/tx_reports_StatusProvider::getStatus()
	 */
	public function getStatus() {
		$reports = array();
		$solrConnections = t3lib_div::makeInstance('tx_solr_ConnectionManager')->getAllConnections();

		foreach ($solrConnections as $solrConnection) {

			if ($solrConnection->ping()
				&& $solrConnection->getSolrconfigName() != self::RECOMMENDED_SOLRCONFIG_VERSION
			) {
				$solrconfigName = $solrConnection->getSolrconfigName();
				if (empty($solrconfigName)) {
					$solrconfigName = '&lt;not set&gt;';
				}

				$message = '<p style="margin-bottom: 10px;">A solrconfig.xml
					different from the one provided with the extension was
					detected.</p>
					<p style="margin-bottom: 10px;">It is recommended to use the
					solrconfig.xml file shipping with the Apache Solr for TYPO3
					extension as it provides an optimized setup for the  use of
					Solr with TYPO3. A difference can occur when you  update the
					TYPO3 extension, but forget to update the solrconfig.xml
					file on the Solr server. The Solr configuration sometimes
					changes to accommodate changes or new features in Apache
					Solr. Also make sure to restart the Tomcat server after
					updating solrconfig.xml.</p>
					<p style="margin-bottom: 10px;">Your Solr server is
					currently using a Solr configuration file named <strong>'
					. $solrconfigName . '</strong>, the
					recommended schema is called <strong>'
					. self::RECOMMENDED_SOLRCONFIG_VERSION . '</strong>. You can
					find the recommended solrconfig.xml file in the extension\'s
					resources folder: EXT:solr/resources/solr/solrconfig.xml.
					While you\'re at it, please check whether you\'re using the
					current schema.xml file, too.</p>';

				$message .= '<p>Affected Solr server:</p>
					<ul>'
					. '<li>Host: ' . $solrConnection->getHost() . '</li>'
					. '<li>Port: ' . $solrConnection->getPort() . '</li>'
					. '<li>Path: ' . $solrConnection->getPath() . '</li>
					</ul>';

				$status = t3lib_div::makeInstance('tx_reports_reports_status_Status',
					'Solrconfig Version',
					'Unsupported solrconfig.xml',
					$message,
					tx_reports_reports_status_Status::WARNING
				);

				$reports[] = $status;
			}
		}

		return $reports;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/report/class.tx_solr_report_solrconfigstatus.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/report/class.tx_solr_report_solrconfigstatus.php']);
}

?>