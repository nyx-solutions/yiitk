<?php

    namespace yiitk\widgets\forms\gmaps;

    use yii\web\AssetBundle;
    use yii\web\View;

    /**
     * Class GmapInputWidgetAsset
     *
     * @category Asset
     * @author   Jonatas Sas
     */
    class GmapInputWidgetAsset extends AssetBundle
    {
        /**
         * @var string
         */
        public static $googleApiKey = '';

        /**
         * @inheritdoc
         */
        public $sourcePath = null;

        /**
         * @inheritdoc
         */
        public $css = [];

        /**
         * @inheritdoc
         */
        public $js = [];

        /**
         * @inheritdoc
         */
        public $jsOptions = ['position' => View::POS_HEAD];

        /**
         * @inheritdoc
         */
        public $depends = ['yii\web\JqueryAsset'];

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            $apiKey = static::$googleApiKey;

            $this->js = ['https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places'.((!empty($apiKey)) ? "&key={$apiKey}" : '')];
        }
    }
