# YiiTK

## DB Migrations

Para utilizar as personalizações aplicadas em YiiTK para Migrations, siga os passos abaixo:

Na configuração do console, adicione:

```php

'controllerMap' => [
    'migrate' => [
        'class' => \yiitk\console\controllers\MigrateController::class
    ]
],

```

### Criando Nova Tabela

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

### Atualizando uma Tabela

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

### Removendo uma Tabela

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

### `Métodos` e `Propriedades` adicionais disponíveis em `yiitk\db\Migration`:

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

[&#171; Voltar](../README.md)
