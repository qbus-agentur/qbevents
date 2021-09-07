<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbevents',
    'Events',
    array(
        \Qbus\Qbevents\Controller\EventDateController::class => 'list, show, teaser, calendar',

    ),
    // non-cacheable actions
    array(
        \Qbus\Qbevents\Controller\EventDateController::class => 'list, teaser, calendar',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Qbevents',
    'EventOverview',
    array(
        \Qbus\Qbevents\Controller\EventController::class => 'list, show',

    ),
    // non-cacheable actions
    array(
        \Qbus\Qbevents\Controller\EventController::class => 'list, show',
    )
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Qbus\Qbevents\Hooks\DataHandlerHooks::class;
