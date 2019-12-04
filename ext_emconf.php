<?php
declare(strict_types = 1);
$EM_CONF[$_EXTKEY] = [
    'title'            => 'ConfUse',
    'description'      => 'a (maybe) smarter way to write your TCA',
    'category'         => 'Utilities',
    'author'           => 'Volker Kemeter',
    'author_email'     => 'v.kemeter@supseven.at',
    'author_company'   => 'https://www.supseven.at',
    'state'            => 'stable',
    'uploadfolder'     => '0',
    'createDirs'       => '',
    'clearCacheOnLoad' => 1,
    'version'          => '1.0.0',
    'constraints'      => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Supseven\\Confuse\\' => 'Classes',
        ],
    ],
    'autoload-dev' => [
        'psr-4' => [
            'Supseven\\Confuse\\Tests\\' => 'Tests',
        ],
    ],
];
