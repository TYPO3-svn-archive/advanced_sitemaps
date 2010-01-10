<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array (
	'tx_advancedsitemaps_update_frequency' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency',		
		'config' => array (
			'type' => 'select',
			'items' => array (
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency.I.0', ''),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency.I.1', 'hourly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency.I.2', 'daily'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency.I.3', 'weekly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_update_frequency.I.4', 'monthly'),
			),
			'size' => 1,	
			'maxitems' => 1,
		)
	),
	'tx_advancedsitemaps_priority' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:pages.tx_advancedsitemaps_priority',		
		'config' => array (
			'type' => 'input',	
			'size' => '10',	
			'max' => '4',
		)
	),
);


t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_advancedsitemaps_update_frequency;;;;1-1-1, tx_advancedsitemaps_priority');


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advanced_sitemaps/locallang_db.xml:tt_content.menu_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'menu_type');
?>