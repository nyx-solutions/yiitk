# YiiTK

## Envio de E-mail por Job: SwiftMailer: QueuedMailer

Para habilitar o envio de e-mails via jobs, siga os passos abaixo:

Adicione o componente `yii-queue` em seu arquivo de configuração (assim como no `bootstrap`):

```php
'bootstrap' => ['yiitk', 'queue'],

'components' => [
    'queue' => [
        'class'     => \yii\queue\db\Queue::class,
        'db'        => 'db',
        'tableName' => '{{%queue}}',
        'channel'   => 'default',
        'mutex'     => \yii\mutex\MysqlMutex::class,
        'as log'    => \yii\queue\LogBehavior::class
    ],
],
```

E em caso de utilizar `\yii\queue\db\Queue`, na configuração do console, adicione:

```php
'controllerMap' => [
    'migrate' => [
        'class' => \yiitk\console\controllers\MigrateController::class,
        'migrationNamespaces' => [
            //...
            'yii\queue\db\migrations',
            //...
        ]
    ]
],

```

Para mais informações sobre a extensão, consulte: https://github.com/yiisoft/yii2-queue.

[&#171; Voltar](../README.md)
