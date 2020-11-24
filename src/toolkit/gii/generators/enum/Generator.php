<?php

    namespace yiitk\gii\generators\enum;

    use Yii;
    use yii\gii\CodeFile;
    use yiitk\helpers\InflectorHelper;
    use yiitk\helpers\StringHelper;
    use yiitk\enum\base\BaseEnum;

    /**
     * This generator will generate a enum and one or a few action view files.
     *
     * @property string $name
     *
     */
    class Generator extends \yii\gii\Generator
    {
        /**
         * @var string the enum class name
         */
        public string $enumClass = '';

        /**
         * @var string the base class of the enum
         */
        public string $baseClass = BaseEnum::class;

        /**
         * @var string list of constants
         */
        public string $constants = '';

        /**
         * @var string the default constant of the class
         */
        public string $defaultConstant = '';

        /**
         * @var string the languages Ids to generate i18n files.
         */
        public string $languages = 'pt-BR';

        //region Rulesets
        /**
         * {@inheritdoc}
         */
        public function rules()
        {
            return array_merge(parent::rules(), [
                [['enumClass', 'constants', 'defaultConstant', 'baseClass'], 'filter', 'filter' => 'trim'],
                [['enumClass', 'baseClass', 'languages', 'constants', 'defaultConstant'], 'required'],
                ['enumClass', 'match', 'pattern' => '/^[\w\\\\]*Enum$/', 'message' => 'Only word characters and backslashes are allowed, and the class name must end with "Enum".'],
                ['enumClass', 'validateNewClass'],
                ['baseClass', 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
                [['languages', 'constants', 'defaultConstant'], 'safe'],
                [['constants'], 'validateDefaultConstant'],
                [['defaultConstant'], 'validateDefaultConstant'],
            ]);
        }

        /**
         * An inline validator that checks if the attribute value is valid.
         *
         * @param string $attribute the attribute being validated
         *
         * @noinspection PhpUnused
         */
        public function validateConstants(string $attribute): void
        {
            $constants = $this->findConstants();

            if (count($constants) <= 0) {
                $this->addError($attribute, "No valid constants found.");
            }
        }

        /**
         * An inline validator that checks if the attribute value is valid.
         *
         * @param string $attribute the attribute being validated
         */
        public function validateDefaultConstant(string $attribute): void
        {
            $constants = $this->findConstants();

            if (!isset($constants[$this->defaultConstant]) || !is_array($constants[$this->defaultConstant])) {
                $this->addError($attribute, "The default constant must be listed in the constants definitions.");
            }
        }
        //endregion

        //region Attribute Labels and Hints
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function attributeLabels()
        {
            return [
                'baseClass'       => 'Base Class',
                'enumClass'       => 'Enum Class',
                'languages'       => 'Languages',
                'constants'       => 'Constants',
                'defaultConstant' => 'Default Constant',
            ];
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function hints()
        {
            return [
                'enumClass' => 'This is the name of the Enum class to be generated. You should
                provide a fully qualified namespaced class (e.g. <code>app\enum\BooleanEnum</code>),
                and class name should be in CamelCase ending with the word <code>Enum</code>.',

                'constants' => 'Provide one or multiple action Constants to generate the options in the Enum Class. 
                Separate multiple constants with new lines and the constant details with the following template: 
                <code>{CONSTANT_KEY}</code>, <code>{constant_value}</code>, <code>{Constant Label}</code>. 
                Constant keys must follow the PHP conventions to constants (<code>CONSTANT_KEY</code>), the constant 
                value must be an string (using the snake case convention: <code>constant_value</code>) or integer 
                and the constant label must be an string using the base value for internationalization. For example:
                <ul>
                    <li><code>CONSTANT_NAME, constant_value, Constant Description</code></li>
                    <li><code>CONSTANT_NAME, 1, Constant Description</code></li>
                </ul>',

                'defaultConstant' => 'Provide the default <code>CONSTANT_KEY</code>.',

                'languages' => 'Provide one or multiple language IDs to generate <code>i18n</code> 
                file(s) in the Enum subsystem. Separate multiple language IDs with commas or spaces. 
                Example: <code>ar</code>, <code>az</code>, <code>bg</code>, <code>bs</code>, <code>ca</code>, 
                <code>cs</code>, <code>da</code>, <code>de</code>, <code>el</code>, <code>es</code>, <code>et</code>, 
                <code>fa</code>, <code>fi</code>, <code>fr</code>, <code>he</code>, <code>hr</code>, <code>hu</code>, 
                <code>hy</code>, <code>id</code>, <code>it</code>, <code>ja</code>, <code>ka</code>, <code>kk</code>, 
                <code>ko</code>, <code>kz</code>, <code>lt</code>, <code>lv</code>, <code>ms</code>, <code>nb-NO</code>, 
                <code>nl</code>, <code>pl</code>, <code>pt</code>, <code>pt-BR</code>, <code>ro</code>, <code>ru</code>, 
                <code>sk</code>, <code>sl</code>, <code>sr</code>, <code>sr-Latn</code>, <code>sv</code>, <code>tg</code>, 
                <code>th</code>, <code>tr</code>, <code>uk</code>, <code>uz</code>, <code>vi</code>, <code>zh-CN</code>, 
                <code>zh-TW</code>.',

                'baseClass' => 'This is the class that the new enum class will extend from. Please make sure the class exists and can be autoloaded.',
            ];
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function stickyAttributes()
        {
            return ['baseClass'];
        }
        //endregion

        //region Required Templates
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function requiredTemplates()
        {
            return [
                'enum.php',
                'language.php',
            ];
        }
        //endregion

        //region Success Message
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function successMessage()
        {
            return 'The Enum has been generated successfully.';
        }
        //endregion

        //region Generation
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function generate()
        {
            $files = [];

            $files[] = new CodeFile(
                $this->findEnumFile(),
                $this->render('enum.php')
            );

            foreach ($this->findLanguagesIDs() as $language) {
                $files[] = new CodeFile(
                    $this->findLanguageFile($language),
                    $this->render('language.php', ['language' => $language])
                );
            }

            return $files;
        }
        //endregion

        //region Getters
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function getName()
        {
            return 'Enum Generator';
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function getDescription()
        {
            return 'This generator helps you to quickly generate a new Enum class.';
        }
        //endregion

        //region Strategic Helpers


        /**
         * Normalizes [[languages]] into an array of action IDs.
         *
         * @return array an array of action IDs entered by the user
         */
        public function findLanguagesIDs(): array
        {
            $languages = array_unique(preg_split('/[\s,]+/', $this->languages, -1, PREG_SPLIT_NO_EMPTY));

            sort($languages);

            return $languages;
        }

        /**
         * @return array
         */
        public function findConstants(): array
        {
            $constants     = [];
            foreach (explode("\n", $this->constants) as $constant) {
                $pairs = explode(',', $constant);

                if (isset($pairs[0], $pairs[1], $pairs[2])) {
                    $key   = mb_convert_case(InflectorHelper::slug(trim($pairs[0]), '_'), MB_CASE_UPPER, Yii::$app->charset);
                    $value = ((is_numeric(trim($pairs[1]))) ? (int)trim($pairs[1]) : mb_convert_case(InflectorHelper::slug(trim($pairs[1]), '_'), MB_CASE_LOWER, Yii::$app->charset));
                    $label = trim($pairs[2]);

                    if (empty($key) || empty($value) || empty($label)) {
                        continue;
                    }

                    $constants[$key] = ['key' => $key, 'value' => $value, 'label' => $label, 'numeric' => is_int($value)];
                }
            }

            return $constants;
        }

        /**
         * @return string the enum class file path
         */
        public function findEnumFile(): string
        {
            return Yii::getAlias('@'.str_replace('\\', '/', $this->enumClass)).'.php';
        }

        /**
         * @return string the enum ID
         *
         * @noinspection SubStrShortHandUsageInspection
         */
        public function findEnumID(): string
        {
            $name = StringHelper::basename($this->enumClass);

            return InflectorHelper::camel2id(substr($name, 0, strlen($name) - 10));
        }

        /**
         * @return string the enum ID
         */
        public function findEnumAttribute(): string
        {
            $id = StringHelper::basename($this->enumClass);

            return lcfirst(str_replace('Enum', '', $id));
        }

        /**
         * @param string $language the language ID
         *
         * @return string the language file path
         */
        public function findLanguageFile(string $language): string
        {
            return Yii::getAlias('@'.str_replace('\\', '/', $this->findEnumNamespace())).'/messages/'.$language.'/'.InflectorHelper::camel2id(StringHelper::basename($this->enumClass)).'.php';
        }

        /**
         * @return string the namespace of the enum class
         */
        public function findEnumNamespace(): string
        {
            $name = StringHelper::basename($this->enumClass);

            return ltrim(substr($this->enumClass, 0, -(strlen($name) + 1)), '\\');
        }

        /**
         * @return string the namespace of the base class
         */
        public function findBaseNamespace(): string
        {
            $name = StringHelper::basename($this->baseClass);

            return ltrim(substr($this->baseClass, 0, -(strlen($name) + 1)), '\\');
        }
        //endregion
    }
