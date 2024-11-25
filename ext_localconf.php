<?php

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News']['instagram2news'] = 'instagram2news';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\GeorgRinger\News\Domain\Model\News::class] = [
    'className' => \DSKZPT\Instagram2News\Domain\Model\NewsInstagram::class,
];

//\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
//    ->registerImplementation(
//        \GeorgRinger\News\Domain\Model\News::class,
//        \DSKZPT\Instagram2News\Domain\Model\NewsInstagram::class
//    );
