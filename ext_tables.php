<?php
if (!defined('TYPO3_MODE')) {
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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Events',
    'Event System'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['qbevents_events'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'qbevents_events',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/Events.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'EventOverview',
    'Event Overview'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['qbevents_eventoverview'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'qbevents_eventoverview',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/Overview.xml'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'qbevents');
