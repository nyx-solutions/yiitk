<?php

    namespace yiitk\emum;

    use yiitk\emum\base\BaseEnum;

    /**
     * Class BooleanEnum
     *
     * @property string $yes
     * @property string $no
     *
     * @property bool   $isYes
     * @property bool   $isNo
     *
     * @method static   yes
     * @method static   no
     */
    class BooleanEnum extends BaseEnum
    {
        const YES = 'yes';
        const NO  = 'no';

        /**
         * {@inheritdoc}
         */
        public static function defaultValue()
        {
            return self::NO;
        }

        /**
         * {@inheritdoc}
         */
        protected static function labels()
        {
            return [
                self::YES => 'Yes',
                self::NO  => 'No'
            ];
        }
    }
