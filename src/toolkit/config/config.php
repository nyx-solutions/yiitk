<?php

    return [
        'components' => [
            'fileManager' => [
                'class'           => \yiitk\file\FileManager::class,
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
