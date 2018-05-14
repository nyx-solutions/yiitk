<?php

    return [
        'components' => [
            'queue' => [
                'class'     => \yii\queue\db\Queue::class,
                'db'        => 'db',
                'tableName' => '{{%queue}}',
                'channel'   => 'default',
                'mutex'     => \yii\mutex\MysqlMutex::class,
                'as log'    => \yii\queue\LogBehavior::class
            ],

            'cache' => [
                'class' => \yii\caching\FileCache::class
            ]
        ],

        'params' => require(__DIR__.'/params.php')
    ];
