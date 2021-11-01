<?php

    namespace yiitk\helpers;

    use DateTime;
    use DateTimeZone;
    use Exception as BaseException;
    use Yii;

    /**
     * Random Strings Trait
     */
    trait RandomStringsTrait
    {
        /**
         * @param int $length
         * @param int $upper
         * @param int $lower
         * @param int $digit
         * @param int $special
         *
         * @return string
         *
         * @noinspection NonSecureStrShuffleUsageInspection
         *
         * @throws BaseException
         */
        public static function generateRandomString($length = 0, $upper = 0, $lower = 0, $digit = 0, $special = 0)
        {
            $length      = (int)$length;
            $upper       = (int)$upper;
            $lower       = (int)$lower;
            $digit       = (int)$digit;
            $special     = (int)$special;

            $lowerList   = 'abcdefghijklmnopqrstuvxwyz';
            $upperList   = 'ABCDEFGHIJKLMNOPQRSTUVXWYZ';
            $digitList   = '0123456789';
            $specialList = '!@#$%&*()_-+={}[]?:;';

            $gapList     = '';

            if ($upper > 0) {
                $gapList .= $upperList;
            }

            if ($lower > 0) {
                $gapList .= $lowerList;
            }

            if ($digit > 0) {
                $gapList .= $digitList;
            }

            if ($special > 0) {
                $gapList .= $specialList;
            }

            if (empty($gapList)) {
                $gapList .= $lowerList.$digitList;
            }

            $final = '';

            if ($length > 0) {
                for ($i = 1; $i <= $length; $i++) {
                    if ($upper > 0) {
                        $aux = str_shuffle($upperList);
                        $final .= $aux[0];
                        $upper--;

                        continue;
                    }

                    if ($lower > 0) {
                        $aux = str_shuffle($lowerList);
                        $final .= $aux[0];
                        $lower--;

                        continue;
                    }

                    if ($digit > 0) {
                        $aux = str_shuffle($digitList);
                        $final .= $aux[0];
                        $digit--;

                        continue;
                    }

                    if ($special > 0) {
                        $aux = str_shuffle($specialList);
                        $final .= $aux[0];
                        $special--;

                        continue;
                    }

                    $aux = str_shuffle($gapList);
                    $final .= $aux[0];
                }

                return str_shuffle($final);
            }

            return (string)random_int(100000, 999999);
        }

        /**
         * @return string
         *
         * @throws BaseException
         */
        public static function getUniqueCode()
        {
            $now = new DateTime('now', new DateTimeZone(Yii::$app->getTimeZone()));

            return (string)sha1(uniqid(mt_rand().mt_rand().$now->format('YmdHis'), true));
        }

        /**
         * @param int $id
         *
         * @return string
         *
         * @throws BaseException
         */
        public static function getPatternFromId($id)
        {
            $id = (int)$id;

            $left  = (string)random_int((int)('1' . str_repeat('0', (self::_idPatternLength() - 1))), (int)(str_repeat('9', (self::_idPatternLength()))));
            $right = (string)random_int((int)('1' . str_repeat('0', (self::_idPatternLength() - 1))), (int)(str_repeat('9', (self::_idPatternLength()))));

            return $left. $id .$right;
        }

        /**
         * @param string $uid
         *
         * @return int
         */
        public static function getIdFromPattern($uid)
        {
            $uid = (string)$uid;

            return (int)preg_replace('/^([0-9]{'.self::_idPatternLength().'})([0-9]+)([0-9]{'.self::_idPatternLength().'})$/', '$2', (string)$uid);
        }

        /**
         * @return int
         */
        private static function _idPatternLength(): int
        {
            $class = static::class;

            if (defined("{$class}::ID_PATTERN_LENGTH")) {
                return $class::ID_PATTERN_LENGTH;
            }

            return 9;
        }
    }
