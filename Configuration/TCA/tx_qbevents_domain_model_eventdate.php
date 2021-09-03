<?php
return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate',
        'formattedLabel_userFunc' => \Qbus\Qbevents\Service\EventDateInlineLabelService::class . '->getInlineLabel',
        'label' => 'start',
        'hideTable' => true,
        'type' => 'frequency',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'endtime' => false,
            'starttime' => false,
        ),
        'searchFields' => 'start,end,is_full_day,frequency,frequency_count,frequency_until',
        'iconfile' => 'EXT:qbevents/Resources/Public/Icons/tx_qbevents_domain_model_eventdate.svg'
    ),
    'types' => array(
        '0' => array('showitem' => '--palette--;;date,--palette--;;basic,--palette--;;hidden'),
        '1' => array('showitem' => '--palette--;;date,--palette--;;basic,--palette--;LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.palette.recurrence;recurrence,--palette--;;hidden'),
        '2' => array('showitem' => '--palette--;;date,--palette--;;basic,--palette--;LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.palette.recurrence;recurrence,--palette--;;hidden'),
        '3' => array('showitem' => '--palette--;;date,--palette--;;basic,--palette--;LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.palette.recurrence;recurrence,--palette--;;weekly,--palette--;;hidden'),
        '4' => array('showitem' => '--palette--;;date,--palette--;;basic,--palette--;LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.palette.recurrence;recurrence,--palette--;;hidden'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
        'basic' => array('showitem' => 'frequency, is_full_day', 'canNotCollapse' => 1),
        'date' => array('showitem' => 'start, end', 'canNotCollapse' => 1),
        'recurrence' => array('showitem' => 'frequency_until, frequency_count', 'canNotCollapse' => 1),
        'weekly' => array('showitem' => 'frequency_weekdays', 'canNotCollapse' => 1),
        'hidden' => array('showitem' => 'hidden', 'isHiddenPalette' => true),
    ),
    'columns' => array(
        't3ver_label' => array(
            'label' => (version_compare(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch(), '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),
        'hidden' => (
            version_compare(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch(), '9.5', '>=') ?
            [
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'default' => 0,
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                            'invertStateDisplay' => true,
                        ],
                    ],
                ],
            ]
            :
            [
                'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                ],
            ]
        ),
        'start' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.start',
            'config' => array(
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime,int',
                'renderType' => 'inputDateTime'
            ),
        ),
        'end' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.end',
            'config' => array(
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime,int',
                'renderType' => 'inputDateTime'
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
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_qbevents_domain_model_event',
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
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.type.recurring', 1],
                ],
                'eval' => ''
            ),
        ),
        'base_date' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'frequency' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency.0', 0],
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency.1', 1],
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency.2', 2],
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency.3', 3],
                    ['LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency.4', 4],
                ],
                'eval' => ''
            ),
        ),
        'frequency_count' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_count',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,int'
            ),
        ),
        'frequency_until' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_until',
            'config' => array(
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime,int',
                'renderType' => 'inputDateTime'
            ),
        ),
        'frequency_weekdays' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays',
            'config' => array(
                'type' => 'check',
                'cols' => 1,
                'default' => 0,
                'items' => array(
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.1', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.2', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.3', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.4', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.5', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.6', ''),
                    array('LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_eventdate.frequency_weekdays.7', ''),
                )
            ),
        ),
        'recurrences' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.dates',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_qbevents_domain_model_eventdate',
                'foreign_field' => 'base_date',
                'maxitems' => 9999,
                'appearance' => [
                    'newRecordLinkTitle' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.dates.add',
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
                'eval' => ''
            ),
        ),
    ),
);
