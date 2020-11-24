<?php

    namespace yiitk\helpers;

    /**
     * Class SlugHelper
     *
     * @package yiitk\helpers
     */
    class SlugHelper extends StringHelper
    {
        public const SLUG_METHOD_SINGLE = 1;
        public const SLUG_METHOD_AS_KEY = 2;

        protected const VALID_SLUG_METHODS = [self::SLUG_METHOD_SINGLE, self::SLUG_METHOD_AS_KEY];

        /**
         * @param array $items
         * @param int   $method
         *
         * @return array
         */
        public static function asSlugs(array $items, int $method = self::SLUG_METHOD_SINGLE): array
        {
            if (!in_array($method, self::VALID_SLUG_METHODS)) {
                $method = self::SLUG_METHOD_SINGLE;
            }

            if (!empty($items)) {
                $slugs = [];

                foreach ($items as $item) {
                    $slug = StringHelper::asSlug((string)$item);

                    if ($method === self::SLUG_METHOD_SINGLE) {
                        $slugs[] = $slug;
                    } else {
                        $slugs[$slug] = (string)$item;
                    }
                }

                return $slugs;
            }

            return [];
        }

        /**
         * @param string $value
         * @param string $spaces
         * @param int    $case
         *
         * @return string
         */
        public static function convert(string $value = '', string $spaces = '-', int $case = MB_CASE_LOWER): string
        {
            return static::asSlug($value, $spaces, $case);
        }
    }
