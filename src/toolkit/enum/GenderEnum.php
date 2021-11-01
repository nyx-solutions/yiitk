<?php

    namespace yiitk\enum;

    use yiitk\enum\base\BaseEnum;
    use yiitk\enum\base\EnumerableInterface;

    /**
     * Gender Enum
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
    class GenderEnum extends BaseEnum implements EnumerableInterface
    {
        public const MALE   = 'male';
        public const FEMALE = 'female';

        /**
         * @inheritdoc
         */
        public static function defaultValue(): int|string|null
        {
            return self::FEMALE;
        }

        /**
         * @inheritdoc
         */
        public static function labels(): array
        {
            return [
                self::MALE   => 'Male',
                self::FEMALE => 'Female'
            ];
        }
    }
