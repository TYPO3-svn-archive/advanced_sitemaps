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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Advanced sitemap' for the 'advanced_sitemaps' extension.
 *
 * @author	Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_advancedsitemaps
 */
class tx_advancedsitemaps_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_advancedsitemaps_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_advancedsitemaps_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'advanced_sitemaps';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website (Menu)
	 */
	function main($content, $conf)	{
			// Get the PID from which to make the menu.
			// If a page is set as reference in the 'Startingpoint' field, use that
			// Otherwise use the page's id-number from TSFE
		$menuPid = intval($this->cObj->data['pages']?$this->cObj->data['pages']:$GLOBALS['TSFE']->id);
	
			// Now, get an array with all the subpages to this pid:
			// (Function getMenu() is found in class.t3lib_page.php)
		$menuItems_level1 = $GLOBALS['TSFE']->sys_page->getMenu($menuPid);
	
			// Prepare vars:
		$tRows=array();
	
			// Traverse menuitems:
		reset($menuItems_level1);
		while(list($uid,$pages_row)=each($menuItems_level1))	{
			$tRows[]='<tr bgColor="#cccccc"><td>'.$this->pi_linkToPage(
				$pages_row['nav_title']?$pages_row['nav_title']:$pages_row['title'],
				$pages_row['uid'],
				$pages_row['target']
			).'</td></tr>';
		}
	
		$totalMenu = '<table border=0 cellpadding=0 cellspacing=2>
			<tr><td>This is a menu. Go to your favourite page:</td></tr>
			'.implode('',$tRows).
			'</table><br />('.$this->tellWhatToDo('Click here if you want to know where to change the menu design').')';
	
		return $totalMenu;
	}
	
	/**
	 * Here you can do what ever you want
	 *
	 * @param	string		$str: The string that is processed
	 * @return	It's your decission
	 */
	function tellWhatToDo($str)	{
		return '<a href="#" onClick="alert(\'Open the PHP-file '.t3lib_extMgm::siteRelPath('advanced_sitemaps').'pi1/class.tx_advancedsitemaps_pi1.php and edit the function main()\nto change how the menu is rendered! It is pure PHP coding!\')">'.$str.'</a>';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php']);
}

?>