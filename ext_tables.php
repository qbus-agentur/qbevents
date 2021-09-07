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

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'qbevents',
    'Events',
    'Event Dates'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['qbevents_events'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'qbevents_events',
    'FILE:EXT:' . 'qbevents' . '/Configuration/FlexForms/Events.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'qbevents',
    'EventOverview',
    'Events'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['qbevents_eventoverview'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'qbevents_eventoverview',
    'FILE:EXT:' . 'qbevents' . '/Configuration/FlexForms/Overview.xml'
);
