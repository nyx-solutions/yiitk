<?php

    /**
     * @noinspection PhpUnused
     */

    namespace yiitk\web;

    use yii\base\Application as BaseApplication;
    use yii\base\BootstrapInterface;
    use yii\base\Component;

    /**
     * Class AssetCompressor
     *
     * @property string $settingsHash
     *
     * @deprecated
     */
    class AssetCompressor extends Component implements BootstrapInterface
    {
        /**
         * Enable or disable the component
         * @var bool
         */
        public bool $enabled = true;

        /**
         * Time in seconds for reading each asset file
         * @var int
         */
        public int $readFileTimeout = 1;

        /**
         * Enable minification js in html code
         * @var bool
         */
        public bool $jsCompress = true;

        /**
         * Cut comments during processing js
         * @var bool
         */
        public bool $jsCompressFlaggedComments = true;

        /**
         * Enable minification css in html code
         * @var bool
         */
        public bool $cssCompress = true;

        /**
         * @var array
         */
        public array $cssOptions = [];

        /**
         * Turning association css files
         * @var bool
         */
        public bool $cssFileCompile = true;

        /**
         * Trying to get css files to which the specified path as the remote file, skchat him to her.
         * @var bool
         */
        public bool $cssFileRemouteCompile = false;

        /**
         * Enable compression and processing before being stored in the css file
         * @var bool
         */
        public bool $cssFileCompress = true;

        /**
         * Moving down the page css files
         * @var bool
         */
        public bool $cssFileBottom = false;

        /**
         * Transfer css file down the page and uploading them using js
         * @var bool
         */
        public bool $cssFileBottomLoadOnJs = false;

        /**
         * Turning association js files
         * @var bool
         */
        public bool $jsFileCompile = true;

        /**
         * @var array
         */
        public array $jsOptions = [];

        /**
         * Trying to get a js files to which the specified path as the remote file, skchat him to her.
         * @var bool
         */
        public bool $jsFileRemouteCompile = false;

        /**
         * Enable compression and processing js before saving a file
         * @var bool
         */
        public bool $jsFileCompress = true;

        /**
         * Cut comments during processing js
         * @var bool
         */
        public bool $jsFileCompressFlaggedComments = true;

        /**
         * Do not connect the js files when all pjax requests.
         * @var bool
         */
        public bool $noIncludeJsFilesOnPjax = true;

        /**
         * @var bool|array|string|IAssetHtmlFormatter
         */
        public $htmlFormatter = false;

        /**
         * @var string
         */
        public string $targetFolder = 'compressed';

        /**
         * @var string
         */
        public string $webroot = '@webroot';

        #region Bootstrap
        /**
         * @param BaseApplication $app
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         * @noinspection PhpMissingParamTypeInspection
         */
        public function bootstrap($app)
        {
        }
        #endregion
    }
