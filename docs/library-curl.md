# YiiTK

## Biblioteca Curl

Requisições HTTP utilizando a extensão do PHP `php-curl` e a biblioteca [PHP Curl Class: HTTP requests made easy](https://github.com/php-curl-class/php-curl-class).

Exemplo:

```php
<?php

    use yiitk\web\http\Curl;
    
    $curl = new Curl();
    
    $response = $curl->get('http://www.google.com.br');

```

Mais detalhes e exemplos de uso podem ser encontrados na página oficial da biblioteca [PHP Curl Class: HTTP requests made easy](https://github.com/php-curl-class/php-curl-class).

### `Métodos` adicionais disponíveis em `yiitk\web\http\Curl` e em `yiitk\web\http\MultiCurl`:

### Métodos Estáticos

- **isValidUrl(string $url, array $allowedSchemes = ['http', 'https'])**: verifica se uma URL é válida;

[&#171; Voltar](../README.md)
