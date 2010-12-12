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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(PATH_t3lib . 'class.t3lib_pagetree.php');
require_once(t3lib_extMgm::extPath('advanced_sitemaps') . 'view/class.tx_advancedsitemaps_view.php');

/**
 * Plugin 'Advanced Sitemap' for the 'advanced_sitemaps' extension.
 *
 * @author Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @author Benjamin Serfhos <serfhos@redkiwi.nl>
 * @package TYPO3
 * @subpackage tx_advancedsitemaps
 */
class tx_advancedsitemaps_pi1 extends tslib_pibase {
    /**
     * The prefix ID, used inside piVars
     *
     * @access public
     * @var string
     */
    public $prefixId = 'tx_advancedsitemaps_pi1';

    /**
     * Relative script path
     *
     * @access public
     * @var string
     */
    public $scriptRelPath = 'pi1/class.tx_advancedsitemaps_pi1.php';

    /**
     * The extension key
     *
     * @access public
     * @var string
     */
    public $extKey = 'advanced_sitemaps';

    /**
     * Defines if the cache-hash should be checked, extension allows caching so yes/true.
     * @var bool
     */
    public $pi_checkCHash = true;

    /**
     * The starting PID, page tree starts here
     *
     * @access private
     * @var int
     */
    private $i_startingPid = 0;

    /**
     * The plugin configuration
     *
     * @access private
     * @var array
     */
    private $a_conf;

    /**
     * The loaded entries that will be outputted inside the sitemap
     *
     * @access private
     * @var array
     */
    private $a_entries;

    /**
     * The generated plugin content
     *
     * @access private
     * @var string
     */
    private $s_content = '';

    /**
     * The pre-loaded record configurations for this sitemap
     *
     * @access private
     * @var array
     */
    private $a_recordConfigurations;

    /**
     * Getter for $this->a_conf
     *
     * @access public
     * @return array
     */
    public function getConfig() {
        return $this->a_conf;
    }

    /**
     * Getter for $this->a_entries
     *
     * @access public
     * @return array
     */
    public function getEntries() {
        return $this->a_entries;
    }

    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     * @return    The content that is displayed on the website (Menu)
     */
    public function main($s_content, $a_conf) {
        $this->_init($s_content, $a_conf);

        // Process pages
        $s_addWhere = '';
        if (!$this->a_conf['pages.']['displayHiddenInMenu']) $s_addWhere .= ' AND nav_hide = 0';
        if ($this->a_conf['pages.']['excludePages']) $s_addWhere .= ' AND uid NOT IN (' . $this->a_conf['pages.']['excludePages'] . ')';
        $o_pageTree = t3lib_div::makeInstance('t3lib_pageTree');
        $o_pageTree->addField('SYS_LASTCHANGED', 1);
        $o_pageTree->addField('crdate', 1);
        $o_pageTree->addField('pid', 1);
        $o_pageTree->addField('tx_advancedsitemaps_priority', 1);
        $o_pageTree->addField('tx_advancedsitemaps_changeFreq', 1);
        $o_pageTree->init('AND no_search = 0 AND hidden != 1 AND deleted != 1 AND doktype NOT IN (199, 254, 255, 5)' . $s_addWhere);
        $o_pageTree->getTree($this->i_startingPid);

        // Page limit
        $i_pageCount = count($o_pageTree->tree);
        if ($this->i_pageLimit > 0 && $this->i_pageLimit < $i_pageCount) {
            $o_pageTree->tree = array_slice($o_pageTree->tree, 0, $this->i_pageLimit);
        }

        $a_configuration = array(
            'single_page' => 0,
            'additional_params' => '',
        );
        foreach ($o_pageTree->tree as $a_page)
        {
            $this->addEntry($a_page['row'], 'pages', $a_configuration);
        }

        unset($o_pageTree);
        $o_pageTree = NULL;

        // Process records, if any configurations exist
        if (is_array($this->a_recordConfigurations)) {
            foreach ($this->a_recordConfigurations as $a_configuration) {
                if (!empty($a_configuration['sortby'])) {
                    /**
                     * Reverse ordering
                     *
                     * In case that the parent is set and exists, the sort order should be reversed. This is
                     * due to the fact that each entry is added directly underneath it's parent. Making last first,
                     * and first last. (If we reverse here, it will be switched back when displayed).
                     */
                    if (isset($a_configuration['parent']) && is_array($this->a_entries[$a_configuration['parent']])) {
                        $s_sortByDir = ($a_configuration['sortby_dir'] == 'DESC') ? 'ASC' : 'DESC';
                    }
                    $s_sorting = $a_configuration['sortby'] . ' ' . $a_configuration['sortby_dir'];
                } elseif (isset($GLOBALS['TCA'][$a_configuration['tablename']]['ctrl']['sortby'])) {
                    $s_sorting = $GLOBALS['TCA'][$a_configuration['tablename']]['ctrl']['sortby'] . ' DESC';
                }

                // Construct WHERE
                $a_selectParts = array(
                    $a_configuration['tablename'] . '.*',
                    "p.value AS tx_advancedsitemaps_priority",
                    "cf.value AS tx_advancedsitemaps_changeFreq"
                );
                $s_select = implode(', ', $a_selectParts);

                // Construct FROM
                $a_fromParts = array(
                    $a_configuration['tablename'],
                    "LEFT OUTER JOIN tx_advancedsitemaps_configurations_records AS p ON p.table_name = '" . $a_configuration['tablename'] . "' AND p.record_uid = uid AND type = 'priority'",
                    "LEFT OUTER JOIN tx_advancedsitemaps_configurations_records AS cf ON cf.table_name = '" . $a_configuration['tablename'] . "' AND cf.record_uid = uid AND cf.type = 'changeFreq'"
                );
                $s_from = implode(' ', $a_fromParts);

                // Construct WHERE
                $a_whereParts = array(1);
                $s_languageField = $GLOBALS['TCA'][$a_configuration['tablename']]['ctrl']['languageField'];
                if ($s_languageField) {
                    $a_whereParts[] = $s_languageField . ' = ' . $GLOBALS['TSFE']->sys_language_uid;
                }
                $s_where = implode(' AND ', $a_whereParts) . $this->cObj->enableFields($a_configuration['tablename']);

                // Execute and process entries
                $a_records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    $s_select,
                    $s_from,
                    $s_where,
                    '',
                    $s_sorting
                );

                foreach ($a_records as $a_record) {
                    $this->addEntry($a_record, $a_configuration['tablename'], $a_configuration);
                }
            }
        }

