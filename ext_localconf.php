<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbus.' . $_EXTKEY,
    'Events',
    array(
        'EventDate' => 'list, show',

    ),
    // non-cacheable actions
    array(
        'EventDate' => 'list',
    )
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Qbus\Qbevents\Hooks\DataHandlerHooks::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \Qbus\Qbevents\Hooks\DataHandlerHooks::class;
