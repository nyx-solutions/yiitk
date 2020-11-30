<?php

    /**
     * @noinspection PhpUnused
     */

    namespace yiitk\web;

    use Exception;
    use yii\base\Component;

    /**
     * @deprecated
     */
    class AssetTylerHtmlHtmlFormatter extends Component implements IAssetHtmlFormatter
    {
        /**
         * Perform extra (possibly unsafe) compression operations
         *
         * @var bool
         */
        public bool $extra = false;

        /**
         * Removes HTML comments
         *
         * @var bool
         */
        public bool $noComments = true;

        /**
         * The maximum number of rows that the formatter runs on
         *
         * @var int
         */
        public int $maxNumberRows = 50000;

        /**
         * @param string|null $content
         *
         * @return string
         *
         * @throws Exception
         */
        public function format(?string $content): string
        {
            return $content;
        }
    }
