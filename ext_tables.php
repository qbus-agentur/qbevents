<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_qbevents_domain_model_event');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_qbevents_domain_model_event',
    'EXT:qbevents/Resources/Private/Language/locallang_csh_tx_qbevents_domain_model_event.xlf'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_qbevents_domain_model_eventdate');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_qbevents_domain_model_eventdate',
    'EXT:qbevents/Resources/Private/Language/locallang_csh_tx_qbevents_domain_model_eventdate.xlf'
);
