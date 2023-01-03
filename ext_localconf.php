<?php

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News']['instagram2news'] = 'instagram2news';

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
    ->registerImplementation(
        \GeorgRinger\News\Domain\Model\News::class,
        \SvenPetersen\Instagram2News\Domain\Model\NewsInstagram::class
    );
