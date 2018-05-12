<?php

    namespace yiitk\emum;

    use yiitk\emum\base\BaseEnum;

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
        const MALE   = 'male';
        const FEMALE = 'female';

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
        protected static function labels()
        {
            return [
                self::MALE   => 'Male',
                self::FEMALE => 'Female'
            ];
        }
    }
