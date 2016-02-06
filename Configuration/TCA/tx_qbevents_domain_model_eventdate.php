<?php
return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate',
        'formattedLabel_userFunc' => \Qbus\Qbevents\Service\EventDateInlineLabelService::class . '->getInlineLabel',
        'label' => 'start',
        'hideTable' => true,
        'type' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'searchFields' => 'start,end,is_full_day,type',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('qbevents') . 'Resources/Public/Icons/tx_qbevents_domain_model_eventdate.svg'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, start, end, is_full_day, type',
    ),
    'types' => array(
        '1' => array('showitem' => '--palette--;;basic,--palette--;;date, --palette--;;hidden'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
        'basic' => array('showitem' => 'type, is_full_day', 'canNotCollapse' => 1),
        'date' => array('showitem' => 'start, end', 'canNotCollapse' => 1),
        'hidden' => array('showitem' => 'hidden, sys_language_uid, l10n_parent, l10n_diffsource', 'isHiddenPalette' => true),
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
                ),
            ),
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_qbevents_domain_model_eventdate',
                'foreign_table_where' => 'AND tx_qbevents_domain_model_eventdate.pid=###CURRENT_PID### AND tx_qbevents_domain_model_eventdate.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),
        'hidden' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            ),
        ),
        'start' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.start',
            'config' => array(
                'type' => 'input',
                'dbType' => 'datetime',
                'size' => 12,
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00',
                'eval' => 'datetime'
            ),
        ),
        'end' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.end',
            'config' => array(
                'type' => 'input',
                'dbType' => 'datetime',
                'size' => 12,
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00',
                'eval' => 'datetime'
            ),
        ),
        'is_full_day' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.is_full_day',
            'config' => array(
                'type' => 'check',
                'default' => 0,
                'eval' => ''
            ),
        ),
        'event' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.type.standard', 0],
                    //['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.type.repeat', 1],
                ],
                'eval' => ''
            ),
        ),
        'base_date' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
    ),
);
