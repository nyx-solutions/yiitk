<?php

    namespace yiitk\helpers;

    use Yii;

    /**
     * Class SlugHelper
     *
     * @package yiitk\helpers
     */
    class SlugHelper extends StringHelper
    {
        const SLUG_METHOD_SINGLE = 1;
        const SLUG_METHOD_AS_KEY = 2;

        /**
         * @param array   $items
         * @param integer $method
         *
         * @return array
         */
        public static function asSlugs($items, $method = self::SLUG_METHOD_SINGLE)
        {
            if (is_array($items) && count($items) > 0) {
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
            } else {
                return [];
            }
        }

        /**
         * @param string  $value
         * @param string  $spaces
         * @param integer $case
         *
         * @return string
         */
        public static function convert($value = '', $spaces = '-', $case = MB_CASE_LOWER)
        {
            return static::asSlug($value, $spaces, $case);
        }
    }
