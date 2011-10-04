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
				($s_tablename != 'tt_content' &&
				preg_match('/^(tt|tx|user)_/', $s_tablename) &&
				isset($GLOBALS['TCA'][$s_tablename]) &&
				!$GLOBALS['TCA'][$s_tablename]['hideTable'] &&
				strpos($s_tablename, 'tx_mnogosearch_') === false &&
				strpos($s_tablename, 'tx_advancedsitemaps_') === false) ||
                $s_tablename = 'pages'
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
?>