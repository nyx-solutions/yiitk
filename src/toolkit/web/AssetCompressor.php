<?php

    namespace yiitk\web;

    use yii\base\BootstrapInterface;
    use yii\base\Component;
    use yii\base\Event;
    use yii\helpers\ArrayHelper;
    use yii\helpers\FileHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\httpclient\Client;
    use yii\web\JsExpression;
    use yii\web\Response;
    use yii\web\View;

    /**
     * Class AssetCompressor
     *
     * @property string $settingsHash
     * @property string $webroot
     */
    class AssetCompressor extends Component implements BootstrapInterface
    {
        /**
         * Enable or disable the component
         * @var bool
         */
        public $enabled = true;

        /**
         * Time in seconds for reading each asset file
         * @var int
         */
        public $readFileTimeout = 1;

        /**
         * Enable minification js in html code
         * @var bool
         */
        public $jsCompress = true;

        /**
         * Cut comments during processing js
         * @var bool
         */
        public $jsCompressFlaggedComments = true;

        /**
         * Enable minification css in html code
         * @var bool
         */
        public $cssCompress = true;

        /**
         * @var array
         */
        public $cssOptions = [];

        /**
         * Turning association css files
         * @var bool
         */
        public $cssFileCompile = true;

        /**
         * Trying to get css files to which the specified path as the remote file, skchat him to her.
         * @var bool
         */
        public $cssFileRemouteCompile = false;

        /**
         * Enable compression and processing before being stored in the css file
         * @var bool
         */
        public $cssFileCompress = true;

        /**
         * Moving down the page css files
         * @var bool
         */
        public $cssFileBottom = false;

        /**
         * Transfer css file down the page and uploading them using js
         * @var bool
         */
        public $cssFileBottomLoadOnJs = false;

        /**
         * Turning association js files
         * @var bool
         */
        public $jsFileCompile = true;

        /**
         * @var array
         */
        public $jsOptions = [];

        /**
         * Trying to get a js files to which the specified path as the remote file, skchat him to her.
         * @var bool
         */
        public $jsFileRemouteCompile = false;

        /**
         * Enable compression and processing js before saving a file
         * @var bool
         */
        public $jsFileCompress = true;

        /**
         * Cut comments during processing js
         * @var bool
         */
        public $jsFileCompressFlaggedComments = true;

        /**
         * Do not connect the js files when all pjax requests.
         * @var bool
         */
        public $noIncludeJsFilesOnPjax = true;

        /**
         * @var bool|array|string|IAssetHtmlFormatter
         */
        public $htmlFormatter = false;

        /**
         * @var string
         */
        protected $_webroot = '@webroot';

        public function init()
        {
            if (is_array($this->htmlFormatter) && isset($this->htmlFormatter['class'])) {
                $this->htmlFormatter = \Yii::createObject($this->htmlFormatter);
            }

            if (!$this->htmlFormatter instanceof IAssetHtmlFormatter) {
                $this->htmlFormatter = false;
            }

            parent::init();
        }

        /**
         * @param \yii\base\Application $app
         */
        public function bootstrap($app)
        {
            if ($app instanceof \yii\web\Application) {
                $app->view->on(
                    View::EVENT_END_PAGE,
                    function (Event $e) use ($app) {
                        /**
                         * @var $view View
                         */
                        $view = $e->sender;

                        if ($this->enabled && $view instanceof View && $app->response->format == Response::FORMAT_HTML && !$app->request->isAjax && !$app->request->isPjax) {
                            \Yii::beginProfile('Compress assets');

                            $this->process($view);

                            \Yii::endProfile('Compress assets');
                        }

                        //TODO:: Think about it
                        if ($this->enabled && $app->request->isPjax && $this->noIncludeJsFilesOnPjax) {
                            \Yii::$app->view->jsFiles = null;
                        }
                    }
                );

                //Html compressing
                $app->response->on(
                    Response::EVENT_BEFORE_SEND,
                    function (Event $event) use ($app) {
                        $response = $event->sender;

                        if ($this->enabled && ($this->htmlFormatter instanceof IAssetHtmlFormatter)  && $response->format == Response::FORMAT_HTML && !$app->request->isAjax && !$app->request->isPjax) {
                            if (!empty($response->data)) {
                                $response->data = $this->processHtml($response->data);
                            }

                            /*if (!empty($response->content)) {
                                $response->content = $this->processHtml($response->content);
                            }*/
                        }
                    }
                );
            }
        }

        #region Process
        /**
         * @param View $view
         */
        protected function process(View $view)
        {
            if ($view->jsFiles && $this->jsFileCompile) {
                \Yii::beginProfile('Compress js files');

                foreach ($view->jsFiles as $pos => $files) {
                    if ($files) {
                        $view->jsFiles[$pos] = $this->processJsFiles($files);
                    }
                }

                \Yii::endProfile('Compress js files');
            }

            if ($view->js && $this->jsCompress) {
                \Yii::beginProfile('Compress js code');

                foreach ($view->js as $pos => $parts) {
                    if ($parts) {
                        $view->js[$pos] = $this->processJs($parts);
                    }
                }

                \Yii::endProfile('Compress js code');
            }


            if ($view->cssFiles && $this->cssFileCompile) {
                \Yii::beginProfile('Compress css files');

                $view->cssFiles = $this->processCssFiles($view->cssFiles);

                \Yii::endProfile('Compress css files');
            }

            if ($view->css && $this->cssCompress) {
                \Yii::beginProfile('Compress css code');

                $view->css = $this->processCss($view->css);

                \Yii::endProfile('Compress css code');
            }

            if ($view->css && $this->cssCompress) {
                \Yii::beginProfile('Compress css code');

                $view->css = $this->processCss($view->css);

                \Yii::endProfile('Compress css code');
            }

            if ($view->cssFiles && $this->cssFileBottom) {
                \Yii::beginProfile('Moving css files bottom');

                if ($this->cssFileBottomLoadOnJs) {
                    \Yii::beginProfile('load css on js');

                    $cssFilesString = implode("", $view->cssFiles);

                    $view->cssFiles = [];

                    $script = Html::script(new JsExpression("document.write('{$cssFilesString}');"));

                    if (ArrayHelper::getValue($view->jsFiles, View::POS_END)) {
                        $view->jsFiles[View::POS_END] = ArrayHelper::merge($view->jsFiles[View::POS_END], [$script]);

                    } else {
                        $view->jsFiles[View::POS_END][] = $script;
                    }


                    \Yii::endProfile('load css on js');
                } else {
                    if (ArrayHelper::getValue($view->jsFiles, View::POS_END)) {
                        $view->jsFiles[View::POS_END] = ArrayHelper::merge($view->cssFiles, $view->jsFiles[View::POS_END]);

                    } else {
                        $view->jsFiles[View::POS_END] = $view->cssFiles;
                    }

                    $view->cssFiles = [];
                }

                \Yii::endProfile('Moving css files bottom');
            }
        }
        /**
         * @param array $files
         *
         * @return array
         */
        protected function processJsFiles($files = [])
        {
            $fileName = md5(implode(array_keys($files)).$this->getSettingsHash()).'.js';

            $publicUrl = \Yii::$app->assetManager->baseUrl.'/js-compress/'.$fileName;

            $rootDir = \Yii::$app->assetManager->basePath.'/js-compress';

            $rootUrl = "{$rootDir}/{$fileName}";

            if (file_exists($rootUrl)) {
                $resultFiles = [];

                if (!$this->jsFileRemouteCompile) {
                    foreach ($files as $fileCode => $fileTag) {
                        if (!Url::isRelative($fileCode)) {
                            $resultFiles[$fileCode] = $fileTag;
                        }
                    }
                }

                $publicUrl = $publicUrl."?v=".filemtime($rootUrl);

                $resultFiles[$publicUrl] = Html::jsFile($publicUrl, $this->jsOptions);

                return $resultFiles;
            }

            //Reading the contents of the files
            try {
                $resultContent = [];
                $resultFiles   = [];

                foreach ($files as $fileCode => $fileTag) {
                    if (Url::isRelative($fileCode)) {
                        if ($pos = strpos($fileCode, "?")) {
                            $fileCode = substr($fileCode, 0, $pos);
                        }

                        $fileCode = $this->webroot.$fileCode;
                        $contentFile = $this->readLocalFile($fileCode);

                        /**\Yii::info("file: " . \Yii::getAlias(\Yii::$app->assetManager->basePath . $fileCode), self::class);*/
                        //$contentFile = $this->fileGetContents( Url::to(\Yii::getAlias($tmpFileCode), true) );
                        //$contentFile = $this->fileGetContents( \Yii::$app->assetManager->basePath . $fileCode );
                        $resultContent[] = trim($contentFile)."\n;";;
                    } else {
                        if ($this->jsFileRemouteCompile) {
                            //Try to download the deleted file
                            $contentFile = $this->fileGetContents($fileCode);
                            $resultContent[] = trim($contentFile);
                        } else {
                            $resultFiles[$fileCode] = $fileTag;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Yii::error(__METHOD__.": ".$e->getMessage(), static::class);

                return $files;
            }

            if ($resultContent) {
                $content = implode($resultContent, ";\n");
                if (!is_dir($rootDir)) {
                    if (!FileHelper::createDirectory($rootDir, 0777)) {
                        return $files;
                    }
                }

                if ($this->jsFileCompress) {
                    $content = \JShrink\Minifier::minify($content, ['flaggedComments' => $this->jsFileCompressFlaggedComments]);
                }

                $page = \Yii::$app->request->absoluteUrl;
                $useFunction = function_exists('curl_init') ? 'curl extension' : 'php file_get_contents';
                $filesString = implode(', ', array_keys($files));

                \Yii::info("Create js file: {$publicUrl} from files: {$filesString} to use {$useFunction} on page '{$page}'", static::className());

                $file = fopen($rootUrl, "w");

                fwrite($file, $content);

                fclose($file);
            }

            if (file_exists($rootUrl)) {
                $publicUrl = $publicUrl."?v=".filemtime($rootUrl);

                $resultFiles[$publicUrl] = Html::jsFile($publicUrl, $this->jsOptions);

                return $resultFiles;
            } else {
                return $files;
            }
        }

        /**
         * @param $parts
         * @return array
         * @throws \Exception
         */
        protected function processJs($parts)
        {
            $result = [];

            if ($parts) {
                foreach ($parts as $key => $value) {
                    $result[$key] = \JShrink\Minifier::minify($value, ['flaggedComments' => $this->jsCompressFlaggedComments]);
                }
            }

            return $result;
        }
        /**
         * @param array $files
         * @return array
         */
        protected function processCssFiles($files = [])
        {
            $fileName = md5(implode(array_keys($files)).$this->getSettingsHash()).'.css';

            $publicUrl = \Yii::$app->assetManager->baseUrl.'/css-compress/'.$fileName;
            //$publicUrl  = \Yii::getAlias('@web/assets/css-compress/' . $fileName);

            $rootDir = \Yii::$app->assetManager->basePath.'/css-compress';
            //$rootDir    = \Yii::getAlias('@webroot/assets/css-compress');
            $rootUrl = $rootDir.'/'.$fileName;

            if (file_exists($rootUrl)) {
                $resultFiles = [];

                if (!$this->cssFileRemouteCompile) {
                    foreach ($files as $fileCode => $fileTag) {
                        if (!Url::isRelative($fileCode)) {
                            $resultFiles[$fileCode] = $fileTag;
                        }
                    }
                }

                $publicUrl = "{$publicUrl}?v=".filemtime($rootUrl);

                $resultFiles[$publicUrl] = Html::cssFile($publicUrl, $this->cssOptions);

                return $resultFiles;
            }

            //Reading the contents of the files
            try {
                $resultContent = [];
                $resultFiles = [];

                foreach ($files as $fileCode => $fileTag) {
                    if (Url::isRelative($fileCode)) {
                        $fileCodeLocal = $fileCode;
                        if ($pos = strpos($fileCode, "?")) {
                            $fileCodeLocal = substr($fileCodeLocal, 0, $pos);
                        }

                        $fileCodeLocal = $this->webroot.$fileCodeLocal;
                        $contentTmp = trim($this->readLocalFile($fileCodeLocal));

                        //$contentTmp         = trim($this->fileGetContents( Url::to(\Yii::getAlias($fileCode), true) ));

                        $fileCodeTmp = explode("/", $fileCode);
                        unset($fileCodeTmp[count($fileCodeTmp) - 1]);
                        $prependRelativePath = implode("/", $fileCodeTmp)."/";

                        $contentTmp = \Minify_CSS::minify($contentTmp, [
                            "prependRelativePath" => $prependRelativePath,

                            'compress'         => true,
                            'removeCharsets'   => true,
                            'preserveComments' => true,
                        ]);

                        //$contentTmp = \CssMin::minify($contentTmp);

                        $resultContent[] = $contentTmp;
                    } else {
                        if ($this->cssFileRemouteCompile) {
                            //Try to download the deleted file
                            $resultContent[] = trim($this->fileGetContents($fileCode));
                        } else {
                            $resultFiles[$fileCode] = $fileTag;
                        }
                    }
                }
            } catch (\Exception $e) {
                \Yii::error(__METHOD__.": ".$e->getMessage(), static::class);

                return $files;
            }

            if ($resultContent) {
                $content = implode($resultContent, "\n");

                if (!is_dir($rootDir)) {
                    if (!FileHelper::createDirectory($rootDir, 0777)) {
                        return $files;
                    }
                }

                if ($this->cssFileCompress) {
                    $content = \CssMin::minify($content);
                }

                $page = \Yii::$app->request->absoluteUrl;
                $useFunction = function_exists('curl_init') ? 'curl extension' : 'php file_get_contents';
                $filesString = implode(', ', array_keys($files));

                \Yii::info("Create css file: {$publicUrl} from files: {$filesString} to use {$useFunction} on page '{$page}'", static::className());

                $file = fopen($rootUrl, "w");

                fwrite($file, $content);
                fclose($file);
            }


            if (file_exists($rootUrl)) {
                $publicUrl = $publicUrl."?v=".filemtime($rootUrl);

                $resultFiles[$publicUrl] = Html::cssFile($publicUrl, $this->cssOptions);

                return $resultFiles;
            } else {
                return $files;
            }
        }

        /**
         * @param array $css
         *
         * @return array
         */
        protected function processCss($css = [])
        {
            $newCss = [];

            foreach ($css as $code => $value) {
                $newCss[] = preg_replace_callback(
                    '/<style\b[^>]*>(.*)<\/style>/is',
                    function ($match) {
                        return $match[1];
                        },
                    $value
                );
            }

            $css = implode($newCss, "\n");
            $css = \CssMin::minify($css);

            return [md5($css) => "<style>".$css."</style>"];
        }

        /**
         * @param string $html
         *
         * @return string
         */
        protected function processHtml($html)
        {
            if ($this->htmlFormatter instanceof IAssetHtmlFormatter) {
                $r = new \ReflectionClass($this->htmlFormatter);

                \Yii::beginProfile('Format html: ' . $r->getName());

                $result = $this->htmlFormatter->format($html);

                \Yii::endProfile('Format html: ' . $r->getName());

                return $result;
            }

            \Yii::warning("Html formatter error");

            return $html;
        }
        #endregion

        #region Getters
        /**
         * @return string
         */
        public function getSettingsHash()
        {
            return serialize((array)$this);
        }

        /**
         * @return bool|string
         */
        public function getWebroot()
        {
            return \Yii::getAlias($this->_webroot);
        }
        #endregion

        #region Setters
        /**
         * @param $path
         *
         * @return $this
         */
        public function setWebroot($path)
        {
            $this->_webroot = $path;

            return $this;
        }
        #endregion

        #region Helpers
        /**
         * @param $filePath
         *
         * @return string
         *
         * @throws \Exception
         */
        protected function readLocalFile($filePath)
        {
            if (YII_ENV == 'dev') {
                \Yii::info("Read local files '{$filePath}'");
            }

            if (!file_exists($filePath)) {
                throw new \Exception("Read file error '{$filePath}'");
            }

            $file = fopen($filePath, 'r');

            if (!$file) {
                throw new \Exception("Unable to open file: '{$filePath}'");
            }

            $contents = fread($file, filesize($filePath));

            fclose($file);

            return $contents;
        }

        /**
         * Read file contents
         *
         * @param $file
         *
         * @return string
         */
        protected function fileGetContents($file)
        {
            $client = new Client();

            $response = $client->createRequest()
                ->setMethod('get')
                ->setUrl($file)
                ->addHeaders(['user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36'])
                ->setOptions(['timeout' => $this->readFileTimeout])
                ->send();

            if ($response->isOk) {
                return $response->content;
            }

            throw new \Exception("File get contents '{$file}' error: ".$response->content);
        }
        #endregion
    }
