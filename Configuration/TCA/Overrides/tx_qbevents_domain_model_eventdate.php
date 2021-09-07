<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

/* Ensure that EventDate::setPid() is able to update the pid. */
$GLOBALS['TCA']['tx_qbevents_domain_model_eventdate']['columns']['pid'] = [
    'config' => [
        'type' => 'passthrough',
    ]
];
