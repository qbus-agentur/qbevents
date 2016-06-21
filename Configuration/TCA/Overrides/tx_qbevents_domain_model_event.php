<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'qbevents',
    'tx_qbevents_domain_model_event'
);

