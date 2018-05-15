# YiiTK

## ActiveRecord

Para configurar sua classe `ActiveRecord` estenda `yiitk\db\ActiveRecord` para habilitar as funcionalidades de **YiiTK** em seus modelos `ActiveRecord`:

```php

    /**
     * Class ActiveRecord
     */
    class ActiveRecord extends \yiitk\db\ActiveRecord
    {
        
    }

```

Na classe base de ActiveRecord o YiiTK adiciona também a `trait` [yiitk\web\FlashMessagesTrait](widgets.md#alert).

[&#171; Voltar](../README.md)
