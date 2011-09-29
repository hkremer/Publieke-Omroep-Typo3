<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

	// TODO change to a constant, so that it can't get manipulated
$PATH_solr    = t3lib_extMgm::extPath('solr');
$PATHrel_solr = t3lib_extMgm::extRelPath('solr');

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

t3lib_div::loadTCA('tt_content');

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// adding the search plugin
t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:solr/locallang_db.xml:tt_content.list_type_pi_results',
		$_EXTKEY . '_pi_results'
	),
	'list_type'
);
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_results'] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi_results'] = 'pi_flexform';

	// add flexform to pi_results
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi_results', 'FILE:EXT:solr/flexforms/pi_results.xml');

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// adding the Search Form plugin
t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:solr/locallang_db.xml:tt_content.list_type_pi_search',
		$_EXTKEY . '_pi_search'
	),
	'list_type'
);
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi_search'] = 'layout,select_key,pages,recursive';

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/solr/', 'Apache Solr');

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

if (TYPO3_MODE == 'BE') {
#	t3lib_extMgm::addModulePath('tools_txsolrMAdmin', t3lib_extMgm::extPath($_EXTKEY) . 'mod_admin/');
	t3lib_extMgm::addModule('tools', 'txsolrMAdmin', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod_admin/');

		// registering reports
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['solr'] = array(
		'tx_solr_report_SchemaStatus',
		'tx_solr_report_SolrconfigStatus',
		'tx_solr_report_SolrConfigurationStatus',
		'tx_solr_report_SolrStatus',
		'tx_solr_report_AccessFilterPluginInstalledStatus'
	);

		// registering the index report with the reports module
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_solr']['index'] = array(
		'title'       => 'LLL:EXT:solr/locallang.xml:report_index_title',
		'description' => 'LLL:EXT:solr/locallang.xml:report_index_description',
		'report'      => 'tx_solr_report_IndexReport',
		'icon'        => 'EXT:solr/report/tx_solr_report.gif'
	);

		// hooking into cache clearing to update detected configuration
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'EXT:solr/classes/class.tx_solr_connectionmanager.php:tx_solr_ConnectionManager->updateConnections';

}

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// replace the built-in search content element
t3lib_extMgm::addPiFlexFormValue(
	'*',
	'FILE:EXT:' . $_EXTKEY . '/flexforms/pi_results.xml',
	'search'
);

if(t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
	$TCA['tt_content']['types']['search']['showitem'] =
		'--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.plugin,
			pi_flexform;;;;1-1-1,
		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
			--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
			--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
			--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.behaviour,
		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';
} else {
	$TCA['tt_content']['types']['search']['showitem'] =
		'CType;;4;;1-1-1, hidden, header;;3;;2-2-2, linkToTop;;;;3-3-3,
		--div--;LLL:EXT:cms/locallang_ttc.xml:CType.I.9, pi_flexform;;;;1-1-1,
		--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime, fe_group';
}

?>