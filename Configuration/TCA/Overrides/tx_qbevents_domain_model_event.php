<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'qbevents',
    'tx_qbevents_domain_model_event',
    'categories',
    array(
        'l10n_mode' => 'exclude',
    ),
    true
);

/* Ensure that EventDate->setPid() is able to update the pid. */
$GLOBALS['TCA']['tx_qbevents_domain_model_event']['columns']['pid'] = [
    'config' => [
        'type' => 'passthrough',
    ]
];

/* Foreign match fields, required if a FileReference is created via ResourceFactory */
$GLOBALS['TCA']['tx_qbevents_domain_model_event']['columns']['image']['config']['foreign_match_fields'] = [
    'fieldname' => 'image',
    'tablenames' => 'tx_qbevents_domain_model_event',
    'table_local' => 'sys_file',
];
