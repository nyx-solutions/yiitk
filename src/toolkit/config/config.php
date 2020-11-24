<?php

    use yiitk\file\FileManager;

    return [
        'components' => [
            'fileManager' => [
                'class'           => FileManager::class,
                'fileTable'       => '{{%file}}',
                'useBigIntegerPk' => true,
                'useBigIntegerFk' => true,
                'pkLength'        => 20,
                'fkLength'        => 20,
                'fkFieldSuffix'   => 'Id'
            ]
        ],

        'params' => require(__DIR__.'/params.php')
    ];
