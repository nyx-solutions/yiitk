# YiiTK

## Bibliotecas: Enum

Implementação do tipo de dados ENUM para Yii Framework 2.

Obs.: essa biblioteca foi inspirada na biblioteca [Enum Extension for Yii2](https://github.com/yii2mod/yii2-enum).

### Novo Enum

Para criar um novo tipo Enum basta você criar uma classe estendendo a classe `\yiitk\enum\base\BaseEnum` (pode ser criada em `common\models\enum`, 
por exemplo):

```php
    namespace common\models\enum;

    use yiitk\enum\base\BaseEnum;

    /**
     * Class BooleanEnum
     *
     * @property string $yes
     * @property string $no
     *
     * @property bool   $isYes
     * @property bool   $isNo
     *
     * @method static   yes
     * @method static   no
     */
    class BooleanEnum extends BaseEnum
    {
        const YES = 'yes';
        const NO  = 'no';
        
        /**
         * @var bool
         */
        public static $useI18n = true; // Declara se utiliza ou não i18n

        /**
         * @var string
         */
        public static $preposition = 'is'; // Declara o prefixo das propriedades mágicas (neste caso, serão geradas: `isYes` e `isNo`); se fosse alterado para 'has', seriam geradas `hasYes` e `hasNo`.

        /**
         * {@inheritdoc}
         */
        public static function defaultValue()
        {
            return self::NO;
        }

        /**
         * {@inheritdoc}
         */
        protected static function labels()
        {
            return [
                self::YES => 'Yes',
                self::NO  => 'No'
            ];
        }
    }
```

### Internacionalização

A classe base irá buscar automaticamente um arquivo para a internacionalização dos labels da classe enum, no caso do exemplo acima, 
dentro da pasta da classe atual crie uma pasta `messages` e dentro crie as pastas conforme o modelo de tradução, neste caso, criamos
uma pasta  em `./common/models/enum/messages/pt-BR/boolean-enum.php` com a estrutura abaixo:

```php
    return [
        'Yes' => 'Sim',
        'No'  => 'Não',
    ];
```

### No Modelo

Para configurar um tipo ENUM no seu modelo é muito simples, basta que seu modelo estenda `\yiitk\db\ActiveRecord` ou que inclua a `trait` `\yiitk\enum\base\EnumTrait` e seguir o exemplo abaixo:

```php
    namespace common\models;

    use common\models\enum\BooleanEnum;

    /**
     * File Model
     *
     * @property integer $id
     * @property string  $active
     * @property string  $createdAt
     * @property string  $updatedAt
     */
    class Table extends \yiitk\db\ActiveRecord
    {
        /**
         * {@inheritdoc}
         */
        public static function tableName()
        {
            return '{{%table}}';
        }

        /**
         * {@inheritdoc}
         */
        public function rules()
        {
            return [
                [['active'], 'required'],
                [['active'], 'in' => BooleanEnum::range()]
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function enums()
        {
            return [
                [['active'], 'enumClass' => BooleanEnum::class, 'default' => BooleanEnum::no()]
            ];
        }
    }
```

No exemplo acima você declarou que o atributo `active` deve ter um valor dentro do range do Enum ('yes', 'no') usando `[['active'], 'in' => BooleanEnum::range()]` e
declarou que o mesmo atributo é uma instância de `BooleanEnum` e que ele deve ter o valor `default` de `BooleanEnum::no()` (um método mágico para declarar o valor de BooleanEnum::NO).

Dessa forma, no evento `afterFind` o atributo será carregado como uma instância de `BooleanEnum` e você poderá utilizar todos os seus métodos para verificar ou alterar seu valor; 
veja alguns exemplos:

#### Atributos e Métodos Mágicos

- `$model->active->value` contém o valor atual do atributo `active`;
- `$model->active->label` contém o label do valor atual do atributo `active`;
- `$model->active->slug` contém o slug do valor atual do atributo `active`;
- `$model->active->yes` `string` que retorna o valor da opção `yes`;
- `$model->active->no` `string` que retorna o valor da opção `no`;
- `$model->active->isYes` `booleano` que indica se o valor atual do atributo é `yes`;
- `$model->active->isNo` `booleano` que indica se o valor atual do atributo é `no`;
- `$model->active = BooleanEnum::no()` altera o valor atual do atributo `active` para `no`;
- `$model->active = BooleanEnum::NO` altera o valor atual do atributo `active` para `no`;
- `$model->active = new BooleanEnum(BooleanEnum::no())` altera o valor atual do atributo `active` para `no`;
- `$model->active = new BooleanEnum(BooleanEnum::NO)` altera o valor atual do atributo `active` para `no`;

### Na Migration

Para utilizar o tipo de dados ENUM (atualmente apenas para MySQL) em suas migrations você deve estender a classe `yiitk\db\Migration` e 
seguir o exemplo abaixo:

```php
if (!$this->tableExists($this->currentTableName())) {
    $this->createTable(
        $this->tableName,
        [
            'id'        => $this->bigPrimaryKey($this->pkLength),
            'active'    => $this->enum(\yiitk\enum\BooleanEnum::range())->notNull()->defaultValue(\yiitk\enum\BooleanEnum::NO),
            'createdAt' => $this->dateTime()->notNull(),
            'updatedAt' => $this->dateTime()->notNull()
        ]
    );
}
```

ou

```php
if (!$this->tableExists($this->currentTableName())) {
    $this->createTable(
        $this->tableName,
        [
            'id'        => $this->bigPrimaryKey($this->pkLength),
            'active'    => $this->enum(['yes', 'no'])->notNull()->defaultValue('no'),
            'valid'     => $this->enum([1, 0])->notNull()->defaultValue(0),
            'createdAt' => $this->dateTime()->notNull(),
            'updatedAt' => $this->dateTime()->notNull()
        ]
    );
}
```

### Métodos Disponíveis

- **BooleanEnum::createByKey(string $name)**: Cria uma nova instância de `BooleanEnum` com o nome da constante passada, ex: `BooleanEnum::createByKey('YES')`;
- **BooleanEnum::createByValue(string $value)**: Cria uma nova instância de `BooleanEnum` com o valor da constante passada, ex: `BooleanEnum::createByKey('yes')`;
- **BooleanEnum::defaultValue()**: Retorna o valor default declarado na classe `BooleanEnum`;
- **BooleanEnum::listData()**: Retorna um `array` de `chave` => `label` com todos os itens disponíveis na classe;
- **BooleanEnum::range()**: Retorna um array contendo todos os valores válidos de `BooleanEnum`;
- **BooleanEnum::findValueByKey(string $value)**: Encontra um valor com base no valor informado;
- **BooleanEnum::findLabel(string $value)**: Encontra um label com base no valor informado;
- **BooleanEnum::findSlug(string $value)**: Encontra um slug com base no valor informado;
- **BooleanEnum::findConstantsByKey()**: Retorna um `array` mapeado pelo nome das constantes;
- **BooleanEnum::findConstantsByValue()**: Retorna um `array` mapeado pelo valor das constantes;
- **BooleanEnum::isValidKey(string $name)**: Retorna um `booleano` informando se uma chave (constante) é valida;
- **BooleanEnum::isValidValue(string $name)**: Retorna um `booleano` informando se um valor é valido.

### Gerando Código com o Gii

Para habilitar a geração de Enums via Gii, utilize o exemplo abaixo:

```php
if (!YII_ENV_TEST) {
    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class'      => \yii\gii\Module::class,
        'generators' => [
            'enum' => [
                'class' => \yiitk\gii\generators\enum\Generator::class
            ]
        ]
    ];
}
```

### Enums disponíveis no Bundle

- `\yiitk\enum\BooleanEnum`;
- `\yiitk\enum\GenderEnum`;
- `\yiitk\enum\StatusEnum`;

[&#171; Voltar](../README.md)
