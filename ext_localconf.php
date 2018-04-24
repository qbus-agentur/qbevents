<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbus.' . $_EXTKEY,
    'Events',
    array(
        'EventDate' => 'list, show, teaser, calendar',

    ),
    // non-cacheable actions
    array(
        'EventDate' => 'list, teaser, calendar',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbus.' . $_EXTKEY,
    'EventOverview',
    array(
        'Event' => 'list, show',

    ),
    // non-cacheable actions
    array(
        'Event' => 'list, show',
    )
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Qbus\Qbevents\Hooks\DataHandlerHooks::class;
