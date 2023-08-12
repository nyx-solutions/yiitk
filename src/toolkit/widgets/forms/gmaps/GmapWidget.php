<?php

    namespace yiitk\widgets\forms\gmaps;

    use Yii;
    use yii\base\Widget;

    /**
     * Class GmapInputWidget
     *
     * @category Widget
     * @author   Jonatas Sas
     *
     * @package  common\widgets\forms\gmaps
     */
    class GmapWidget extends Widget
    {
        public const DEFAULT_LATITUDE = -23.5505199;
        public const DEFAULT_LONGITUDE = -46.63330939999997;

        /**
         * @var string
         */
        public string $name = 'gmap';

        /**
         * @var int|null
         */
        public ?int $mapId = null;

        /**
         * @var string
         */
        public string $searchPlaceholder = '';

        /**
         * @var float
         */
        public float $latitude = self::DEFAULT_LATITUDE;

        /**
         * @var float
         */
        public float $longitude = self::DEFAULT_LONGITUDE;

        /**
         * @var int
         */
        public int $mapHeight = 400;

        /**
         * @var int
         */
        public int $mapMarginBottom = 25;

        /**
         * @var string
         */
        public string $googleApiKey = '';

        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            $this->mapId = date('YmdHis').random_int(100000, 999999);

            $view = $this->getView();

            if (empty($this->latitude)) {
                $this->latitude = self::DEFAULT_LATITUDE;
            }

            if (empty($this->longitude)) {
                $this->longitude = self::DEFAULT_LONGITUDE;
            }

            if (empty($this->searchPlaceholder)) {
                $this->searchPlaceholder = Yii::t('yiitk', 'Enter a location...');
            }

            GmapInputWidgetAsset::$googleApiKey = $this->googleApiKey;

            GmapInputWidgetAsset::register($view);
        }
        #endregion

        #region Run
        /**
         * @inheritdoc
         */
        public function run()
        {
            echo $this->_styles();
            echo $this->_scripts();
            echo $this->_html();
        }

        /**
         * @return string
         */
        private function _styles(): string
        {
            $id        = $this->id;
            $mapId     = $this->mapId;
            $mapHeight = (int)$this->mapHeight;

            return /** @lang TEXT */ <<<STYLES
<style type="text/css">
    div#gmap-{$id}-{$mapId}-canvas{height:{$mapHeight}px;margin:0;padding:0}
</style>
STYLES;
        }

        /**
         * @return string
         */
        private function _scripts(): string
        {
            $id        = $this->id;
            $mapId     = $this->mapId;
            $latitude  = $this->latitude;
            $longitude = $this->longitude;

            return /** @lang TEXT */ <<<SCRIPT
<script type="text/javascript">
    function initializeGmap_{$id}_{$mapId}() {
        var elements = {
            map:       'gmap-{$id}-{$mapId}-canvas',
            search:    'gmap-{$id}-{$mapId}-search-input'
        };

        var mapOptions = {
            zoom:              18,
            panControl:        false,
            zoomControl:       true,
            scaleControl:      false,
            streetViewControl: false,
            scrollwheel:       false,
            center:            new google.maps.LatLng({$latitude}, {$longitude}),
            mapTypeId:         google.maps.MapTypeId.ROADMAP
        };

        var map     = new google.maps.Map(document.getElementById(elements.map), mapOptions);
        var markers = [];
        var input   = (document.getElementById(elements.search));

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        marker = new google.maps.Marker({map: map, title: '', position: new google.maps.LatLng({$latitude}, {$longitude})});

        markers.push(marker);
    }

    google.maps.event.addDomListener(window, 'load', initializeGmap_{$id}_{$mapId});
</script>
SCRIPT;
        }

        /**
         * @return string
         */
        private function _html(): string
        {
            $id    = $this->id;
            $mapId = $this->mapId;

            return /** @lang TEXT */ <<<HTML
<div id="gmap-{$id}-{$mapId}-canvas"></div>
HTML;
        }
        #endregion
    }
