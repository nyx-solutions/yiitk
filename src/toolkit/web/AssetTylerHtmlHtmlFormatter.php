<?php

    namespace yiitk\web;

    use yii\base\Component;

    /**
     * @author Semenov Alexander <semenov@skeeks.com>
     * @author Jonatas Sas <atendimento@jsas.com.br>
     */
    class AssetTylerHtmlHtmlFormatter extends Component implements IAssetHtmlFormatter
    {
        /**
         * Perform extra (possibly unsafe) compression operations
         *
         * @var bool
         */
        public $extra = false;

        /**
         * Removes HTML comments
         *
         * @var bool
         */
        public $noComments = true;

        /**
         * The maximum number of rows that the formatter runs on
         *
         * @var int
         */
        public $maxNumberRows = 50000;

        /**
         * @param string $html
         *
         * @return string
         */
        public function format($html)
        {
            $options = ['no-comments' => $this->noComments, 'extra' => $this->extra];

            \Yii::beginProfile('countHtmlRows');

            $count = substr_count($html, "\n") + 1;

            \Yii::info('Number of HTML rows: '.$count);

            if ($count > $this->maxNumberRows) {
                \Yii::info("Not run: ".self::class.". Too many lines: {$count}. Can be no more than: {$this->maxNumberRows}");

                return $html;
            }

            \Yii::endProfile('countHtmlRows');

            $result = AssetHtmlCompressor::compress((string)$html, $options);

            return $result;
        }
    }