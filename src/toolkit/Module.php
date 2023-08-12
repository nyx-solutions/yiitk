<?php

    namespace yiitk;

    use Yii;
    use yii\base\InvalidConfigException;
    use yii\i18n\Formatter;
    use yii\i18n\PhpMessageSource;
    use yiitk\file\FileManager;
    use yiitk\helpers\ArrayHelper;

    /**
     * Class Module
     *
     * @package yiitk
     */
    class Module extends \yii\base\Module
    {
        /**
         * @var string
         */
        public string $translationsBasePath = '@yiitk/messages';

        /**
         * @var bool
         */
        public bool $formatter = true;

        /**
         * @var string
         */
        public string $defaultTimeZone = 'America/Sao_Paulo';

        /**
         * @var array
         */
        public array $sessionDb = [];

        /**
         * @var array
         */
        public array $fileManager = [];

        /**
         * @var array
         */
        public array $i18n = [];

        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            $this->setup();

            parent::init();
        }
        #endregion

        #region Setup
        /**
         * @return void
         *
         * @throws InvalidConfigException
         */
        protected function setup()
        {
            $this->setupAliases();
            $this->setupConfiguration();
            $this->setupTranslations();
            $this->setupFormatter();
            $this->setupSessionDb();
            $this->setupFileManager();
        }

        /**
         * @return void
         */
        protected function setupAliases()
        {
            Yii::setAlias('@yiitk', __DIR__);
            Yii::setAlias('@yiitk/messages', __DIR__.'/messages');
        }

        /**
         * @return void
         */
        protected function setupConfiguration()
        {
            Yii::configure($this, require(__DIR__.'/config/config.php'));
        }

        /**
         * @return void
         */
        protected function setupTranslations()
        {
            if (!isset(Yii::$app->i18n->translations['yiitk'])) {
                if (empty($this->i18n)) {
                    $this->i18n = [
                        'class'          => PhpMessageSource::class,
                        'sourceLanguage' => 'en',
                        'basePath'       => $this->translationsBasePath,
                        'fileMap'        => ['yiitk' => 'yiitk.php']
                    ];
                }

                Yii::$app->i18n->translations['yiitk'] = $this->i18n;
            }
        }

        /**
         * @return void
         *
         * @throws InvalidConfigException
         */
        protected function setupFormatter()
        {
            if ($this->formatter) {
                /** @var Formatter $formatter */
                $formatter = Yii::$app->get('formatter', false);

                if ($formatter instanceof Formatter) {
                    $formatter->dateFormat        = 'php:d/m/Y';
                    $formatter->datetimeFormat    = 'php:d/m/Y H:i';
                    $formatter->decimalSeparator  = ',';
                    $formatter->thousandSeparator = '.';
                    $formatter->currencyCode      = 'BRL';
                    $formatter->defaultTimeZone   = $this->defaultTimeZone;
                }
            }
        }

        /**
         * @return void
         */
        protected function setupSessionDb()
        {
            $sessionDbConfig = [
                'db'         => false,
                'dbFrontend' => false,
                'dbBackend'  => false,
                'dbApi'      => false
            ];

            $sessionDbConfig = ArrayHelper::merge($sessionDbConfig, $this->sessionDb);

            $this->sessionDb = [
                'db'         => (bool)$sessionDbConfig['db'],
                'dbFrontend' => (bool)$sessionDbConfig['dbFrontend'],
                'dbBackend'  => (bool)$sessionDbConfig['dbBackend'],
                'dbApi'      => (bool)$sessionDbConfig['dbApi']
            ];
        }

        /**
         * @return void
         *
         * @throws InvalidConfigException
         */
        protected function setupFileManager()
        {
            $fileManagerConfig = [
                'fileTable'       => '{{%file}}',
                'useBigIntegerPk' => true,
                'useBigIntegerFk' => true,
                'pkLength'        => 20,
                'fkLength'        => 20,
                'fkFieldSuffix'   => 'Id'
            ];

            $fileManagerConfig = ArrayHelper::merge($fileManagerConfig, $this->fileManager);

            /** @var FileManager $fileManager */
            $fileManager = $this->get('fileManager');

            $fileManager->fileTable       = (string)$fileManagerConfig['fileTable'];
            $fileManager->useBigIntegerPk = (bool)$fileManagerConfig['useBigIntegerPk'];
            $fileManager->useBigIntegerFk = (bool)$fileManagerConfig['useBigIntegerFk'];
            $fileManager->pkLength        = (int)$fileManagerConfig['pkLength'];
            $fileManager->fkLength        = (int)$fileManagerConfig['fkLength'];
            $fileManager->fkFieldSuffix   = (string)$fileManagerConfig['fkFieldSuffix'];
        }
        #endregion
    }
