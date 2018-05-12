<?php

    namespace yiitk\helpers;

    use Carbon\Carbon;
    use Yii;

    /**
     * Class DateTimeHelper
     *
     * @package yiitk\helpers
     */
    class DateTimeHelper extends Carbon
    {
        /**
         * {@inheritdoc}
         */
        public function __construct($time = null, $tz = null)
        {
            if (is_null($tz)) {
                $tz = new \DateTimeZone(static::findCurrentTimeZone());
            }

            parent::__construct($time, $tz);
        }

        /**
         * @return string
         */
        public static function findCurrentTimeZone()
        {
            return Yii::$app->timeZone;
        }
    }
