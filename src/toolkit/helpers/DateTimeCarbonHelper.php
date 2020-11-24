<?php

    namespace yiitk\helpers;

    use Carbon\Carbon;
    use DateTimeZone;
    use Yii;

    /**
     * Class DateTimeHelper
     */
    class DateTimeCarbonHelper extends Carbon
    {
        /**
         * {@inheritdoc}
         */
        public function __construct($time = null, $tz = null)
        {
            if (is_null($tz)) {
                $tz = new DateTimeZone(static::findCurrentTimeZone());
            }

            parent::__construct($time, $tz);
        }

        /**
         * @return string
         */
        public static function findCurrentTimeZone(): string
        {
            return Yii::$app->timeZone;
        }
    }
