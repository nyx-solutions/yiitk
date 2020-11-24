<?php

    namespace yiitk\helpers;

    /**
     * Class ArrayHelper
     */
    class ArrayHelper extends \yii\helpers\ArrayHelper
    {
        /**
         * @param array $items
         *
         * @return array
         */
        public static function asAssociative(array $items): array
        {
            if (!empty($items)) {
                $newItems = [];

                foreach ($items as $item) {
                    $newItems[(string)$item] = (string)$item;
                }

                return $newItems;
            }

            return [];
        }
    }
