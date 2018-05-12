<?php

    namespace yiitk\helpers;

    use yii\helpers\Html;

    /**
     * Class HtmlHelper
     *
     * @category Html
     * @author   Jonatas Sas
     *
     * @package  yiitk\helpers
     */
    class HtmlHelper extends Html
    {
        /**
         * @param string $glyph
         * @param string $content
         * @param string $prefix
         * @param string $suffix
         * @param string $prefixSep
         * @param string $suffixSep
         *
         * @return string
         */
        public static function glyphicon($glyph, $content = '', $prefix = '', $suffix = ' ', $prefixSep = '', $suffixSep = '')
        {
            return $prefix.$prefixSep.self::tag('span', $content, ['class' => 'glyphicon glyphicon-'.$glyph]).$suffixSep.$suffix;
        }

        /**
         * @param string $html
         *
         * @return string
         */
        public static function toText($html)
        {
            return strip_tags($html);
        }
    }
