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
 * Class renders the output into HTML
 *
 * @author Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @package TYPO3
 * @subpackage tx_advancedsitemaps
 */
class tx_advancedsitemaps_view {
    protected $a_conf;
    protected $a_entries;
    protected $s_templateItem;
    protected $s_templateWrap;
    protected $o_plugin;
    protected $b_directOutput = false;
    protected $s_mimeType = 'text/html';

    /**
     * Main render function
     *
     * @access public
     * @param tx_advancedsitemaps_pi1 $o_plugin
     * @return string The rendered content
     */
    public function render(tx_advancedsitemaps_pi1 &$o_plugin) {
        $this->a_conf = $o_plugin->getConfig();
        $this->a_entries = $o_plugin->getEntries();
        $s_outputFormat = strtolower($this->a_conf['outputFormat']);
        $this->o_plugin = $o_plugin;

        $s_templateFile = t3lib_div::getFileAbsFileName($this->a_conf[$s_outputFormat . '.']['templateFile']);
        if (empty($s_templateFile)) exit('(Advanced Sitemaps) No template found, have you included the static template?');
        $s_templateData = t3lib_div::getURL($s_templateFile);
        if (empty($s_templateData)) exit('(Advanced Sitemaps) No template data found, file does not exist, is empty or not readable...');

        $this->s_templateWrap = t3lib_parsehtml::getSubpart($s_templateData, '###TEMPLATE_' . strtoupper($s_outputFormat) . '###');
        $this->s_templateItem = t3lib_parsehtml::getSubpart($this->s_templateWrap, '###SUBPART_ENTRY###');
        $s_content = '';
        switch ($s_outputFormat) {
            case 'html':
                break;
            case 'xml':
                // Not available yet
                break;
            case 'rss':
                // Not available yet
                break;
            case 'atom':
                // Not available yet
                break;
            case 'google':
                $s_baseUrl = $GLOBALS['TSFE']->absRefPrefix ? $GLOBALS['TSFE']->absRefPrefix : $GLOBALS['TSFE']->baseUrl;
                $this->b_directOutput = true;
                $this->s_mimeType = 'text/xml';
                $a_entryKeys = array_keys($this->a_entries);
                foreach ($a_entryKeys as $s_entryKey) {
                    $i_lastMod = (intval($this->a_entries[$s_entryKey]['tstamp']) != 0) ? $this->a_entries[$s_entryKey]['tstamp'] : $this->a_entries[$s_entryKey]['crdate'];
                    $this->a_entries[$s_entryKey]['change_frequency'] = ($this->a_entries[$s_entryKey]['changeFreq']) ? $this->a_entries[$s_entryKey]['changeFreq'] : $this->a_conf['google.']['changeFrequency'];
                    $this->a_entries[$s_entryKey]['priority'] = ($this->a_entries[$s_entryKey]['priority']) ? $this->a_entries[$s_entryKey]['priority'] : $this->a_conf['google.']['priority'];
                    $this->a_entries[$s_entryKey]['tstamp'] = date($this->a_conf['google.']['dateFormat'], $i_lastMod);
                    $this->a_entries[$s_entryKey]['url'] = htmlspecialchars($this->a_entries[$s_entryKey]['url']);
                    if (strstr($this->a_entries[$s_entryKey]['url'], $s_baseUrl) === false) {
                        $this->a_entries[$s_entryKey]['url'] = $s_baseUrl . $this->a_entries[$s_entryKey]['url'];
                    }
                }
                break;
            default:
                return 'Bad output format selected (' . $s_outputFormat . ')';
                break;
        }

        $s_renderedItems = $this->renderItems();
        $s_content = t3lib_parsehtml::substituteSubpart($this->s_templateWrap, '###SUBPART_ENTRY###', $s_renderedItems);

        if ($this->b_directOutput) {
            header('Content-type: ' . $this->s_mimeType . ';');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo $s_content;
            exit();
        }
        return $s_content;
    }

    /**
     * Renderes all items
     *
     * @access protected
     * @param string $s_templateData The template for single items
     * @param array $a_items An array containing all the items that need to be rendered
     * @return string The rendered items
     */
    protected function renderItems() {
        $s_renderedItems = '';
        $i_itemCount = 0;
        foreach ($this->a_entries as $s_entryKey => $a_entryData) {
            $s_renderedItems .= $this->renderItem($a_entryData, $s_entryKey);
            if (++$i_itemCount == $this->a_conf['displayMax'] && $this->a_conf['displayMax'] != 0) break;
        }
        return $s_renderedItems;
    }

    /**
     * Renders a single item
     *
     * @access protected
     * @param string $s_templateData The template subpart
     * @param array $a_itemData The item data
     * @return string The rendered item
     */
    protected function renderItem($a_entryData, $s_entryKey) {
        if (empty($a_entryData['url'])) return ''; // No access, no display
        $a_markers = array();
        foreach ($a_entryData as $s_itemKey => $s_itemValue) {
            $a_markers[$s_itemKey] = $s_itemValue;
        }

        $s_stdWrap = 'level' . $a_entryData['level'] . '_stdWrap.';
        $b_hasSub = $this->hasChildren($s_entryKey, $a_entryData);
        if ($b_hasSub) {
            $a_markers['WRAPPED_TITLE'] = $this->o_plugin->cObj->stdWrap($a_entryData['title'], $this->a_conf[$s_stdWrap]['ifSub.']);
        } else {
            $a_markers['WRAPPED_TITLE'] = $this->o_plugin->cObj->stdWrap($a_entryData['title'], $this->a_conf[$s_stdWrap]);
        }

        $s_renderedItem = t3lib_parsehtml::substituteMarkerArray($this->s_templateItem, $a_markers, '###|###', true, true);
        return $s_renderedItem;
    }

    /**
     * Check if the current item has children
     *
     * @protected
     * @param string $s_currentKey The current entry key
     * @param array $a_currentEntry The current entry data
     * @return bool
     */
    protected function hasChildren($s_currentKey, $a_currentEntry) {
        $a_entryKeys = array_keys($this->a_entries);
        $i_offset = array_search($s_currentKey, $a_entryKeys);
        $a_subpart = array_slice($this->a_entries, $i_offset + 1, 1, true);
        if ($a_subpart[key($a_subpart)]['level'] > $a_currentEntry['level']) return true;
        return false;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/view/class.tx_advancedsitemaps_view.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/view/class.tx_advancedsitemaps_view.php']);
}
?>