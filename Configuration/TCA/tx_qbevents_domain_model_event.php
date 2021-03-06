<?php
return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event',
        'label' => 'title',
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
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'title,location,teaser,description,dates,image,files,external_url',
        'iconfile' => 'EXT:qbevents/Resources/Public/Icons/tx_qbevents_domain_model_event.svg'
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, location, teaser, description, image, files, external_url, dates',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;1, title, location, teaser, description, image, files, dates, external_url, --div--;' . (version_compare(TYPO3_branch, '7.4', '>=') ? 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf' : 'LLL:EXT:cms/locallang_ttc.xlf') . ':tabs.access, starttime, endtime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => (version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [(version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.allLanguages', -1],
                    [(version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.default_value', 0],
                ],
                'default' => 0,
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ]
        ],
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => (version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_qbevents_domain_model_event',
                'foreign_table_where' => 'AND tx_qbevents_domain_model_event.pid=###CURRENT_PID### AND tx_qbevents_domain_model_event.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        't3ver_label' => array(
            'label' => (version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            )
        ),
        'hidden' => (
            version_compare(TYPO3_branch, '9.5', '>=') ?
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
                'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                ],
            ]
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => (version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime,int',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => (version_compare(TYPO3_branch, '9.5', '>=') ? 'LLL:EXT:core/Resources/Private/Language' : 'LLL:EXT:lang') . '/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime,int',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ),
            ),
        ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'location' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.location',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'teaser' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.teaser',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ),
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]',
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.description',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ),
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts_css]',
        ),
        'image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                array(
                    'appearance' => array(
                        'createNewRelationLinkTitle' => (version_compare(TYPO3_branch, '7.4', '>=') ? 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf' : 'LLL:EXT:cms/locallang_ttc.xlf') . ':images.addFileReference'
                    ),
                    'foreign_types' => array(
                        '0' => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
                            'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                        )
                    ),
                    'maxitems' => 1
                ),
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ),
        'dates' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.dates',
            'l10n_mode' => 'exclude',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_qbevents_domain_model_eventdate',
                'foreign_field' => 'event',
                'foreign_match_fields' => array(
                    'base_date' => 0,
                ),
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
        'files' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.files',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'files',
                [
                    'appearance' => [
                        'showPossibleLocalizationRecords' => 1,
                        'showRemovedLocalizationRecords' => 1,
                        'showAllLocalizationLink' => 1,
                        'showSynchronizationLink' => 1
                    ],
                    'inline' => [
                        'inlineOnlineMediaAddButtonStyle' => 'display:none'
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'files',
                        'tablenames' => 'tx_qbevents_domain_model_event',
                        'table_local' => 'sys_file',
                    ],
                ]
            )
        ),
        'external_url' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:qbevents/Resources/Private/Language/locallang_db.xlf:tx_qbevents_domain_model_event.external_url',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 50,
                'max' => 1024,
                'eval' => 'trim',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        ],
                    ],
                ],
                'softref' => 'typolink'
            ]
        ),
    ),
);
