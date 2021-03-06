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
    protected $a_globalMarkers = array();
    protected $a_csvFields = array();

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
        if (empty($s_templateFile) && $s_outputFormat != 'csv') exit('(Advanced Sitemaps) No template found, have you included the static template?');
        $s_templateData = t3lib_div::getURL($s_templateFile);
        if (empty($s_templateData) && $s_outputFormat != 'csv') exit('(Advanced Sitemaps) No template data found, file does not exist, is empty or not readable...');

        $this->s_templateWrap = t3lib_parsehtml::getSubpart($s_templateData, '###TEMPLATE_' . strtoupper($s_outputFormat) . '###');
        $this->s_templateItem = t3lib_parsehtml::getSubpart($this->s_templateWrap, '###SUBPART_ENTRY###');
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
            case 'csv':
                $s_baseUrl = $GLOBALS['TSFE']->absRefPrefix ? $GLOBALS['TSFE']->absRefPrefix : $GLOBALS['TSFE']->baseUrl;
                $this->b_directOutput = true;
                $this->s_mimeType = 'text/plain';
                $a_entryKeys = array_keys($this->a_entries);
                foreach ($a_entryKeys as $s_entryKey) {
                    $this->a_entries[$s_entryKey]['url'] = htmlspecialchars($this->a_entries[$s_entryKey]['url']);
                    if (strstr($this->a_entries[$s_entryKey]['url'], $s_baseUrl) === false) {
                        $this->a_entries[$s_entryKey]['url'] = $s_baseUrl . $this->a_entries[$s_entryKey]['url'];
                    }
                }

                // Spoof the wrapper template
                $this->s_templateWrap = '<!-- ###SUBPART_ENTRY### --><!-- ###SUBPART_ENTRY### -->';

                // Generate "markers"
                $_prefixCallback = create_function('$str','return "field.".$str;');
                $this->a_csvFields = array_merge(
                    array_map($_prefixCallback,t3lib_div::trimExplode(',',$this->a_conf['csv.']['pageFields'],true)),
                    t3lib_div::trimExplode(',',$this->a_conf['csv.']['customFields'],true)
                );
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
            case 'google_news':
                $s_baseUrl = $GLOBALS['TSFE']->absRefPrefix ? $GLOBALS['TSFE']->absRefPrefix : $GLOBALS['TSFE']->baseUrl;
                $this->b_directOutput = true;
                $this->s_mimeType = 'text/xml';
                $this->a_globalMarkers = array(
                    'news_name' => $this->a_conf['news.']['name'],
                    'language' => $this->a_conf['news.']['language'],
                    'wrap_genres' => $this->a_conf['news.']['genres'],
                    'wrap_access' => '',
                );
                foreach ($this->a_entries as $s_entryKey => $a_entryData) {
                    $this->a_entries[$s_entryKey]['wrap_keywords'] =
                            ($a_entryData['wrap_keywords'] && $this->a_conf['news.']['keywords']) ?
                                    $a_entryData['wrap_keywords'].','.$this->a_conf['news.']['keywords'] :
                                    $a_entryData['wrap_keywords'].$this->a_conf['news.']['keywords'];
                    $this->a_entries[$s_entryKey]['wrap_stockTicker'] =
                            ($a_entryData['wrap_stockTicker'] && $this->a_conf['news.']['stockTicker']) ?
                                    $a_entryData['wrap_stockTicker'].','.$this->a_conf['news.']['stockTicker'] :
                                    $a_entryData['wrap_stockTicker'].$this->a_conf['news.']['stockTicker'];
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

            switch($s_outputFormat) {
                case 'XML':
                    echo '<?xml version="1.0" encoding="UTF-8"?>';
                    break;
                case 'CSV':
                    // @todo Add headers
                    break;
            }
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
            // CSV rendering
            if($this->a_conf['outputFormat'] == 'csv') {
                $a_data = array();
                foreach($this->a_csvFields as $s_fieldName) {
                    // Data comes from the record
                    if(strpos($s_fieldName, 'field.') === 0) {
                        $a_data[] = $a_entryData['record'][substr($s_fieldName,6)];
                    }
                    // Data comes from the entry data
                    elseif(!is_object($a_entryData[$s_fieldName]) && !is_array($a_entryData[$s_fieldName])) {
                        $a_data[] = $a_entryData[$s_fieldName];
                    }
                }

                $s_renderedItems .= $this->strPutCSV($a_data, $this->a_conf['csv.']['delimiter'], $this->a_conf['csv.']['enclosure']);
            }
            // Normal rendering
            else {
                $s_renderedItems .= $this->renderItem($a_entryData, $s_entryKey);
            }
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
        $s_renderedItem = $this->s_templateItem;
        if (empty($a_entryData['url'])) return ''; // No access, no display
        // Set global markers
        $a_markers = array();
        $a_entryData = array_merge($a_entryData,$this->a_globalMarkers);
        foreach ($a_entryData as $s_itemKey => $s_itemValue) {
            if(stristr($s_itemKey,'wrap_')) {
                $s_subpart = $s_itemValue ? t3lib_parsehtml::getSubpart($s_renderedItem,'###'.strtoupper($s_itemKey)) : '';
                $s_renderedItem = t3lib_parsehtml::substituteSubpart($s_renderedItem,'###'.strtoupper($s_itemKey).'###',$s_subpart);
                $a_markers[substr($s_itemKey,5)] = $s_itemValue;
            }
            else {
                $a_markers[$s_itemKey] = $s_itemValue;
            }
        }

        // Apply wraps
        $s_stdWrap = 'level' . $a_entryData['level'] . '_stdWrap.';
        $b_hasSub = $this->hasChildren($s_entryKey, $a_entryData);
        if ($b_hasSub) {
            $a_markers['WRAPPED_TITLE'] = $this->o_plugin->cObj->stdWrap($a_entryData['title'], $this->a_conf[$s_stdWrap]['ifSub.']);
        } else {
            $a_markers['WRAPPED_TITLE'] = $this->o_plugin->cObj->stdWrap($a_entryData['title'], $this->a_conf[$s_stdWrap]);
        }

        // Render the remaining markers
        $s_renderedItem = t3lib_parsehtml::substituteMarkerArray($s_renderedItem, $a_markers, '###|###', true, true);
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

    /**
     * Render function to write CSV data into a string
     *
     * @param   array   $a_data         The data array containing the values
     * @param   string  $s_delimiter    The delimiter
     * @param   string  $s_enclosure    The string enclosure
     * @param   string  $s_terminator   The row terminator
     * @return  string  The CSV data
     */
    protected function strPutCSV($a_data, $s_delimiter = ',', $s_enclosure = '"', $s_terminator = "\n") {
        # First convert associative array to numeric indexed array
        foreach ($a_data as $value) $a_workData[] = $value;

        $s_returnValue = '';
        $i_dataSize = count($a_workData);

        for ($i=0; $i<$i_dataSize; $i++) {
            if (is_array($a_workData[$i])) {
                $s_returnValue .= str_putcsv($a_workData[$i], $s_delimiter, $s_enclosure, $s_terminator);
            } else {
                switch (gettype($a_workData[$i])) {
                    case "NULL":     $s_format = ''; break;
                    case "boolean":  $s_format = ($a_workData[$i] == true) ? 'true': 'false'; break;
                    # Make sure sprintf has a good datatype to work with
                    case "integer":  $s_format = '%i'; break;
                    case "double":   $s_format = '%0.2f'; break;
                    case "string":   $s_format = '%s'; break;
                    # Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested
                    case "object":
                    case "resource":
                    default:         $s_format = ''; break;
                }
                $s_returnValue .= sprintf('%2$s'.$s_format.'%2$s', $a_workData[$i], $s_enclosure);
                $s_returnValue .= ($i < ($i_dataSize-1)) ? $s_delimiter : $s_terminator;
            }
        }

        return $s_returnValue;
    }
}

/**
 * XCLASS inclusion
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/view/class.tx_advancedsitemaps_view.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/view/class.tx_advancedsitemaps_view.php']);
}
?>