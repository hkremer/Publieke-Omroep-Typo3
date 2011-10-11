<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_eonieuwsbrief_nieuwsbrief"] = array (
	"ctrl" => $TCA["tx_eonieuwsbrief_nieuwsbrief"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,name,midrid,campaign"
	),
	"feInterface" => $TCA["tx_eonieuwsbrief_nieuwsbrief"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_eonieuwsbrief_nieuwsbrief',
				'foreign_table_where' => 'AND tx_eonieuwsbrief_nieuwsbrief.pid=###CURRENT_PID### AND tx_eonieuwsbrief_nieuwsbrief.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:eo_nieuwsbrief/locallang_db.xml:tx_eonieuwsbrief_nieuwsbrief.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"midrid" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:eo_nieuwsbrief/locallang_db.xml:tx_eonieuwsbrief_nieuwsbrief.midrid",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
            "campaign" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:eo_nieuwsbrief/locallang_db.xml:tx_eonieuwsbrief_nieuwsbrief.campaign",		
			"config" => Array (
				"type" => "select",	
				'items' => array (
					array('testTCA', 0),
				),
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, name, midrid, campaign")
	),
	"palettes" => array (
		"1" => array("showitem" => "starttime, endtime, fe_group")
	)
);
?>
