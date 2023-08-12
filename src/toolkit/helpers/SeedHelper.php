<?php

    namespace yiitk\helpers;

    use DateTime;

    /**
     * Helper: Seed
     */
    class SeedHelper
    {
        /**
         * @param mixed       $date
         * @param bool        $time
         * @param bool        $null
         * @param string|null $now
         *
         * @return string|null
         */
        public static function toDate(mixed $date, bool $time = false, bool $null = false, ?string $now = null)
        {
            $date = trim($date);

            if (empty($date)) {
                $date = date('Y-m-d H:i:s');
            }

            if ($null && empty($date)) {
                return null;
            }

            if (empty($date)) {
                return $now;
            }

            $dtm = DateTime::createFromFormat('Ymd', (string)$date);

            if (!$dtm instanceof DateTime) {
                if ($null) {
                    return null;
                }

                return (string)$now;
            }

            $format = 'Y-m-d';

            if ($time) {
                $format .= ' H:i:s';
            }

            return (string)$dtm->format($format);
        }

        /**
         * @param string $string
         * @param bool   $upper
         * @param bool   $lower
         * @param bool   $null
         * @param bool   $numeric
         * @param bool   $namefy
         *
         * @return string|null
         */
        public static function toString(string $string, bool $upper = true, bool $lower = false, bool $null = true, bool $numeric = false, bool $namefy = true)
        {
            $string = str_replace(' ', ' ', $string);

            if ($namefy) {
                $string = trim((string)preg_replace('/([0-9]+)/', '', $string));
            }

            if ($upper) {
                $string = StringHelper::toUpperCase($string);
            }

            if ($lower) {
                $string = StringHelper::toLowerCase($string);
            }

            if ($null && empty($string)) {
                return null;
            }

            if (!$numeric && is_numeric($string)) {
                return null;
            }

            return $string;
        }

        /**
         * @param mixed $phone
         *
         * @return int|null
         */
        public static function toPhone(mixed $phone)
        {
            $phone = (int)StringHelper::justNumbers($phone);

            if (empty($phone) || strlen((string)$phone) < 10 || str_contains($phone, '99999999')) {
                return null;
            }

            return (int)$phone;
        }
    }
