<?php

declare(strict_types=1);

namespace SvenPetersen\Instagram2News\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\Model\RecordStateFactory;
use TYPO3\CMS\Core\DataHandling\SlugHelper;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SlugService
{
    /**
     * Populate empty slugs in custom table
     *
     * Inspired by TYPO3\CMS\Install\Updates\PopulatePageSlugs
     * Workspaces are not respected here!
     */
    public static function populateEmptySlugsInCustomTable(string $tableName, string $fieldName): void
    {
        /* @var $connection \TYPO3\CMS\Core\Database\Connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);

        /* @var $queryBuilder \TYPO3\CMS\Core\Database\Query\QueryBuilder */
        $queryBuilder = $connection->createQueryBuilder();

        /* @var $querBuilder \TYPO3\CMS\Core\Database\Query\QueryBuilder */
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $statement = $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull($fieldName)
                )
            )
            ->addOrderBy('uid', 'asc')
            ->execute();

        $suggestedSlugs = [];

        $fieldConfig = $GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config'];
        $evalInfo = !empty($fieldConfig['eval']) ? GeneralUtility::trimExplode(',', $fieldConfig['eval'], true) : [];
        $hasToBeUniqueInSite = in_array('uniqueInSite', $evalInfo, true);
        $hasToBeUniqueInPid = in_array('uniqueInPid', $evalInfo, true);
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, $tableName, $fieldName, $fieldConfig);

        while ($record = $statement->fetch()) {
            $recordId = (int)$record['uid'];
            $pid = (int)$record['pid'];
            $languageId = (int)$record['sys_language_uid'];
            $pageIdInDefaultLanguage = $languageId > 0 ? (int)$record['l10n_parent'] : $recordId;
            $slug = $suggestedSlugs[$pageIdInDefaultLanguage][$languageId] ?? '';

            if (empty($slug)) {
                $slug = $slugHelper->generate($record, $pid);
            }

            $state = RecordStateFactory::forName($tableName)
                ->fromArray($record, $pid, $recordId);
            if ($hasToBeUniqueInSite && !$slugHelper->isUniqueInSite($slug, $state)) {
                $slug = $slugHelper->buildSlugForUniqueInSite($slug, $state);
            }
            if ($hasToBeUniqueInPid && !$slugHelper->isUniqueInPid($slug, $state)) {
                $slug = $slugHelper->buildSlugForUniqueInPid($slug, $state);
            }

            $connection->update(
                $tableName,
                [$fieldName => $slug],
                ['uid' => $recordId]
            );
        }
    }
}