        // Output rendering
        $o_renderer = t3lib_div::makeInstance('tx_advancedsitemaps_view');
        $s_content = $o_renderer->render($this);
        return $s_content;
    }

    /**
     * Initializes the plugin
     *
     * @access protected
     * @param string $s_content The plugin content
     * @param array $a_conf The plugin configuration
     * @return void
     */
    protected function _init($s_content, $a_conf) {
        $this->s_content = $s_content;
        $this->a_conf = $a_conf;

        // Locallang
        $this->pi_loadLL();

        // Plugin configuration, merge TS/FF
        $this->pi_initPiFlexForm();
        foreach ($this->cObj->data['pi_flexform']['data'] as $s_sheetName => $a_sheetData) {
            $s_prefix = strtolower(substr($s_sheetName, 1)) . '.';
            foreach ($a_sheetData['lDEF'] as $s_fieldName => $a_fieldData) {
                $s_fieldValue = $a_fieldData['vDEF'];
                if (!empty($s_fieldValue)) {
                    if ($s_prefix != 'def.') {
                        $this->a_conf[$s_prefix][$s_fieldName] = $s_fieldValue;
                    } else {
                        $this->a_conf[$s_fieldName] = $s_fieldValue;
                    }
                }
            }
        }

        // Pre-load record configurations
        $this->i_startingPid = $this->cObj->data['pages'] ? $this->cObj->data['pages'] : $GLOBALS['TSFE']->id;
        $this->i_pageLimit = intval($this->a_conf['pages.']['limit']);
        $this->a_entries = array();
        $this->a_recordConfigurations = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'tx_advancedsitemaps_configurations',
                'uid IN (' . $this->a_conf['records.']['records'] . ')' . $this->cObj->enableFields('tx_advancedsitemaps_configurations'),
            '',
            'sorting ASC'
        );
    }

    /**
     * Adds an entry to the internal entry list which is used to render the total
     *
     * @access public
     * @param array $a_record An array containing the record data
     * @param string $s_table The record table
     * @param array $a_configuration The table configuration
     * @param string $s_parent The parent record, if none is set the record will be put at the bottom of the list
     * @return void
     */
    public function addEntry($a_record, $s_table, $a_configuration) {
        $s_labelField = $GLOBALS['TCA'][$s_table]['ctrl']['label'];
        $s_crdateField = $GLOBALS['TCA'][$s_table]['ctrl']['crdate'];
        $s_tstampField = $GLOBALS['TCA'][$s_table]['ctrl']['tstamp'];
        $s_tstampField = ($s_table == 'pages') ? 'SYS_LASTCHANGED' : $GLOBALS['TCA'][$s_table]['ctrl']['tstamp'];
        $s_parent = ($s_table == 'pages') ? $a_record['pid'] : self::replaceFields($a_record, $a_configuration['parent']);

        // Setup the new entry
        $a_newEntry = array(
            'title' => $a_record[$s_labelField],
            'url' => $this->createLink($a_record, $s_table, $a_configuration),
            'crdate' => $a_record[$s_crdateField],
            'tstamp' => $a_record[$s_tstampField],
            'level' => ($s_table == 'pages') ? intval($this->a_entries['pages:' . $a_record['pid']]) + 1 : intval($this->a_entries[$s_parent]['level']) + 1,
            'priority' => $a_record['tx_advancedsitemaps_priority'],
            'changeFreq' => $a_record['tx_advancedsitemaps_changeFreq'],
        );

        // Set default priority and change frequency, in case not set
        if (empty($a_newEntry['priority'])) {
            $a_newEntry['priority'] = ($a_configuration['gs_priority']) ? $a_configuration['gs_priority'] : '0.5';
        }
        if (empty($a_newEntry['changeFreq'])) {
            $a_newEntry['changeFreq'] = ($a_configuration['gs_changeFreq']) ? $a_configuration['gs_changeFreq'] : 'weekly';
        }

        // Insert the entry into the list, underneath it's parent if set.
        if (empty($s_parent)) {
            // Parent is not set, just add at the end
            $this->a_entries[$s_table . ':' . $a_record['uid']] = $a_newEntry;
        } else {
            // Parent is set, compute insertion point
            $a_entryKeys = array_keys($this->a_entries);
            $i_offset = array_search($s_parent, $a_entryKeys);
            if ($i_offset) {
                $i_offset++;
                // Insertion point found, add record
                $a_entriesAfter = array_splice($this->a_entries, $i_offset);
                $this->a_entries[$s_table . ':' . $a_record['uid']] = $a_newEntry;
                $this->a_entries = array_merge($this->a_entries, $a_entriesAfter);
            } else {
                // Insertion point not found, add at the end anyways
                $this->a_entries[$s_table . ':' . $a_record['uid']] = $a_newEntry;
            }
        }
    }

    /**
     * Creates a link to the record
     *
     * @access private
     * @param array $a_record The record
     * @param string $s_table
     * @param array $a_configuration The table configuration
     * @return string The generated link
     */
    private function createLink($a_record, $s_table, $a_configuration) {
        $s_additionalParameters = self::replaceFields($a_record, $a_configuration['url_params']);
        $s_url = $this->cObj->typoLink_URL(array(
            'parameter' => ($s_table == 'pages') ? $a_record['uid'] : $a_configuration['single_page'],
            'additionalParams' => $s_additionalParameters,
        ));
        return $s_url;
    }

    /**
     * Replaces fields inside a text. Used for rendering links with parameters
     *
     * @param array $a_needles An array containing all the field names to look for
     * @param string $s_haystack The string to process
     * @return string The processed string
     */
    public static function replaceFields($a_needles, $s_haystack) {
        $a_matches = array();
        $a_replacements = array();
        foreach ($a_needles as $s_key => $s_value) {
            $a_matches[] = '{field:' . $s_key . '}';
            $a_replacements[] = $s_value;
        }
        $s_result = str_replace($a_matches, $a_replacements, $s_haystack);

        preg_match_all('/\{field\:([^}]*)\}/i', $s_result, $a_secMatches);
        $i_matches = count($a_secMatches[1]);
        for ($i = 0; $i < $i_matches; $i++) {
            $a_matchParts = t3lib_div::trimExplode(':', $a_secMatches[1][$i]);
            if (count($a_matchParts == 3)) {
                if ($a_matchParts[1] == 'date') {
                    $s_replacement = date($a_matchParts[2], $a_needles[$a_matchParts[0]]);
                    $s_result = str_replace($a_secMatches[0][$i], $s_replacement, $s_result);
                }
            }
        }
        return $s_result;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php']);
}
?>