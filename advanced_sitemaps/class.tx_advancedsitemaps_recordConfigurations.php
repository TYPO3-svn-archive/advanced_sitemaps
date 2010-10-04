<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Sebastiaan de Jonge <szebi.eger@gmail.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


/**
 * Record Configuration class, handles configuration for records by
 * adding non-existent columns to the TCA and process them on save to
 * store custom configurations per record.
 *
 * @author Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @author Benjamin Serfhos <serfhos@redkiwi.nl>
 * @package TYPO3
 * @subpackage tx_advancedsitemaps
 * @implements t3lib_Singleton
 */
class tx_advancedsitemaps_recordConfigurations implements t3lib_Singleton {
	
	/**
	 * Checks if a table is allowed to have custom record configurations
	 * 
	 * @param string $s_tableName
	 * @return bool
	 */
	private function isAllowedTable($s_tableName) {
		if($s_tableName == 'pages') return false;
		$a_extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['advanced_sitemaps']);
		$a_allowedTables = t3lib_div::trimExplode(',',$a_extConf['allowedTables'],true);
		return in_array($s_tableName,$a_allowedTables);
	}
	
	/**
	 * Checks if the configuration for a record exists
	 * 
	 * @param string $s_tableName
	 * @param int $i_recordUid
	 * @param string $s_type
	 * @return mixed FALSE if the configuration doesn't exist, otherwise the configuration value is returned
	 * 
	 * @TODO Optimize so only a single SQL query is used for grabbing all data for a single record
	 */
	private function checkRecordConfigurationValue($s_tableName, $i_recordUid, $s_type) {
		$a_records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_advancedsitemaps_configurations_records',
			"table_name = '$s_tableName' AND record_uid = $i_recordUid"
		);
		
		$i_recordCount = count($a_records);
		if($i_recordCount === 0) return false;
		return $a_records[0]['value'];
	}
	
	/**
	 * Updates an existing record configuration value
	 * 
	 * @param string $s_tableName
	 * @param int $i_recordUid
	 * @param string $s_type
	 * @param string $s_value
	 * @return void
	 */
	private function updateRecordConfigurationValue($s_tableName,$i_recordUid,$s_type,$s_value) {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_advancedsitemaps_configurations_records',
			"table_name = '$s_tableName' AND record_uid = $i_recordUid AND `type`='$s_type'",
			array(
				'value' => $s_value
			)
		);
	}
	
	/**
	 * Inserts a new record configuration value
	 * 
	 * @param string $s_tableName
	 * @param int $i_recordUid
	 * @param string $s_type
	 * @param string $s_value
	 * @return void
	 */
	private function insertRecordConfigurationValue($s_tableName,$i_recordUid,$s_type,$s_value) {
		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			'tx_advancedsitemaps_configurations_records',
			array(
				'table_name' => $s_tableName,
				'record_uid' => $i_recordUid,
				'type' => $s_type,
				'value' => $s_value
			)
		);
	}
	
	/**
	 * Manipulates the TCA of all tables (specified in extension 
	 * configuration), and adds additional Google Sitemaps fields: 
	 * importance, update rate
	 * 
	 * @param string $s_table The name of the table
	 * @param array $a_row The actual data of the record that is edited (if there is any)
	 * @param object $o_parent An instance of t3lib_tceforms
	 * @return void
	 */
	public function getMainFields_preProcess($s_table, &$a_row, $o_parent) {
		if(!$this->isAllowedTable($s_table)) return;
		
		/**
		 * Now we know that everything is OK, we will create some fake fields and
		 * add them to the TCA
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
		t3lib_extMgm::addTCAcolumns($s_table,$a_additionalColumns,1);
		t3lib_extMgm::addToAllTCAtypes($s_table,'--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tabs.googleSitemaps,tx_advancedsitemaps_priority,tx_advancedsitemaps_changeFreq');
		
		// Add default values
		if(intval($a_row['uid']) > 0) {
			$a_row['tx_advancedsitemaps_priority'] = $this->checkRecordConfigurationValue($s_table,$a_row['uid'],'priority');
			$a_row['tx_advancedsitemaps_changeFreq'] = $this->checkRecordConfigurationValue($s_table,$a_row['uid'],'changeFreq');
		}
	}
	
	/**
	 * This hook is used to store the raw input from the form. This is filtered afterwards to store
	 * only the neccesary changes. Unfortunately this will remove our 'fake' fields.
	 *
	 * @access public
	 * @param array $a_fields An associative array containing all values and keys for the table
	 * @param string $s_table The tablename
	 * @param string $i_id The record uid (if exists)
	 * @param object $o_parent The parent object
	 * @return void 
	 */
	public function processDatamap_preProcessFieldArray(&$a_fields, $s_table, $i_uid, &$o_parent) {
		$this->a_fields = $a_fields;
	}
	
	/**
	 * Not used but required
	 *
	 * @access public
	 * @param string $s_status The TCEmain operation status, zb. 'update'
	 * @param string $s_table The table TCEmain is currently processing
	 * @param string $i_uid The records id (if any)
	 * @param array $a_fields The field names and their values to be processed (passed by reference)
	 * @param object $o_parent Reference to the parent object (TCEmain)
	 * @return void
	 */
	public function processDatamap_postProcessFieldArray ($s_status, $s_table, $i_uid, &$a_fields, &$o_parent) {
		//
	}
	
	/**
	 * This is where the actual storage is done.
	 * 
	 * @param string $s_status
	 * @param unknown_type $table
	 * @param unknown_type $id
	 * @param unknown_type $fieldArray
	 * @param unknown_type $o_parent
	 * @return void
	 */
	public function processDatamap_afterDatabaseOperations($s_status, $s_table, $i_uid, $a_fields, $o_parent) {
	if(!$this->isAllowedTable($s_table)) return;
		$i_realRecordUid = (is_numeric($i_uid)) ? $i_uid : $o_parent->substNEWwithIDs[$i_uid];
		
		if(isset($this->a_fields['tx_advancedsitemaps_priority'])) {
			if($this->checkRecordConfigurationValue($s_table,$i_realRecordUid,'priority')) {
				$this->updateRecordConfigurationValue($s_table,$i_realRecordUid,'priority',$this->a_fields['tx_advancedsitemaps_priority']);
			}
			else {
				$this->insertRecordConfigurationValue($s_table,$i_realRecordUid,'priority',$this->a_fields['tx_advancedsitemaps_priority']);
			}
		}
		if(isset($this->a_fields['tx_advancedsitemaps_changeFreq'])) {
			if($this->checkRecordConfigurationValue($s_table,$i_realRecordUid,'changeFreq')) {
				$this->updateRecordConfigurationValue($s_table,$i_realRecordUid,'changeFreq',$this->a_fields['tx_advancedsitemaps_changeFreq']);
			}
			else {
				$this->insertRecordConfigurationValue($s_table,$i_realRecordUid,'changeFreq',$this->a_fields['tx_advancedsitemaps_changeFreq']);
			}
		}
	}
	
	/**
	 * Not used but required
	 *
	 * @access public
	 * @return	void
	 */
	public function processDatamap_preProcessIncomingFieldArray() {
		//
	}
}
?>