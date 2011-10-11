<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_eonieuwsbrief_addFieldsToFlexForm.php');

t3lib_extMgm::allowTableOnStandardPages('tx_eonieuwsbrief_nieuwsbrief');

t3lib_extMgm::addToInsertRecords('tx_eonieuwsbrief_nieuwsbrief');

$TCA["tx_eonieuwsbrief_nieuwsbrief"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:eo_nieuwsbrief/locallang_db.xml:tx_eonieuwsbrief_nieuwsbrief',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, name, midrid",
	)
);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1','FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

t3lib_extMgm::addPlugin(array('LLL:EXT:eo_nieuwsbrief/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","EO Nieuwsbrief");

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_eonieuwsbrief_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_eonieuwsbrief_pi1_wizicon.php';
?>
