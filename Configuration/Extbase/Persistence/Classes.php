<?php

declare(strict_types=1);

return [
    \GeorgRinger\News\Domain\Model\News::class => [
        'subclasses' => [
            1672764382 => \SvenPetersen\Instagram2News\Domain\Model\NewsInstagram::class,
        ],
    ],
    \SvenPetersen\Instagram2News\Domain\Model\NewsInstagram::class => [
        'tableName' => 'tx_news_domain_model_news',
        'recordType' => 1672764382,
    ],
];
