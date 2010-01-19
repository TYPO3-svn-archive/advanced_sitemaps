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
require_once(PATH_t3lib.'class.t3lib_pagetree.php');

/**
 * Plugin 'Advanced sitemap' for the 'advanced_sitemaps' extension.
 *
 * @author	Sebastiaan de Jonge <szebi.eger@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_advancedsitemaps
 */
class tx_advancedsitemaps_pi1 extends tslib_pibase {
	public $prefixId = 'tx_advancedsitemaps_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_advancedsitemaps_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey = 'advanced_sitemaps';	// The extension key.
	public $pi_checkCHash = true;
	
/**
	 * The TypoScript configuration array
	 *
	 * @access private
	 * @var array
	 */
	private $a_conf;
	
	/**
	 * The content that the plugin will output
	 *
	 * @access private
	 * @var string
	 */
	private $s_content;
	
	/**
	 * The generated XML document (SimpleXML)
	 *
	 * @access private
	 * @var object (SimpleXML)
	 */
	private $o_document;
	
	/**
	 * The baseUrl of the website
	 *
	 * @access private
	 * @var string
	 */
	private $s_baseUrl;
	
	/**
	 * Standard where statement that can be used in multiple queries (enableFields)
	 *
	 * @access private
	 * @var string
	 */
	private $s_standardWhere = 'deleted = 0 AND hidden = 0 AND (starttime = 0 || starttime > NOW()) AND (endtime = 0 || endtime < NOW()) ';
	
	/**
	 * The generated sitemap in a multidimensional array
	 * 
	 * @access private
	 * @var array
	 */
	private $a_sitemapData;
	
	/**
	 * An array where all urls that are added to the sitemap are stored in, used to prevent duplicates
	 *
	 * @access private
	 * @var array
	 */
	private $a_userUrls = array();
	
	/**
	 * Initialize the plugin
	 * 
	 * @param string $s_content The content of the plugin
	 * @param array $a_conf The TypoScript configuration for 
	 */
	protected function init($s_content, $a_conf)
	{
		// Store configuration to local variables
		$this->a_conf = $a_conf;
		$this->s_content = $s_content;
		$this->s_baseUrl = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'];
		$this->a_sitemapData = array();
		
		// Initialize plugin
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		// Overwrite local configuration with FlexForm values
		
		// Disable caching completely
		if($this->a_conf['disableAllCaching']) $GLOBALS['TSFE']->set_no_cache();
	}
	
