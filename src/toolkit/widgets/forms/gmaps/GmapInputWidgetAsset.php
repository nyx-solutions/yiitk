<?php

    /**
     * @noinspection PhpMissingFieldTypeInspection
     */

    namespace yiitk\widgets\forms\gmaps;

    use yii\web\AssetBundle;
    use yii\web\View;
    use yii\web\JqueryAsset;

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
        public static string $googleApiKey = '';

        /**
         * @inheritdoc
         */
        public $jsOptions = ['position' => View::POS_HEAD];

        /**
         * @inheritdoc
         */
        public $depends = [JqueryAsset::class];

        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            $apiKey = static::$googleApiKey;

            $this->js = ['https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places'.((!empty($apiKey)) ? "&key={$apiKey}" : '')];
        }
        #endregion
    }
