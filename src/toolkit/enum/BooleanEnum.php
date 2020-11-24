<?php

    namespace yiitk\enum;

    use yiitk\enum\base\BaseEnum;

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
        public const YES = 'yes';
        public const NO  = 'no';

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
        protected static function labels(): array
        {
            return [
                self::YES => 'Yes',
                self::NO  => 'No'
            ];
        }
    }
