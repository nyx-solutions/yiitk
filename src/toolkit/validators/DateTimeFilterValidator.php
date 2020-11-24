<?php

    namespace yiitk\validators;

    use DateTime;
    use DateTimeZone;
    use Yii;

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
        public string $format = 'd/m/Y H:i:s';

        /**
         * @var bool
         */
        public bool $useTime = true;

        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $format  = $this->format;
            $useTime = $this->useTime;

            $this->addFilter(
                static function ($value) use ($format, $useTime) {
                    $date = DateTime::createFromFormat($format, (string)$value, new DateTimeZone(Yii::$app->getTimeZone()));

                    $dbFormat = 'Y-m-d';

                    if ($useTime) {
                        $dbFormat .= ' H:i:s';
                    }

                    return $date->format($dbFormat);
                }
            );

            parent::init();
        }
        //endregion
    }