	/**
	 * The main method of the PlugIn
	 *
	 * @access public
	 * @param string $s_content The plugin content
	 * @param array $a_conf The TypoScript configuration for this plugin
	 * @return void
	 */
	public function main($s_content, $a_conf) {
		// Initialize
		$this->init();
		
		
		
		/*
		
		
		
		// Setup the XML document
		$this->o_document = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
		$this->o_document->addAttribute('xmlns','http://www.google.com/schemas/sitemap/0.84');
		$this->o_document->addAttribute('xsi:schemaLocation','http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd','http://www.w3.org/2001/XMLSchema-instance');
		
		// Load page tree
		$o_pageTree = t3lib_div::makeInstance('t3lib_pageTree');
		$o_pageTree->addField('SYS_LASTCHANGED', 1);
		$o_pageTree->addField('crdate', 1);
		$o_pageTree->addField('sitemap_index_changefreq',1);
		$o_pageTree->addField('sitemap_index_prioriy',1);
		$o_pageTree->init('AND '.$this->s_standardWhere.'AND no_search = 0 AND doktype NOT IN (199, 254, 255, 5) AND nav_hide = 0');
		$o_pageTree->getTree(1,50,'');
		
		// Add pages
		foreach($o_pageTree->tree as $a_branch)
		{
			$s_realUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL($a_branch['row']['uid']);
			$this->addUrl(
				$s_realUrl,
				$a_branch['row']['SYS_LASTCHANGED'] ? $a_branch['row']['SYS_LASTCHANGED'] : $a_branch['row']['crdate'],
				$a_branch['row']['sitemap_index_changefreq'] != '' ? $a_branch['row']['sitemap_index_changefreq'] : 'daily',
				$a_branch['row']['sitemap_index_prioriy'] != '' ? $a_branch['row']['sitemap_index_prioriy'] : '0.7'
			);
		}
		unset($o_pageTree);
		
		// News settings
		$i_newsSinglePid = 5;
		$i_newsCatSinglePid = 15;
		$i_newsListPid = 16;
		$i_newsStoragePid = 13;
		
		// Add news categories
		$a_newCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid,tstamp,crdate',
			'tt_news_cat',
			$this->s_standardWhere.'AND pid ='.$i_newsStoragePid
		);
		foreach($a_newCategories as $a_newsCategory)
		{
			$s_realUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL(
				$i_newsCatSinglePid,
				array(
					'tx_ttnews' => array(
						'cat' => $a_newsCategory['uid'],
					),
				)
			);
			$this->addUrl(
				$s_realUrl,
				$a_newsCategory['tstamp'] > 0 ? $a_newsCategory['tstamp'] : $a_newsCategory['crdate'],
				'weekly',
				'0.4'
			);
		}
		
		// Add news articles
		$a_newsArticles = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid,tstamp,datetime',
			'tt_news',
			$this->s_standardWhere.'AND pid ='.$i_newsStoragePid
		);
		foreach($a_newsArticles as $a_newsArticle)
		{
			$s_realUrl = $GLOBALS['TSFE']->cObj->getTypoLink_URL(
				$i_newsSinglePid,
				array(
					'tx_ttnews' => array(
						'tt_news' => $a_newsArticle['uid'],
						'year' => date('Y',$a_newsArticle['datetime']),
						'month' => date('m',$a_newsArticle['datetime']),
					),
				)
			);
			$this->addUrl(
				$s_realUrl,
				$a_newsArticle['tstamp'] > 0 && $a_newsArticle['tstamp'] > $a_newsArticle['datetime'] ? $a_newsArticle['tstamp'] : $a_newsArticle['datetime'],
				'daily',
				'0.8'
			);
		}
		
		
		// Generate XML and cleanup
		$o_dom = dom_import_simplexml($this->o_document)->ownerDocument;
		$o_dom->formatOutput = true;
		
		// Output headers
		header('Content-type: text/xml;');
		
		// Print output and exit
		$this->s_content = $o_dom->saveXML();
		@file_put_contents('/home/admin/domains/sebastiaandejonge.nl/public_html/sitemap.xml',$this->s_content);
		echo $this->s_content;
		exit();
		*/
	}
	
	/**
	 * Displays Google Sitemap (XML)
	 *
	 */
	private function displayGoogleSitemap()
	{
		
	}
	
	/**
	 * Adds a URL to the XML document
	 *
	 * @access private
	 * @param int|string $m_location The URL/or UID of the page
	 * @param int|string $m_lastModified The modify date
	 * @param string $s_changeFrequency
	 * @param float $f_priority The page priority
	 * @return void
	 */
	private function addUrl($m_location, $m_lastModified = NULL, $s_changeFrequency = 'daily', $f_priority = 0.5)
	{
		// Load (if neccesary) the proper URL and set it
		if(is_int($m_location))
		{
		}
		$s_location = $this->s_baseUrl.$m_location;
		if(in_array($s_location,$this->a_usedUrls)) return;
		
		// Date formatting
		if(is_int($m_lastModified) || is_numeric($m_lastModified))
		{
			$m_lastModified = date('Y-m-d',$m_lastModified);
		}
		$s_lastModified = $m_lastModified;

		// Set the XML child object
		$o_childElement = $this->o_document->addChild('url');
		$o_childElement->addChild('loc',$s_location);
		$o_childElement->addChild('lastmod',$s_lastModified);
		$o_childElement->addChild('changefreq',$s_changeFrequency);
		$o_childElement->addChild('priority',$f_priority);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/pi1/class.tx_advancedsitemaps_pi1.php']);
}

?>