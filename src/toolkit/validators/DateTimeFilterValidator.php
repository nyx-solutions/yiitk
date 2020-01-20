<?php

    namespace yiitk\validators;

    /**
     * Class DateTimeFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class DateTimeFilterValidator extends FilterValidator
    {
        /**
         * @var string
         */
        public $format = 'd/m/Y H:i:s';

        /**
         * @var bool
         */
        public $useTime = true;

        /**
         * @inheritdoc
         */
        public function init()
        {
            $format  = $this->format;
            $useTime = $this->useTime;

            $this->addFilter(
                function ($value) use ($format, $useTime) {
                    $date = \DateTime::createFromFormat($format, (string)$value, new \DateTimeZone(\Yii::$app->getTimeZone()));

                    $dbFormat = 'Y-m-d';

                    if ($useTime) {
                        $dbFormat .= ' H:i:s';
                    }

                    return $date->format($dbFormat);
                }
            );

            parent::init();
        }
    }
