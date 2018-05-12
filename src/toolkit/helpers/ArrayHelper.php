<?php

    namespace yiitk\helpers;

    /**
     * Class ArrayHelper
     *
     * @package yiitk\helpers
     */
    class ArrayHelper extends \yii\helpers\ArrayHelper
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
         * @param array $items
         *
         * @return array
         */
        public static function asAssociative($items)
        {
            if (is_array($items) && count($items) > 0) {
                $newItems = [];

                foreach ($items as $item) {
                    $newItems[(string)$item] = (string)$item;
                }

                return $newItems;
            } else {
                return [];
            }
        }

        /**
         * @param array $items
         *
         * @return array
         *
         * @see static::asAssociative()
         *
         * @deprecated
         */
        public static function asAssociate($items)
        {
            return static::asAssociative($items);
        }
    }
