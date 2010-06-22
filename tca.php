<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_advancedsitemaps_configurations'] = array (
	'ctrl' => $TCA['tx_advancedsitemaps_configurations']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,tablename,parent,url_params,sortby,record_limit,single_page,startingpoint,recursive'
	),
	'feInterface' => $TCA['tx_advancedsitemaps_configurations']['feInterface'],
	'columns' => array (
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
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_advancedsitemaps_configurations',
				'foreign_table_where' => 'AND tx_advancedsitemaps_configurations.pid=###CURRENT_PID### AND tx_advancedsitemaps_configurations.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
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
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
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
		'tablename' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.tablename',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('', 0),
				),
				'itemsProcFunc' => 'tx_advancedsitemaps_tca->listTables',
				'eval' => 'required',
			)
		),
		'parent' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.parent',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'url_params' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.url_params',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'sortby' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby',
			'displayCond' => 'FIELD:tablename:!=:', 		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('', 0),
				),
				'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
			)
		),
		'sortby_dir' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir',
			'displayCond' => 'FIELD:sortby:!=:',		
			'config' => array (
				'type' => 'select',
				'items' => array(
					array('',''),
					array('LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir.ASC','DESC'),
					array('LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir.DESC','ASC'),
				),
			)
		),
		'record_limit' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.record_limit',		
			'config' => array (
				'type'     => 'input',
				'size'     => '4',
				'max'      => '4',
				'eval'     => 'int',
				'checkbox' => '0',
				'range'    => array (
					'upper' => '1000',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'single_page' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.single_page',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'pages',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'title_field' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.title_field',
			'displayCond' => 'FIELD:tablename:!=:', 		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('', 0),
				),
				'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
			)
		),
		'startingpoint' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.startingpoint',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'pages',	
				'size' => 4,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'recursive' => array (		
			'exclude' => 1,                                                                                                                                                     
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.recursive',                                                                                                      
			'config' => array (                                                                                                                                                 
				'type' => 'select',                                                                                                                                         
				'items' => Array (                                                                                                                                          
					array('', '0'),                                                                                                                                     
					array('LLL:EXT:cms/locallang_ttc.php:recursive.I.1', '1'),                                                                                          
					array('LLL:EXT:cms/locallang_ttc.php:recursive.I.2', '2'),                                                                                          
					array('LLL:EXT:cms/locallang_ttc.php:recursive.I.3', '3'),                                                                                          
					array('LLL:EXT:cms/locallang_ttc.php:recursive.I.4', '4'),                                                                                          
					array('LLL:EXT:cms/locallang_ttc.php:recursive.I.5', '250')                                                                                         
				),                                                                                                                                                          
				'default' => '0'
			),   
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, tablename;;2, startingpoint, recursive, parent,--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.link_configuration,title_field, single_page, url_params')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group'),
		'2' => array('showitem' => 'sortby, sortby_dir, record_limit'),
	)
);

/**
 * TCA handler containts functions to dynamically fill TCA fields and labels
 * 
 * @author Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @url SebastiaanDeJonge.com
 * @package TYPO3
 * @subpackage tx_advancedsitemaps
 */
class tx_advancedsitemaps_tca {
	/**
	 * Renders the label of the records inside the record listing
	 * 
	 * @access public
	 * @static
	 * @param array $a_parameters
	 * @return void
	 */
	public static function labelFunc(array &$a_parameters) {
		if($a_parameters['row']['tablename']) {
			$a_parameters['title'] = $GLOBALS['LANG']->sL($GLOBALS['TCA'][$a_parameters['row']['tablename']]['ctrl']['title']).' ('.$a_parameters['row']['tablename'].')';
		} else {
			$a_parameters['title'] = $GLOBALS['LANG']->sL('LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.noTableSelected');
		}
		
	}
	
	/**
	 * Lists the available tables inside this TYPO3 installations database
	 * 
	 * @access public
	 * @static
	 * @param array $a_parameters
	 * @return void
	 */
	public static function listTables(array &$a_parameters) {
		$a_additionalItems = array();
		$a_tables = array_keys($GLOBALS['TYPO3_DB']->admin_get_tables());
		foreach ($a_tables as $s_tablename) {
			if(
				$s_tablename != 'tt_content' && 
				preg_match('/^(tt|tx|user)_/', $s_tablename) &&
				isset($GLOBALS['TCA'][$s_tablename]) &&
				!$GLOBALS['TCA'][$name]['hideTable'] &&
				strpos($s_tablename, 'tx_mnogosearch_') === false &&
				strpos($s_tablename, 'tx_advancedsitemaps_') === false
			){
				$s_title = $GLOBALS['LANG']->sL($GLOBALS['TCA'][$s_tablename]['ctrl']['title']);
				if ($s_title) {
					$a_additionalItems[$s_title] = array($s_title, $s_tablename, $GLOBALS['TCA'][$s_tablename]['ctrl']['iconfile']);
				}
			}
		}
		$a_parameters['items'] = array_merge($a_parameters['items'],$a_additionalItems);
		sort($a_parameters['items']);
	}
	
	/**
	 * Lists the fields inside the selected table
	 * 
	 * @access public
	 * @static
	 * @param array $a_parameters
	 * @return void
	 */
	public static function listFields(array &$a_parameters) {
		$s_tableName = $a_parameters['row']['tablename'] ? $a_parameters['row']['tablename'] : 'pages';
		
		if($s_tableName != '') {
			$a_additionalFields = array();
			$a_fields = array_keys($GLOBALS['TYPO3_DB']->admin_get_fields($s_tableName));
			t3lib_div::loadTCA($a_parameters['row']['tablename']);
			
			foreach ($a_fields as $s_field) {
				if(isset($GLOBALS['TCA'][$s_tableName]['columns'][$s_field])){
					$s_title = trim($GLOBALS['LANG']->sL($GLOBALS['TCA'][$s_tableName]['columns'][$s_field]['label']), ':');
					$a_additionalFields[$s_title] = array($s_title, $s_field);
				}
			}
			
			$a_parameters['items'] = array_merge($a_parameters['items'],$a_additionalFields);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/tca.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/tca.php']);
}
?>