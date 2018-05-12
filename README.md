# YiiTK

## Yii Framework version 2 Toolkit

YiiTK é uma caixa de ferramentas para o [Framework Yii2](https://www.yiiframework.com/).

[![Latest Stable Version](https://poser.pugx.org/yiitk/yiitk/v/stable)](https://packagist.org/packages/yiitk/yiitk)
[![Total Downloads](https://poser.pugx.org/yiitk/yiitk/downloads)](https://packagist.org/packages/yiitk/yiitk)
[![Latest Unstable Version](https://poser.pugx.org/yiitk/yiitk/v/unstable)](https://packagist.org/packages/yiitk/yiitk)
[![License](https://poser.pugx.org/yiitk/yiitk/license)](https://packagist.org/packages/yiitk/yiitk)
[![Monthly Downloads](https://poser.pugx.org/yiitk/yiitk/d/monthly)](https://packagist.org/packages/yiitk/yiitk)
[![Daily Downloads](https://poser.pugx.org/yiitk/yiitk/d/daily)](https://packagist.org/packages/yiitk/yiitk)
[![composer.lock](https://poser.pugx.org/yiitk/yiitk/composerlock)](https://packagist.org/packages/yiitk/yiitk)

## Instalação

O método indicado para a instalação dessa extensão é via [composer](http://getcomposer.org/download/).

Você pode executar

```
php composer.phar require --prefer-dist yiitk/yiitk "*"
```

ou adicionar

```
"yiitk/yiitk": "*"
```

à seção `require` do seu arquivo `composer.json`.

## Como utilizar o YiiTK

- [Módulo Base](#módulo-base)
- [DB Migrations](#db-migrations)
- [Helpers](#helpers)
- [Behaviors](#behaviors)
- [Validators](#validators)
- [Models](#models)
- [ActiveRecord](#activerecord)
- [Biblioteca: Enum](#biblioteca-enum)
- [Biblioteca: Curl e MultiCurl](#biblioteca-curl)
- [Biblioteca: SOAP](#biblioteca-soap)

### Módulo Base

É requerido que seja configurado o módulo base para o correto funcionamento do `YiiTK`, para fazê-lo, adicione na seção `modules` da sua configuração:

```php
'bootstrap' => [
    'yiitk'
],

'modules' => [
    'yiitk' => [
        'class' => \yiitk\Module::class
    ]
]
```

### DB Migrations

Para utilizar as personalizações aplicadas em YiiTK para Migrations, siga os passos abaixo:

Na configuração do console, adicione:

```php

'controllerMap' => [
    'migrate' => [
        'class' => \yiitk\console\controllers\MigrateController::class
    ]
],

```

#### Criando Nova Tabela

O padrão do nome da migration informa seu objetivo e o nome da tabela referenciada, neste caso: `create_` + `nome_da_tabela`
(sempre em minúsculas, separadas por underline). 

```bash
php yii migrate/create create_table_name
```

Exemplo da migration criada:

```php
<?php

    use yiitk\db\Migration;

    /**
     * Class m180207_115641_create_table_name
     */
    class m180207_115641_create_table_name extends Migration
    {
        /**
         * @inheritdoc
         */
        protected $tableName = 'table_name';

        /**
         * @inheritdoc
         */
        public function safeUp()
        {
            if (!$this->tableExists($this->findCurrentTableName())) {
                $this->createTable(
                    $this->findCurrentTableName(),
                    [
                        'id'        => $this->bigPrimaryKey($this->pkLength),
                        'createdAt' => $this->dateTime()->notNull(),
                        'updatedAt' => $this->dateTime()->notNull()
                    ]
                );
            }
        }
    }
```

#### Atualizando uma Tabela

O padrão do nome da migration informa seu objetivo e o nome da tabela referenciada, neste caso: `update_` + `nome_da_tabela`
(sempre em minúsculas, separadas por underline). 

```bash
php yii migrate/create update_table_name
```

Exemplo da migration criada:

```php
<?php

    use yiitk\db\Migration;

    /**
     * Class m180207_115653_update_table_name
     */
    class m180207_115653_update_table_name extends Migration
    {
        /**
         * @inheritdoc
         */
        protected $tableName = 'table_name';

        /**
         * @inheritdoc
         */
        public function safeUp()
        {
            if (!$this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->addColumn($this->findCurrentTableName(), 'fieldName', $this->string(255)->null()->after('id'));
            }
        }

        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            if ($this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->dropColumn($this->findCurrentTableName(), 'fieldName');
            }
        }
    }
```

Após finalizado, execute:

```bash
php yii migrate
```

#### Removendo uma Tabela

O padrão do nome da migration informa seu objetivo e o nome da tabela referenciada, neste caso: `drop_` + `nome_da_tabela`
(sempre em minúsculas, separadas por underline). 

```bash
php yii migrate/create drop_table_name
```

Exemplo da migration criada:

```php
<?php

    use yiitk\db\Migration;

    /**
     * Class m180207_115702_drop_table_name
     */
    class m180207_115702_drop_table_name extends Migration
    {
        /**
         * @inheritdoc
         */
        protected $tableName = 'table_name';

        /**
         * @inheritdoc
         */
        public function safeUp()
        {
            if ($this->tableExists($this->findCurrentTableName())) {
                $this->dropTable($this->findCurrentTableName());
            }
        }

        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            echo "m180207_115702_drop_table_name cannot be reverted.\n";

            return false;
        }
    }
```

Após finalizado, execute:

```bash
php yii migrate
```

#### `Métodos` e `Propriedades` adicionais disponíveis em `yiitk\db\Migration`:

#### Propriedades Gerais

- **onlyMySql**: define se é ou não obrigatório que o `driver` de `db` pode ser apenas `MySQL` (default: **true**);
- **pkLength**: padrão para campos `PK` (default: **20**);
- **fkLength**: padrão para campos `FK` (default: **20**);
- **tableName**: nome da tabela referenciada na Migration, não é necessária a inclusão de `{{%}}` para processar prefixos, a classe base já faz isso;
- **tableCharset**: Charset da tabela (apenas para `MySQL`; default: **utf8**);
- **tableCollate**: Collate da tabela (apenas para `MySQL`; default: **utf8_unicode_ci**);
- **tableEngine**: Engine da tabela (apenas para `MySQL`; default: **InnoDB**);
- **useMysqlInnoDbRowFormat**: Define se, quando `tableEngine` é igual a `InnoDB` se deve alterar o `ROW_FORMAT` (apenas para `MySQL`; default: **InnoDB**);
- **useMysqlInnoDbBarracudaFileFormat**: Define se, quando `tableEngine` é igual a `InnoDB` e `useMysqlInnoDbRowFormat` é igual a `true` se deve aplicar o `Barracuda File Format` (apenas para `MySQL`; default: **InnoDB**);
- **mysqlInnoDbRowFormat**: Define se, quando `tableEngine` é igual a `InnoDB` e `useMysqlInnoDbRowFormat` é igual a `true` qual é o `ROW_FORMAT`; opções disponíveis: `\yiitk\db::ROW_FORMAT_COMPACT`, `\yiitk\db::ROW_FORMAT_REDUNDANT`, `\yiitk\db::ROW_FORMAT_DYNAMIC` ou `\yiitk\db::ROW_FORMAT_COMPRESSED` (apenas para `MySQL`; default: **InnoDB**);

#### Métodos Gerais

- **tableExists**: verifica se uma tabela existe no banco de dados;
- **columnExists**: verifica se uma coluna existe no banco de dados;
- **fieldExists**: alias para `columnExists`;
- **addUniqueForeignKey**: adiciona uma `FK` com um índice `Unique`;
- **addForeignKey**: adiciona uma `FK` com um índice;
- **addForeignKeyWithoutIndex**: adiciona uma `FK` sem um índice;
- **dropForeignKey**: remove uma `FK` e seu índice (se existir);
- **viewExists**: verifica se uma `View` existe (disponível apenas para `MySQL`);
- **createView**: cria uma `View` (disponível apenas para `MySQL`);
- **dropView**: remove uma `View` (disponível apenas para `MySQL`); 
- **findTableName**: retorna o nome de uma tabela com o formato `{{%table_name}}`;
- **findCurrentTableName**: retorna o nome da tabela atual (formato: `{{%table_name}}`); 
- **currentTableName**: alias para `findCurrentTableName`;
- **findFieldName**: retorna o nome de um campo prefixado com o nome da tabela;
- **withTableName**: retorna o nome de um campo prefixado com o nome da tabela.

#### Métodos do SchemaBuilder

- **enum(array $options)**: cria um campo do tipo `ENUM` onde as opções disponíveis são passadas no array `$options` (disponível apenas para `MySQL`);
- **mediumText()**: cria um campo `MEDIUMTEXT` (disponível apenas para `MySQL`);
- **longText()**: cria um campo `LONGTEXT` (disponível apenas para `MySQL`);
- **tinyText()**: cria um campo `TINYTEXT` (disponível apenas para `MySQL`).

### Helpers

#### yiitk\helpers\ArrayHelper

Estende a classe `\yii\helpers\ArrayHelper` e adiciona o(s) seguinte(s) método(s):

- **asAssociative(array $items)**: retorna um array como associativo

#### yiitk\helpers\ConsoleHelper

Estende a classe `\yii\helpers\Console` sem adicionar nenhum método.

#### yiitk\helpers\DateTimeHelper

Estende a classe `\Carbon\Carbon` sem adicionar nenhum método. Mais informações sobre a biblioteca: https://carbon.nesbot.com/.

#### yiitk\helpers\FileHelper

Estende a classe `\yii\helpers\FileHelper` sem adicionar nenhum método.

#### yiitk\helpers\FormatConverterHelper

Estende a classe `\yii\helpers\FormatConverter` sem adicionar nenhum método.

#### yiitk\helpers\HtmlHelper

Estende a classe `\yii\helpers\Html` sem adicionar nenhum método.

#### yiitk\helpers\HtmlPurifierHelper

Estende a classe `\yii\helpers\HtmlPurifier` sem adicionar nenhum método.

#### yiitk\helpers\InflectorHelper

Estende a classe `\yii\helpers\Inflector` sem adicionar nenhum método.

#### yiitk\helpers\JsonHelper

Estende a classe `\yii\helpers\Json` sem adicionar nenhum método.

#### yiitk\helpers\MarkdownHelper

Estende a classe `\yii\helpers\Markdown` sem adicionar nenhum método.

#### yiitk\helpers\MaskHelper

Estende a classe `\yiitk\helpers\StringHelper` e adiciona o(s) seguinte(s) método(s):

- **mask(string $string, string $mask, string $empty = '')**: retorna uma string formatada com a máscara informada (máscaras padrões: `cpf`, `cnpj`, `zipcode`, `credit-card`). Ex.: `MaskHelper::mask('00000000000', 'cpf', '-')` ou `MaskHelper::mask('0000', '##-##', '-')`

#### yiitk\helpers\NumberHelper

Métodos disponíveis:

- **justNumbers(string $content = '')**: retorna uma string contendo apenas digitos;

#### yiitk\helpers\SlugHelper

Estende a classe `\yiitk\helpers\StringHelper` e adiciona o(s) seguinte(s) método(s):

- **asSlugs(array $items, string $method = SlugHelper::SLUG_METHOD_SINGLE)**: converte o conteúdo de um `array` em `slugs` 
- **convert($value = '', $spaces = '-', $case = MB_CASE_LOWER)**: converte uma `string` em `slug`

#### yiitk\helpers\StringHelper

- **justNumbers(string $content = '')**: retorna a `string` com apenas digitos
- **justLetters(string $content = '')**: retorna a `string` com apenas letras
- **compare(string $originalValue = '', string $targetValue = '')**: compara dois valores retornando `true` ou `false`
- **asSlug(string $value = '', string $spaces = '-', string $case = MB_CASE_LOWER)**: retorna uma `string` como `slug`
- **convertCase(string $string, string $mode = StringHelper::CASE_UPPER)**: converte uma `string` em minúscula, maiúscula ou como título
- **toLowerCase(string $string)**: converte uma `string` para minúscula
- **toUpperCase(string $string)**: converte uma `string` para maiúscula
- **generateRandomString(integer $length = 0, integer $upper = 0, integer $lower = 0, integer $digit = 0, integer $special = 0)**: gera uma `string` randômica
- **obfuscateEmail(string $email)**: ofusca um endereço de e-mail
- **removeAccents(string $string)**: remove os acentos de uma `string`

#### yiitk\helpers\UrlHelper

Estende a classe `\yii\helpers\Url` sem adicionar nenhum método.

#### yiitk\helpers\VarDumperHelper

Estende a classe `\yii\helpers\VarDumper` sem adicionar nenhum método.

### Behaviors

Documentação pendente...

### Validators

Documentação pendente...

### Models

Para configurar sua classe `Model` estenda `yiitk\base\Model` para habilitar as funcionalidades de **YiiTK** em seus modelos `Model`:

```php

    /**
     * Class Model
     */
    class Model extends \yiitk\base\Models
    {
        
    }

```

### ActiveRecord

Para configurar sua classe `ActiveRecord` estenda `yiitk\db\ActiveRecord` para habilitar as funcionalidades de **YiiTK** em seus modelos `ActiveRecord`:

```php

    /**
     * Class ActiveRecord
     */
    class ActiveRecord extends \yiitk\db\ActiveRecord
    {
        
    }

```

### Generators: Gii

#### Enum

Documentação pendente...

### Biblioteca Enum

Documentação pendente...

### Biblioteca Curl

Requisições HTTP utilizando a extensão do PHP `php-curl` e a biblioteca [PHP Curl Class: HTTP requests made easy](https://github.com/php-curl-class/php-curl-class).

Exemplo:

```php
<?php

    use yiitk\web\http\Curl;
    
    $curl = new Curl();
    
    $response = $curl->get('http://www.google.com.br');

```

Mais detalhes e exemplos de uso podem ser encontrados na página oficial da biblioteca [PHP Curl Class: HTTP requests made easy](https://github.com/php-curl-class/php-curl-class).

#### `Métodos` adicionais disponíveis em `yiitk\web\http\Curl` e em `yiitk\web\http\MultiCurl`:

#### Métodos Estáticos

- **isValidUrl(string $url, array $allowedSchemes = ['http', 'https'])**: verifica se uma URL é válida;

### Biblioteca SOAP

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

## Licença

**yiitk** está liberado sob a `BSD 3-Clause License`. Leia o arquivo `LICENSE.md` para mais detalhes.

![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)
