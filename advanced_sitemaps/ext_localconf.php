<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_advancedsitemaps_configurations=1
');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_advancedsitemaps_pi1.php', '_pi1', 'list_type', 1);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:advanced_sitemaps/class.tx_advancedsitemaps_recordConfigurations.php:&tx_advancedsitemaps_recordConfigurations';
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:advanced_sitemaps/class.tx_advancedsitemaps_recordConfigurations.php:&tx_advancedsitemaps_recordConfigurations';
?>