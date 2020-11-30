<?php

    namespace yiitk\web;

    /**
     * Interface IAssetFormatter
     *
     * @deprecated
     */
    interface IAssetHtmlFormatter
    {
        /**
         * @param string|null $content
         *
         * @return string
         */
        public function format(?string $content): string;
    }
