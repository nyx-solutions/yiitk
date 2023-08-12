<?php

    namespace yiitk\helpers;

    use DateTime;
    use DateTimeZone;
    use Exception as BaseException;
    use Throwable;
    use Yii;
    use yii\base\InvalidArgumentException;
    use yii\db\Expression;

    /**
     * DateTime Helper
     */
    class DateTimeHelper
    {
        public const TYPE_DATE            = 'date';
        public const TYPE_TIME            = 'time';
        public const TYPE_DATE_TIME       = 'datetime';
        public const TYPE_OTHER           = 'other';

        public const BASE_DATE_FORMAT     = 'Y-m-d';
        public const BASE_DATETIME_FORMAT = 'Y-m-d H:i:s';
        public const BASE_TIME_FORMAT     = 'H:i:s';

        #region TimeZone
        /**
         * @return string
         */
        public static function findCurrentTimeZone(): string
        {
            return Yii::$app->timeZone;
        }
        #endregion

        #region Months
        /**
         * @param string $month
         * @param int    $year
         *
         * @return string
         */
        public static function simpleMonthWithYear(string $month, int $year): string
        {
            switch ($month) {
                case 'january': {
                    return "JAN/{$year}";
                }

                case 'february': {
                    return "FEV/{$year}";
                }

                case 'march': {
                    return "MAR/{$year}";
                }

                case 'april': {
                    return "ABR/{$year}";
                }

                case 'may': {
                    return "MAI/{$year}";
                }

                case 'june': {
                    return "JUN/{$year}";
                }

                case 'july': {
                    return "JUL/{$year}";
                }

                case 'august': {
                    return "AGO/{$year}";
                }

                case 'september': {
                    return "SET/{$year}";
                }

                case 'october': {
                    return "OUT/{$year}";
                }

                case 'november': {
                    return "NOV/{$year}";
                }

                case 'december': {
                    return "DEZ/{$year}";
                }
            }

            return '';
        }
        #endregion

        #region WeekDays
        /**
         * @param int $day
         *
         * @return string
         */
        public static function findWeekDayName(int $day): string
        {
            switch ($day) {
                case 1: {
                    return 'SEGUNDA';
                }

                case 2: {
                    return 'TERÇA';
                }

                case 3: {
                    return 'QUARTA';
                }

                case 4: {
                    return 'QUINTA';
                }

                case 5: {
                    return 'SEXTA';
                }

                case 6: {
                    return 'SÁBADO';
                }

                case 7: {
                    return 'DOMINGO';
                }
            }

            return '';
        }
        #endregion

        #region Now
        /**
         * @param bool|string|null $format
         *
         * @return string|DateTime
         *
         * @throws BaseException
         */
        public static function now(bool|string|null $format = null): DateTime|string
        {
            if ($format === null) {
                $format = 'Y-m-d H:i:s';
            }

            $now = new DateTime('now', new DateTimeZone(static::findCurrentTimeZone()));

            if ($format === null || $format === false) {
                return $now;
            }

            return $now->format($format);
        }

        /**
         * @param mixed|null $from
         *
         * @return DateTime
         */
        public static function tomorrow(mixed $from = null): DateTime
        {
            try {
                if (!$from instanceof DateTime) {
                    if (!is_string($from) || empty($from)) {
                        $from = static::now(false);
                    } else {
                        $from = static::guess($from, false);
                    }
                }

                return $from->modify('+1 day');
            } catch (Throwable) {}

            return new DateTime('now');
        }

        /**
         * @param bool $complete
         *
         * @return string
         *
         * @throws BaseException
         */
        public static function getNow(bool $complete = true): string
        {
            if ($complete) {
                return static::now('Y-m-d H:i:s');
            }

            return static::now('Y-m-d');
        }

        /**
         * @param bool        $useExpression
         * @param bool        $useDateTime
         * @param string|null $format
         *
         * @return string|Expression
         *
         * @throws BaseException
         */
        public static function dbNow(bool $useExpression = false, bool $useDateTime = true, ?string $format = null): string|Expression
        {

            if ($useExpression) {
                return new Expression('NOW()');
            }

            if ($format !== null) {
                return static::now($format);
            }

            if ($useDateTime) {
                return static::now('Y-m-d H:i:s');
            }

            return static::now('Y-m-d');
        }
        #endregion

        #region Convert
        /**
         * @param string $date
         * @param string $sourceFormat
         * @param string $targetFormat
         * @param string $type
         *
         * @return string
         */
        public static function convert(string $date, string $sourceFormat = 'd/m/Y', string $targetFormat = 'Y-m-d', string $type = 'date'): string
        {
            if (!in_array($type, [self::TYPE_DATE, self::TYPE_TIME, self::TYPE_DATE_TIME, self::TYPE_OTHER])) {
                return '';
            }

            if (empty($date)) {
                return '';
            }

            if ($type === self::TYPE_DATE_TIME) {
                $fmt = (empty($sourceFormat)) ? self::BASE_DATETIME_FORMAT : $sourceFormat;
            } elseif ($type === self::TYPE_TIME) {
                $fmt = (empty($sourceFormat)) ? self::BASE_TIME_FORMAT : $sourceFormat;
            } elseif ($type === self::TYPE_DATE) {
                $fmt = (empty($sourceFormat)) ? self::BASE_DATE_FORMAT : $sourceFormat;
            } else {
                if (empty($sourceFormat)) {
                    return '';
                }

                $fmt = $sourceFormat;
            }

            $sourceFormat = $fmt;

            unset($fmt);

            $newDate = DateTime::createFromFormat($sourceFormat, $date);

            if ($newDate instanceof DateTime) {
                $newDate = $newDate->format($targetFormat);
            } else {
                $newDate = '';
            }

            return $newDate;
        }

        /**
         * @param string $date
         * @param bool   $exception
         *
         * @return DateTime|bool
         *
         * @throws InvalidArgumentException|Throwable
         */
        public static function guess(string $date, bool $exception = true): DateTime|bool
        {
            $formats = [
                'Y-m-d'       => '/^([\d]{4})\-([\d]{2})\-([\d]{2})$/',
                'Y-m-d H:i'   => '/^([\d]{4})\-([\d]{2})\-([\d]{2}) ([\d]{2})\:([\d]{2})$/',
                'Y-m-d H:i:s' => '/^([\d]{4})\-([\d]{2})\-([\d]{2}) ([\d]{2})\:([\d]{2})\:([\d]{2})$/',
                'd/m/Y'       => '/^([\d]{2})\/([\d]{2})\/([\d]{4})$/',
                'd/m/Y H:i'   => '/^([\d]{2})\/([\d]{2})\/([\d]{4}) ([\d]{2})\:([\d]{2})$/',
                'd/m/Y H:i:s' => '/^([\d]{2})\/([\d]{2})\/([\d]{4}) ([\d]{2})\:([\d]{2})\:([\d]{2})$/',
            ];

            $currentFormat = null;

            foreach ($formats as $format => $re) {
                if (preg_match($re, $date)) {
                    $currentFormat = $format;
                }
            }

            $targetDate = null;

            if ($currentFormat !== null) {
                try {
                    $targetDate = DateTime::createFromFormat($currentFormat, $date, new DateTimeZone(static::findCurrentTimeZone()));
                } catch (Throwable){}
            }

            if ($targetDate instanceof DateTime) {
                return $targetDate;
            }

            if ($exception) {
                throw new InvalidArgumentException(sprintf('Unknown date format for the string "%s".', $date));
            }

            return false;
        }

        /**
         * @param mixed $date
         *
         * @return bool
         */
        public static function isToday(mixed $date): bool
        {
            if (!$date instanceof DateTime) {
                if (!is_string($date) || empty($date)) {
                    return false;
                }
            }

            try {
                $now = static::now(false);
                $date = static::guess($date, false);

                return ($now->format('Y-m-d') === $date->format('Y-m-d'));
            } catch (Throwable) {}

            return false;
        }
        #endregion

        #region Seconds Passed
        /**
         * @param string $start
         * @param string $end
         *
         * @return int
         *
         * @noinspection SummerTimeUnsafeTimeManipulationInspection
         * @noinspection BadExceptionsProcessingInspection
         */
        public static function secondsPassed(string $start, string $end): int
        {
            try {
                $startDate   = new DateTime($start, new DateTimeZone(static::findCurrentTimeZone()));
                $diff        = (new DateTime($end, new DateTimeZone(static::findCurrentTimeZone())))->diff($startDate);
                $daysInSecs  = $diff->format('%r%a') * 24 * 60 * 60;
                $hoursInSecs = ($diff->h * 60 * 60);
                $minsInSecs  = ($diff->i * 60);

                return round($daysInSecs + $hoursInSecs + $minsInSecs + $diff->s);
            } catch (Throwable) {}

            return 0;
        }
        #endregion
    }
