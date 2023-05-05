<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Event Management',
    'description' => '',
    'category' => '',
    'author' => 'Benjamin Franzke',
    'author_email' => 'bfr@qbus.de',
    'state' => 'stable',
    'version' => '0.20.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '11.5.0-12.4.99',
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
