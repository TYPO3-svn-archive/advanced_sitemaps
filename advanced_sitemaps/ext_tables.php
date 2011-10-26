<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_advancedsitemaps_configurations');

$TCA['tx_advancedsitemaps_configurations'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations',		
		'label' => 'title',
        'label_alt' => 'tablename',
		'label_userFunc' => 'tx_advancedsitemaps_tca->labelFunc',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField' => 'sys_language_uid',	
		'transOrigPointerField' => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'sortby' => 'sorting',	
		'delete' => 'deleted',
		'dividers2tabs' => TRUE,
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'requestUpdate' => 'tablename',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_advancedsitemaps_configurations.gif',
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_div::loadTCA('tx_advancedsitemaps_configurations');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:advanced_sitemaps/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/','Advanced Sitemaps');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/ds.tx_advancedsitemaps_pi1.xml','list');

/**
 * Add additional columns to the TCA for pages. Since the option for this table
 * is always allowed and enabled.
 */
$a_additionalColumns = array(
    'tx_advancedsitemaps_priority' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.priority',        
        'config' => array (
    		'type' => 'select',
			'items' => array(
				array('','0'),
				array('0.1','0.1'),
				array('0.2','0.2'),
				array('0.3','0.3'),
				array('0.4','0.4'),
				array('0.5','0.5'),
				array('0.6','0.6'),
				array('0.7','0.7'),
				array('0.8','0.8'),
				array('0.9','0.9'),
				array('1.0','1.0')
			),
		),
	),
	'tx_advancedsitemaps_changeFreq' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq',        
        'config' => array (
    		'type' => 'select',
			'items' => array(
				array('',''),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.always','always'),
    			array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.hourly','hourly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.daily','daily'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.weekly','weekly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.monthly','monthly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.yearly','yearly'),
				array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.never','never'),
			),
			'selected' => 'never'
		),
	),
);
t3lib_extMgm::addTCAcolumns('pages',$a_additionalColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tabs.googleSitemaps,tx_advancedsitemaps_priority,tx_advancedsitemaps_changeFreq');
?>