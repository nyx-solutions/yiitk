<?php

    namespace yiitk\helpers;

    /**
     * Class ArrayHelper
     *
     * @package yiitk\helpers
     */
    class ArrayHelper extends \yii\helpers\ArrayHelper
    {
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
    }
