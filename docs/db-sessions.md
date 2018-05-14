# YiiTK

## Sessões via DB

Nesta versão é possível habilitar a configuração das tabelas no banco de dados para sessão, sendo uma `genérica`, uma para `backend`, 
uma para `frontend` e uma `api`, sendo possível configurar quais serão executadas ao inicializar o YiiTK, conforme abaixo:

```php
'modules' => [
    'yiitk' => [
        'class'     => \yiitk\Module::class,
        'sessionDb' => [
            'db'         => true,
            'dbFrontend' => true,
            'dbBackend'  => true,
            'dbApi'      => false
        ]
    ]
]
```

E na configuração do console, adicione:

```php
'controllerMap' => [
    'migrate' => [
        'class' => \yiitk\console\controllers\MigrateController::class,
        'migrationNamespaces' => [
            //...
            'yiitk\db\migrations\session',
            //...
        ]
    ]
],

```

Por fim, no arquivo de configuração de ambiente, adicione:

```php
'session' => [
    'name'         => 'app-frontend',
    'class'        => \yii\web\DbSession::class,
    'sessionTable' => '{{%session_frontend}}', // ou {{%session}} ou {{%session_backend}} ou {{%session_api}}
    'timeout'      => (3600 * 4),
    'cookieParams' => ['lifetime' => (7 * 24 * 60 * 60)],
    'useCookies'   => true
],
```

[&#171; Voltar](../README.md)
