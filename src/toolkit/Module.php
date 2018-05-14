<?php

    namespace yiitk;

    use yii\i18n\Formatter;
    use yii\i18n\PhpMessageSource;

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
        public $translationsBasePath = '@yiitk/messages';

        /**
         * @var bool
         */
        public $formatter = true;

        /**
         * @var string
         */
        public $defaultTimeZone = 'America/Sao_Paulo';

        /**
         * @var bool
         */
        public $useSessionDb = false;

        /**
         * @var bool
         */
        public $useSessionDbFrontend = false;

        /**
         * @var bool
         */
        public $useSessionDbBackend = false;

        /**
         * @var bool
         */
        public $useSessionDbApi = false;

        #region Initialization
        /**
         * {@inheritdoc}
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
         */
        protected function setup()
        {
            $this->setupAliases();
            $this->setupConfiguration();
            $this->setupTranslations();
            $this->setupFormatter();
        }

        /**
         * @return void
         */
        protected function setupAliases()
        {
            \Yii::setAlias('@yiitk', __DIR__);
            \Yii::setAlias('@yiitk/messages', __DIR__.'/messages');
        }

        /**
         * @return void
         */
        protected function setupConfiguration()
        {
            \Yii::configure($this, require(__DIR__.'/config/config.php'));
        }

        /**
         * @return void
         */
        protected function setupTranslations()
        {
            /** @var array $translations */
            $translations = \Yii::$app->i18n->translations;

            if (!isset($translations['yiitk'])) {
                $translations['yiitk'] = ['class' => PhpMessageSource::class, 'sourceLanguage' => 'en-US', 'basePath' => $this->translationsBasePath, 'fileMap' => ['yiitk' => 'yiitk.php']];
            }

            $translations['yii'] = ['class' => PhpMessageSource::class, 'sourceLanguage' => 'en-US', 'basePath' => $this->translationsBasePath, 'fileMap' => ['yii' => 'yii.php']];
        }

        /**
         * @return void
         *
         * @throws \yii\base\InvalidConfigException
         */
        protected function setupFormatter()
        {
            if ($this->formatter) {
                /** @var Formatter $formatter */
                $formatter = \Yii::$app->get('formatter', false);

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
        #endregion
    }
