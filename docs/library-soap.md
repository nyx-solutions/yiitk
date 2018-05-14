# YiiTK

## Biblioteca SOAP

`yiitk\web\http\soap\HttpSoapClient` é um wrapper para uso da extensão do PHP `php-soap`.

Para utilizá-la, basta configurará-la como um `Component` na seção `components` do seu arquivo de configuração:

```php
<?php

    return [
        //...
        'components' => [
            'soap' => [
                'class' => \yiitk\web\http\soap\HttpSoapClient::class,
                'endpoint' => 'http://api.endpoint.com/soap',
                'options' => [
                    'trace'              => true,
                    'cache_wsdl'         => WSDL_CACHE_NONE,
                    'connection_timeout' => 500000,
                    'keep_alive'         => true
                ]
            ]
        ]
        //...
    ];

```

Para utilizar o componente, basta carregá-lo na seção [bootstrap](https://www.yiiframework.com/doc/guide/2.0/en/structure-application-components#bootstrapping-components) do arquivo de configuração ou da forma abaixo (o componente será uma instância de [\SoapClient](http://php.net/manual/pt_BR/class.soapclient.php)):

```php
<?php

    /** @var object $soap */
    $soap = \Yii::$app->get('soap');
    
    $response = $soap->remoteMethod([]);
    
```

Você ainda pode utilizar sem criar um componente, conforme abaixo:

```php
<?php

    use yiitk\web\http\soap\HttpSoapClient;

    /** @var object $soap */
    $soap = new HttpSoapClient(['endpoint' => 'http://api.endpoint.com/soap', 'options' => ['trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'connection_timeout' => 500000, 'keep_alive' => true]]);
    
    $response = $soap->remoteMethod([]);
```
