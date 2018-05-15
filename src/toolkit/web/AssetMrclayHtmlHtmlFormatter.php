<?php

    namespace yiitk\web;

    use yii\base\Component;

    /**
     * @author Semenov Alexander <semenov@skeeks.com>
     * @author Jonatas Sas <atendimento@jsas.com.br>
     */
    class AssetMrclayHtmlHtmlFormatter extends Component implements IAssetHtmlFormatter
    {
        /**
         * @param string $html
         *
         * @return string
         */
        public function format($html)
        {
            return \Minify_HTML::minify((string)$html, []);
        }

    }
