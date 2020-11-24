<?php

    namespace yiitk\enum;

    use yiitk\enum\base\BaseEnum;

    /**
     * Class GenderEnum
     *
     * @property string $male
     * @property string $female
     *
     * @property bool   $isMale
     * @property bool   $isFemale
     *
     * @method   static male
     * @method   static female
     */
    class GenderEnum extends BaseEnum
    {
        public const MALE   = 'male';
        public const FEMALE = 'female';

        /**
         * {@inheritdoc}
         */
        public static function defaultValue()
        {
            return self::FEMALE;
        }

        /**
         * {@inheritdoc}
         */
        protected static function labels(): array
        {
            return [
                self::MALE   => 'Male',
                self::FEMALE => 'Female'
            ];
        }
    }
