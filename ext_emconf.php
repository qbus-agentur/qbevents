<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Event Management',
    'description' => '',
    'category' => '',
    'author' => 'Benjamin Franzke',
    'author_email' => 'bfr@qbus.de',
    'state' => 'stable',
    'version' => '0.11.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4.0-10.4.99',
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
