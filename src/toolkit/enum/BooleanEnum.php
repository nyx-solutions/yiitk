<?php

    namespace yiitk\enum;

    use yiitk\enum\base\BaseEnum;
    use yiitk\enum\base\EnumerableInterface;

    /**
     * Boolean Enum
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
    class BooleanEnum extends BaseEnum implements EnumerableInterface
    {
        public const YES = 'yes';
        public const NO  = 'no';

        /**
         * @inheritdoc
         */
        public static function defaultValue(): int|string|null
        {
            return self::NO;
        }

        /**
         * @inheritdoc
         */
        public static function labels(): array
        {
            return [
                self::YES => 'Yes',
                self::NO  => 'No'
            ];
        }
    }
