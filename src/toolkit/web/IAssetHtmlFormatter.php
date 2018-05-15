<?php

    namespace yiitk\web;

    /**
     * Interface IAssetFormatter
     *
     * @author Semenov Alexander <semenov@skeeks.com>
     * @author Jonatas Sas <atendimento@jsas.com.br>
     */
    interface IAssetHtmlFormatter
    {
        /**
         * @param string $content
         *
         * @return string
         */
        public function format($content);
    }
