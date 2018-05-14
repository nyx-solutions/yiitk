# YiiTK

## Módulo Base

É requerido que seja configurado o módulo base para o correto funcionamento do `YiiTK`, para fazê-lo, adicione na seção `modules` da sua configuração:

```php
'bootstrap' => ['yiitk'],

'modules' => [
    'yiitk' => [
        'class' => \yiitk\Module::class
    ]
]
```

[&#171; Voltar](../README.md)
