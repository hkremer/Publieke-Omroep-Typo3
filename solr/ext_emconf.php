<?php

########################################################################
# Extension Manager/Repository config file for ext "solr".
#
# Auto generated 29-09-2011 09:21
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Apache Solr for TYPO3',
	'description' => 'Apache Solr for TYPO3 is the enterprise search engine you were looking for with special features such as Facetted Search or Synonym Support and incredibly fast response times of results within milliseconds.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.3.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod_admin',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Ingo Renner',
	'author_email' => 'ingo@typo3.org',
	'author_company' => 'd.k.d Internet Service GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:156:{s:9:"ChangeLog";s:4:"074b";s:16:"ext_autoload.php";s:4:"b921";s:12:"ext_icon.gif";s:4:"11e4";s:17:"ext_localconf.php";s:4:"892a";s:14:"ext_tables.php";s:4:"6833";s:13:"locallang.xml";s:4:"a3d6";s:16:"locallang_db.xml";s:4:"639c";s:25:"solr2search-dkd-de.rexpfd";s:4:"8755";s:49:"classes/class.tx_solr_additionalfieldsindexer.php";s:4:"efe2";s:41:"classes/class.tx_solr_commandresolver.php";s:4:"4fc3";s:43:"classes/class.tx_solr_connectionmanager.php";s:4:"9225";s:33:"classes/class.tx_solr_indexer.php";s:4:"26e8";s:41:"classes/class.tx_solr_indexerselector.php";s:4:"d68b";s:58:"classes/class.tx_solr_languagefileunavailableexception.php";s:4:"da9a";s:56:"classes/class.tx_solr_nosolrconnectionfoundexception.php";s:4:"8605";s:31:"classes/class.tx_solr_query.php";s:4:"1bed";s:32:"classes/class.tx_solr_search.php";s:4:"fe49";s:37:"classes/class.tx_solr_solrservice.php";s:4:"67c1";s:38:"classes/class.tx_solr_suggestquery.php";s:4:"2b80";s:34:"classes/class.tx_solr_template.php";s:4:"a054";s:51:"classes/class.tx_solr_typo3pagecontentextractor.php";s:4:"eb89";s:42:"classes/class.tx_solr_typo3pageindexer.php";s:4:"e762";s:30:"classes/class.tx_solr_util.php";s:4:"56cb";s:48:"classes/access/class.tx_solr_access_rootline.php";s:4:"8f56";s:55:"classes/access/class.tx_solr_access_rootlineelement.php";s:4:"a3fe";s:70:"classes/access/class.tx_solr_access_rootlineelementformatexception.php";s:4:"bf17";s:51:"classes/facet/class.tx_solr_facet_facetrenderer.php";s:4:"8191";s:57:"classes/facet/class.tx_solr_facet_simplefacetrenderer.php";s:4:"7537";s:55:"classes/facet/class.tx_solr_facet_usedfacetrenderer.php";s:4:"31ce";s:63:"classes/fieldprocessor/class.tx_solr_fieldprocessor_service.php";s:4:"d7d1";s:74:"classes/fieldprocessor/class.tx_solr_fieldprocessor_timestamptoisodate.php";s:4:"6e02";s:65:"classes/pluginbase/class.tx_solr_pluginbase_commandpluginbase.php";s:4:"06d2";s:58:"classes/pluginbase/class.tx_solr_pluginbase_pluginbase.php";s:4:"5d59";s:64:"classes/query/modifier/class.tx_solr_query_modifier_faceting.php";s:4:"7bf3";s:73:"classes/viewhelper/class.tx_solr_viewhelper_abstractsubpartviewhelper.php";s:4:"0b0f";s:52:"classes/viewhelper/class.tx_solr_viewhelper_crop.php";s:4:"d405";s:67:"classes/viewhelper/class.tx_solr_viewhelper_currentresultnumber.php";s:4:"e87d";s:52:"classes/viewhelper/class.tx_solr_viewhelper_date.php";s:4:"aa83";s:53:"classes/viewhelper/class.tx_solr_viewhelper_facet.php";s:4:"bffb";s:52:"classes/viewhelper/class.tx_solr_viewhelper_link.php";s:4:"4fd0";s:51:"classes/viewhelper/class.tx_solr_viewhelper_lll.php";s:4:"d25e";s:55:"classes/viewhelper/class.tx_solr_viewhelper_oddeven.php";s:4:"5611";s:57:"classes/viewhelper/class.tx_solr_viewhelper_relevance.php";s:4:"77e6";s:60:"classes/viewhelper/class.tx_solr_viewhelper_relevancebar.php";s:4:"6f7f";s:56:"classes/viewhelper/class.tx_solr_viewhelper_solrlink.php";s:4:"0222";s:61:"classes/viewhelper/class.tx_solr_viewhelper_sortindicator.php";s:4:"3d10";s:55:"classes/viewhelper/class.tx_solr_viewhelper_sorturl.php";s:4:"b277";s:50:"classes/viewhelper/class.tx_solr_viewhelper_ts.php";s:4:"a129";s:30:"compat/class.ux_tslib_cobj.php";s:4:"65d7";s:47:"compat/interface.tslib_content_postinithook.php";s:4:"d41c";s:52:"compat/interface.tslib_content_postinithook.php.orig";s:4:"e197";s:14:"doc/manual.sxw";s:4:"eb6d";s:23:"eid_suggest/suggest.php";s:4:"d592";s:24:"flexforms/pi_results.xml";s:4:"8140";s:29:"flexforms/pi_results.xml.orig";s:4:"ca36";s:50:"interfaces/interface.tx_solr_additionalindexer.php";s:4:"d54a";s:46:"interfaces/interface.tx_solr_facetrenderer.php";s:4:"f7c3";s:47:"interfaces/interface.tx_solr_facetsmodifier.php";s:4:"74f5";s:47:"interfaces/interface.tx_solr_fieldprocessor.php";s:4:"9b92";s:45:"interfaces/interface.tx_solr_formmodifier.php";s:4:"8c95";s:46:"interfaces/interface.tx_solr_plugincommand.php";s:4:"1336";s:50:"interfaces/interface.tx_solr_queryfilterparser.php";s:4:"b758";s:46:"interfaces/interface.tx_solr_querymodifier.php";s:4:"1959";s:50:"interfaces/interface.tx_solr_responseprocessor.php";s:4:"adb2";s:55:"interfaces/interface.tx_solr_resultdocumentmodifier.php";s:4:"f67c";s:50:"interfaces/interface.tx_solr_resultsetmodifier.php";s:4:"cd70";s:50:"interfaces/interface.tx_solr_subpartviewhelper.php";s:4:"2a17";s:54:"interfaces/interface.tx_solr_substitutepageindexer.php";s:4:"690d";s:49:"interfaces/interface.tx_solr_templatemodifier.php";s:4:"4604";s:43:"interfaces/interface.tx_solr_viewhelper.php";s:4:"1835";s:51:"interfaces/interface.tx_solr_viewhelperprovider.php";s:4:"ac30";s:18:"lang/locallang.xml";s:4:"6683";s:25:"lib/SolrPhpClient/COPYING";s:4:"7b1a";s:42:"lib/SolrPhpClient/Apache/Solr/Document.php";s:4:"c338";s:43:"lib/SolrPhpClient/Apache/Solr/Exception.php";s:4:"3b94";s:56:"lib/SolrPhpClient/Apache/Solr/HttpTransportException.php";s:4:"0876";s:58:"lib/SolrPhpClient/Apache/Solr/InvalidArgumentException.php";s:4:"0d44";s:61:"lib/SolrPhpClient/Apache/Solr/NoServiceAvailableException.php";s:4:"1f5f";s:49:"lib/SolrPhpClient/Apache/Solr/ParserException.php";s:4:"2d2e";s:42:"lib/SolrPhpClient/Apache/Solr/Response.php";s:4:"db7b";s:41:"lib/SolrPhpClient/Apache/Solr/Service.php";s:4:"54bb";s:50:"lib/SolrPhpClient/Apache/Solr/Service/Balancer.php";s:4:"aee6";s:55:"lib/SolrPhpClient/Apache/Solr/Service/Balancer.php.orig";s:4:"aee6";s:18:"mod_admin/conf.php";s:4:"de43";s:19:"mod_admin/index.php";s:4:"6f11";s:23:"mod_admin/locallang.xml";s:4:"99b7";s:28:"mod_admin/locallang.xml.orig";s:4:"61c6";s:24:"mod_admin/mod_admin.html";s:4:"bcea";s:29:"mod_admin/mod_admin.html.orig";s:4:"845b";s:24:"mod_admin/moduleicon.png";s:4:"a81f";s:39:"pi_results/class.tx_solr_pi_results.php";s:4:"9c39";s:59:"pi_results/class.tx_solr_pi_results_advancedformcommand.php";s:4:"dfec";s:55:"pi_results/class.tx_solr_pi_results_facetingcommand.php";s:4:"f68d";s:51:"pi_results/class.tx_solr_pi_results_formcommand.php";s:4:"29ed";s:56:"pi_results/class.tx_solr_pi_results_noresultscommand.php";s:4:"2451";s:54:"pi_results/class.tx_solr_pi_results_resultscommand.php";s:4:"f730";s:54:"pi_results/class.tx_solr_pi_results_sortingcommand.php";s:4:"2246";s:62:"pi_results/class.tx_solr_pi_results_spellcheckformmodifier.php";s:4:"a164";s:24:"pi_results/locallang.xml";s:4:"b1cd";s:37:"pi_search/class.tx_solr_pi_search.php";s:4:"f057";s:23:"pi_search/locallang.xml";s:4:"5abd";s:65:"report/class.tx_solr_report_accessfilterplugininstalledstatus.php";s:4:"d69d";s:43:"report/class.tx_solr_report_indexreport.php";s:4:"2566";s:44:"report/class.tx_solr_report_schemastatus.php";s:4:"bf4f";s:48:"report/class.tx_solr_report_solrconfigstatus.php";s:4:"9fd4";s:55:"report/class.tx_solr_report_solrconfigurationstatus.php";s:4:"4e36";s:42:"report/class.tx_solr_report_solrstatus.php";s:4:"ec1c";s:25:"report/tx_solr_report.gif";s:4:"11e4";s:44:"resources/css/jquery-ui/jquery-ui.custom.css";s:4:"35ca";s:30:"resources/css/report/index.css";s:4:"5af4";s:35:"resources/images/indicator-down.png";s:4:"309b";s:33:"resources/images/indicator-up.png";s:4:"1522";s:50:"resources/images/jquery-ui/ui-anim_basic_16x16.gif";s:4:"03ce";s:58:"resources/images/jquery-ui/ui-bg_glass_55_fcf0ba_1x400.png";s:4:"c15e";s:66:"resources/images/jquery-ui/ui-bg_gloss-wave_100_ece8da_500x100.png";s:4:"df1c";s:68:"resources/images/jquery-ui/ui-bg_highlight-hard_100_f7f7f7_1x100.png";s:4:"0165";s:68:"resources/images/jquery-ui/ui-bg_highlight-hard_100_fafaf4_1x100.png";s:4:"6923";s:67:"resources/images/jquery-ui/ui-bg_highlight-hard_15_aac402_1x100.png";s:4:"79ee";s:67:"resources/images/jquery-ui/ui-bg_highlight-hard_95_cccccc_1x100.png";s:4:"23c9";s:67:"resources/images/jquery-ui/ui-bg_highlight-soft_25_aac402_1x100.png";s:4:"52bc";s:67:"resources/images/jquery-ui/ui-bg_highlight-soft_95_ffedad_1x100.png";s:4:"54bf";s:63:"resources/images/jquery-ui/ui-bg_inset-soft_15_2b2922_1x100.png";s:4:"c364";s:54:"resources/images/jquery-ui/ui-icons_808080_256x240.png";s:4:"2857";s:54:"resources/images/jquery-ui/ui-icons_847e71_256x240.png";s:4:"d13c";s:54:"resources/images/jquery-ui/ui-icons_8dc262_256x240.png";s:4:"4128";s:54:"resources/images/jquery-ui/ui-icons_cd0a0a_256x240.png";s:4:"3e45";s:54:"resources/images/jquery-ui/ui-icons_eeeeee_256x240.png";s:4:"2e6d";s:54:"resources/images/jquery-ui/ui-icons_ffffff_256x240.png";s:4:"342b";s:36:"resources/javascript/jquery-1.4.2.js";s:4:"c0ac";s:40:"resources/javascript/jquery-1.4.2.min.js";s:4:"1009";s:50:"resources/javascript/jquery-ui-1.8.9.custom.min.js";s:4:"3155";s:43:"resources/javascript/eid_suggest/suggest.js";s:4:"7f08";s:42:"resources/javascript/pi_results/results.js";s:4:"4569";s:31:"resources/shell/install-solr.sh";s:4:"077f";s:51:"resources/solr/plugins/typo3-accessfilter-1.1.0.jar";s:4:"7b07";s:53:"resources/solr/singlecore/mapping-ISOLatin1Accent.txt";s:4:"9f3c";s:39:"resources/solr/singlecore/protwords.txt";s:4:"89d5";s:36:"resources/solr/singlecore/schema.xml";s:4:"ef3a";s:40:"resources/solr/singlecore/solrconfig.xml";s:4:"bbea";s:46:"resources/templates/pi_results/pagebrowser.htm";s:4:"7b82";s:42:"resources/templates/pi_results/results.css";s:4:"542a";s:42:"resources/templates/pi_results/results.htm";s:4:"5371";s:40:"resources/templates/pi_search/search.htm";s:4:"518d";s:27:"resources/tomcat/server.xml";s:4:"f6bc";s:25:"resources/tomcat/solr.xml";s:4:"8c2a";s:24:"resources/tomcat/tomcat6";s:4:"5bd2";s:48:"scheduler/class.tx_solr_scheduler_committask.php";s:4:"37a0";s:63:"scheduler/class.tx_solr_scheduler_committasksolrserverfield.php";s:4:"3313";s:50:"scheduler/class.tx_solr_scheduler_optimizetask.php";s:4:"c824";s:65:"scheduler/class.tx_solr_scheduler_optimizetasksolrserverfield.php";s:4:"493c";s:25:"static/solr/constants.txt";s:4:"e526";s:21:"static/solr/setup.txt";s:4:"5afa";s:45:"tests/classes/class.tx_solr_querytestcase.php";s:4:"c71c";s:65:"tests/classes/class.tx_solr_typo3pagecontentextractortestcase.php";s:4:"abc3";s:71:"tests/classes/facet/class.tx_solr_facet_simplefacetrenderertestcase.php";s:4:"8352";s:77:"tests/classes/fieldprocessor/class.tx_solr_fieldprocessor_servicetestcase.php";s:4:"e5de";}',
	'suggests' => array(
	),
);

?>