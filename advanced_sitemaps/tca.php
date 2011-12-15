<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$TCA['tx_advancedsitemaps_configurations'] = array(
    'ctrl' => $TCA['tx_advancedsitemaps_configurations']['ctrl'],
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,fe_group,tablename,parent,url_params,sortby,record_limit,single_page,startingpoint,recursive'
    ),
    'feInterface' => $TCA['tx_advancedsitemaps_configurations']['feInterface'],
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_advancedsitemaps_configurations',
                'foreign_table_where' => 'AND tx_advancedsitemaps_configurations.pid=###CURRENT_PID### AND tx_advancedsitemaps_configurations.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'starttime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'default' => '0',
                'checkbox' => '0'
            )
        ),
        'endtime' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'range' => array(
                    'upper' => mktime(3, 14, 7, 1, 19, 2038),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
                )
            )
        ),
        'fe_group' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                ),
                'foreign_table' => 'fe_groups'
            )
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
            )
        ),
        'tablename' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.tablename',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listTables',
                'eval' => 'required',
            )
        ),
        'andWhere' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.andWhere',
            'displayCond' => 'HIDE_FOR_NON_ADMINS',
            'config' => array(
                'type' => 'text',
                'rows' => '5',
                'columns' => '30',
            )
        ),
        'parent' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.parent',
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        ),
        'url_params' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.url_params',
            'config' => array(
                'type' => 'input',
                'size' => '48',
            )
        ),
        'sortby' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby',
            'displayCond' => 'FIELD:tablename:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
            )
        ),
        'sortby_dir' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir',
            'displayCond' => 'FIELD:sortby:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', ''),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir.ASC', 'DESC'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.sortby_dir.DESC', 'ASC'),
                ),
            )
        ),
        'record_limit' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.record_limit',
            'config' => array(
                'type' => 'input',
                'size' => '4',
                'max' => '4',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => array(
                    'upper' => '1000',
                    'lower' => '0'
                ),
                'default' => 0
            )
        ),
        'single_page' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.single_page',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
        'startingpoint' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.startingpoint',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 4,
                'minitems' => 0,
                'maxitems' => 10,
            )
        ),
        'recursive' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.recursive',
            'config' => array(
                'type' => 'select',
                'items' => Array(
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
        'gs_priority' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.priority',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', '0'),
                    array('0.1', '0.1'),
                    array('0.2', '0.2'),
                    array('0.3', '0.3'),
                    array('0.4', '0.4'),
                    array('0.5', '0.5'),
                    array('0.6', '0.6'),
                    array('0.7', '0.7'),
                    array('0.8', '0.8'),
                    array('0.9', '0.9'),
                    array('1.0', '1.0')
                ),
            ),
        ),
        'gs_changeFreq' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', ''),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.always', 'always'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.hourly', 'hourly'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.daily', 'daily'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.weekly', 'weekly'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.monthly', 'monthly'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.yearly', 'yearly'),
                    array('LLL:EXT:advanced_sitemaps/locallang_db.xml:fields.changeFreq.never', 'never'),
                ),
                'selected' => 'never'
            ),
        ),
        'gsn_dateField' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.gsn_dateField',
            'displayCond' => 'FIELD:tablename:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
            )
        ),
        'gsn_titleField' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.gsn_titleField',
            'displayCond' => 'FIELD:tablename:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
            )
        ),
        'gsn_keywordsField' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.gsn_keywordsField',
            'displayCond' => 'FIELD:tablename:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
            )
        ),
        'gsn_stockTickerField' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.gsn_stockTickerField',
            'displayCond' => 'FIELD:tablename:!=:',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'itemsProcFunc' => 'tx_advancedsitemaps_tca->listFields',
            )
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'title, sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, tablename;;2, andWhere, startingpoint, recursive, parent,--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tx_advancedsitemaps_configurations.link_configuration,title_field, single_page, url_params,--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tabs.googleSitemaps,gs_priority,gs_changeFreq,--div--;LLL:EXT:advanced_sitemaps/locallang_db.xml:tabs.googleNewsSitemaps,gsn_dateField,gsn_keywordsField,gsn_stockTickerField')
    ),
    'palettes' => array(
        '1' => array('showitem' => 'starttime, endtime, fe_group'),
        '2' => array('showitem' => 'sortby, sortby_dir, record_limit'),
        '3' => array('showitem' => 'gs_priority,gs_changeFreq')
    )
);

require_once(t3lib_extMgm::extPath('advanced_sitemaps') . 'class.tx_advancedsitemaps_tca.php');

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/tca.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/advanced_sitemaps/tca.php']);
}
?>