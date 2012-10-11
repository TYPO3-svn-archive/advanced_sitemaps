<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Generate a page-tree, non-browsable.
 *
 * Revised for TYPO3 3.6 November/2003 by Kasper Skårhøj
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @coauthor	René Fritz <r.fritz@colorcube.de>
 */


/**
 * Class for generating a page tree.
 *
 * @author	Sebastiaan de Jonge <office@sebastiaandejonge.com
 * @see t3lib_treeView, t3lib_pageTree
 * @package TYPO3
 * @subpackage t3lib
 */
class tx_advancedsitemaps_localizedPageTree extends t3lib_treeView {
    var $fieldArray = array(
        'p.uid',
        'p.title',
        'p.doktype',
        'p.php_tree_stop',
        'p.t3ver_id',
        'p.t3ver_state',
        'plo.title AS plo_title',
    );
    var $defaultList = 'p.uid,p.pid,p.tstamp,p.sorting,p.deleted,p.perms_userid,p.perms_groupid,p.perms_user,p.perms_group,p.perms_everybody,p.crdate,p.cruser_id';
    var $setRecs = 0;

    /**
     * Init function
     * REMEMBER to feed a $clause which will filter out non-readable pages!
     *
     * @param string $clause Part of where query which will filter out non-readable pages.
     * @param string $orderByFields record ORDER BY field
     * @return void
     */
    function init($clause = '', $orderByFields = '') {
        parent::init(' AND p.deleted=0 ' . $clause, 'sorting');

        if (t3lib_extMgm::isLoaded('cms')) {
            $this->fieldArray = array_merge(
                $this->fieldArray,
                array(
                     'p.hidden',
                     'p.starttime',
                     'p.endtime',
                     'p.fe_group',
                     'p.module',
                     'p.extendToSubpages',
                     'p.nav_hide')
            );
        }

        $this->table = 'pages AS p LEFT OUTER JOIN (SELECT pid AS plo_pid, title FROM pages_language_overlay WHERE sys_language_uid = '.intval($GLOBALS['TSFE']->sys_language_uid).') AS plo ON plo.plo_pid = p.uid';
        $this->treeName = 'pages';
    }

    /**
     * Returns TRUE/FALSE if the next level for $id should be expanded - and all levels should, so we always return 1.
     *
     * @param	integer		ID (uid) to test for (see extending classes where this is checked againts session data)
     * @return	boolean
     */
    function expandNext($id) {
        return 1;
    }


    /**
     * Get stored tree structure AND updating it if needed according to incoming PM GET var.
     * - Here we just set it to nothing since we want to just render the tree, nothing more.
     *
     * @return	void
     * @access private
     */
    function initializePositionSaving() {
        $this->stored = array();
    }
}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/classes/class.tx_advancedsitemaps_localizedpagetree.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/classes/class.tx_advancedsitemaps_localizedpagetree.php']);
}
?>