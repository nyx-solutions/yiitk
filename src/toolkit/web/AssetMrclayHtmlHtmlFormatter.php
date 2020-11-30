<?php

    namespace yiitk\web;

    use yii\base\Component;

    /**
     * @deprecated
     */
    class AssetMrclayHtmlHtmlFormatter extends Component implements IAssetHtmlFormatter
    {
        /**
         * @param string|null $content
         *
         * @return string
         */
        public function format(?string $content): string
        {
            return $content;
        }

    }
