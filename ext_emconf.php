<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Event Management',
    'description' => '',
    'category' => '',
    'author' => 'Benjamin Franzke',
    'author_email' => 'bfr@qbus.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.5.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-8.7.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    'autoload' => array(
        'psr-4' => array(
            'Qbus\\Qbevents\\' => 'Classes',
        ),
        'classmap' => array(
            'Resources/Private/PHP',
        ),
    ),
);
