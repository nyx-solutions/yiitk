# YiiTK

## Controllers

Para configurar sua classe `Controller` estenda `yiitk\web\Controller` (para aqueles do tipo `Web`), `yiitk\rest\Controller` 
(para aqueles do tipo `REST`) e `yiitk\console\Controller` (para aqueles do tipo `Console`) para habilitar as 
funcionalidades de **YiiTK** em seus `Controllers`:

```php

    /**
     * Class AppController
     */
    class AppController extends \yiitk\web\Controller
    {
        
    }

```

Na classe base de Controllers Web o YiiTK adiciona também a `trait` [yiitk\web\FlashMessagesTrait](widgets.md#alert).

```php

    /**
     * Class RestController
     */
    class RestController extends \yiitk\rest\Controller
    {
        
    }

```

```php

    /**
     * Class ActiveRestController
     */
    class ActiveRestController extends \yiitk\rest\ActiveController
    {
        
    }

```

```php

    /**
     * Class ConsoleController
     */
    class ConsoleController extends \yiitk\console\Controller
    {
        
    }

```

[&#171; Voltar](../README.md)
