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
