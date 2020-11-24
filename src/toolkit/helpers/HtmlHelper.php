<?php

    namespace yiitk\helpers;

    use yii\helpers\Html;

    /**
     * Class HtmlHelper
     */
    class HtmlHelper extends Html
    {
        /**
         * @param array|string $current
         * @param array|string $new
         *
         * @return string
         */
        public static function createCssClass($current, $new = []): string
        {
            if (!is_array($current)) {
                if (empty($current)) {
                    $current = '';
                }

                $current = explode(' ', $current);
            }

            if (!is_array($new)) {
                if (empty($new)) {
                    $new = '';
                }

                $new = explode(' ', $new);
            }

            $classes = array_unique(array_merge($current, $new));

            if (!empty($classes)) {
                sort($classes);

                return trim(implode(' ', $classes));
            }

            return '';
        }
    }
