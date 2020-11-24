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
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         * @noinspection PhpMissingParamTypeInspection
         */
        public function format($content);
    }
