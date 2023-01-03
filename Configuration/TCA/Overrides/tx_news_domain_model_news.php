<?php

defined('TYPO3') or die();

$GLOBALS['TCA']['tx_news_domain_model_news']['columns']['type']['config']['items']['1672764382'] =
    ['Instagram', 1672764382];

$GLOBALS['TCA']['tx_news_domain_model_news']['types']['1672764382'] = $GLOBALS['TCA']['tx_news_domain_model_news']['types']['0'];

$fields = [
    'instagram_id' => [
        'exclude' => 1,
        'label' => 'Instagram ID',
        'config' => [
            'type' => 'input',
            'size' => 30,
        ],
    ],
    'posted_by' => [
        'exclude' => 1,
        'label' => 'Posted by',
        'config' => [
            'type' => 'input',
            'size' => 30,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tx_news_domain_model_news',
    'tx_instagram2news_fields',
    'instagram_id, posted_by'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $fields);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    '--div--;Instagram,--palette--;LLL:EXT:instagram2news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.palette.tx_instagram2news_fields;tx_instagram2news_fields',
    '1672764382',
    ''
);
