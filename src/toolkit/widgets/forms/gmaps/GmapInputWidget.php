<?php

    /**
     * @noinspection PhpMissingFieldTypeInspection
     */

    namespace yiitk\widgets\forms\gmaps;

    use Yii;
    use yii\base\InvalidConfigException;
    use yii\widgets\InputWidget;

    /**
     * Class GmapInputWidget
     *
     * @category Widget
     * @author   Jonatas Sas
     */
    class GmapInputWidget extends InputWidget
    {
        public const DEFAULT_INITIAL_LATITUDE = -23.5505199;
        public const DEFAULT_INITIAL_LONGITUDE = -46.63330939999997;

        /**
         * @inheritdoc
         */
        public $name = 'gmap';

        /**
         * @var int
         */
        public $mapId;

        /**
         * @var string
         */
        public $latitudeFieldId;

        /**
         * @var string
         */
        public $longitudeFieldId;

        /**
         * @var string|int
         */
        public $finalAddressFieldId = 'undefined';

        /**
         * @var string
         */
        public $searchPlaceholder = '';

        /**
         * @var float
         */
        public $initialLatitude = self::DEFAULT_INITIAL_LATITUDE;

        /**
         * @var float
         */
        public $initialLongitude = self::DEFAULT_INITIAL_LONGITUDE;

        /**
         * @var int
         */
        public $mapHeight = 400;

        /**
         * @var int
         */
        public $mapMarginBottom = 25;

        /**
         * @var string
         */
        public $googleApiKey = '';

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

            if (empty($this->latitudeFieldId) || empty($this->longitudeFieldId)) {
                throw new InvalidConfigException("Both 'latitudeFieldId' and 'longitudeFieldId' fields are required.");
            }

            if (empty($this->initialLatitude)) {
                $this->initialLatitude = self::DEFAULT_INITIAL_LATITUDE;
            }

            if (empty($this->initialLongitude)) {
                $this->initialLongitude = self::DEFAULT_INITIAL_LONGITUDE;
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
            if ($this->hasModel()) {
                throw new InvalidConfigException('This widget does not accept Models.');
            }

            echo $this->_styles();
            echo $this->_scripts();
            echo $this->_html();
        }

        /**
         * @return string
         */
        private function _styles(): string
        {
            $id              = $this->id;
            $mapId           = $this->mapId;
            $mapHeight       = (int)$this->mapHeight;
            $mapMarginBottom = (int)$this->mapMarginBottom;

            return /** @lang TEXT */ <<<STYLES
<style type="text/css">
    div#gmap-{$id}-{$mapId}-canvas{height:{$mapHeight}px;margin:0 0 {$mapMarginBottom}px 0;padding:0}

    input.gmap-{$id}-{$mapId}-controls{margin-top:16px;border:1px solid transparent;border-radius:2px 0 0 2px;box-sizing:border-box;-moz-box-sizing:border-box;height:32px;outline:none;box-shadow:0 2px 6px rgba(0, 0, 0, 0.3);}

    input#gmap-{$id}-{$mapId}-search-input{background-color:#fff;font-size:15px;font-weight:300;margin-left:12px;padding:0 11px 0 13px;text-overflow:ellipsis;width:400px;}
    input#gmap-{$id}-{$mapId}-search-input:focus{border-color:#4d90fe;}
</style>
STYLES;
        }

        /**
         * @return string
         */
        private function _scripts(): string
        {
            $id                  = $this->id;
            $mapId               = $this->mapId;
            $latitudeFieldId     = $this->latitudeFieldId;
            $longitudeFieldId    = $this->longitudeFieldId;
            $finalAddressFieldId = $this->finalAddressFieldId;
            $initialLatitude     = $this->initialLatitude;
            $initialLongitude    = $this->initialLongitude;

            return /** @lang TEXT */ <<<SCRIPT
<script type="text/javascript">
    function initializeGmap_{$id}_{$mapId}() {
        var elements = {
            map:       'gmap-{$id}-{$mapId}-canvas',
            search:    'gmap-{$id}-{$mapId}-search-input',
            latitude:  '{$latitudeFieldId}',
            longitude: '{$longitudeFieldId}'
        };

        var mapOptions = {
            zoom:              18,
            panControl:        false,
            zoomControl:       true,
            scaleControl:      false,
            streetViewControl: false,
            scrollwheel:       false,
            center:            new google.maps.LatLng({$initialLatitude}, {$initialLongitude}),
            mapTypeId:         google.maps.MapTypeId.ROADMAP
        }

        var map     = new google.maps.Map(document.getElementById(elements.map), mapOptions);
        var markers = [];
        var input   = (document.getElementById(elements.search));

        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        marker = new google.maps.Marker({map: map, title: 'Pesquisa', position: new google.maps.LatLng({$initialLatitude}, {$initialLongitude})});

        markers.push(marker);

        $('input#' + elements.latitude).val({$initialLatitude});
        $('input#' + elements.longitude).val({$initialLongitude});

        var searchBox = new google.maps.places.SearchBox(input);

        google.maps.event.addListener(
            searchBox,
            'places_changed',
            function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                for (var i = 0, marker; marker = markers[i]; i++) {
                    marker.setMap(null);
                }

                markers = [];

                for (var i = 0, place; place = places[i]; i++) {
                    for (var i = 0, marker; marker = markers[i]; i++) {
                        marker.setMap(null);
                    }

                    marker = new google.maps.Marker({map: map, title: place.name, position: place.geometry.location});

                    try {
                        $('input#' + elements.latitude).val(place.geometry.location.lat());
                        $('input#' + elements.longitude).val(place.geometry.location.lng());
                        if('{$finalAddressFieldId}' !== 'undefined'){
                            $('#' + '{$finalAddressFieldId}').val($("#gmap-{$id}-{$mapId}-search-input").val());
                        }
                    }catch(e){
                        $('input#' + elements.latitude).val({$initialLatitude});
                        $('input#' + elements.longitude).val({$initialLongitude});
                    }

                    markers.push(marker);

                    try {
                        map.setCenter(new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng()));
                    }catch(e){
                        marker = new google.maps.Marker({map: map, title: 'Pesquisa', position: new google.maps.LatLng({$initialLatitude}, {$initialLongitude})});

                        map.setCenter(new google.maps.LatLng({$initialLatitude}, {$initialLongitude}));
                    }
                }
            }
        );

        google.maps.event.addListener(
            map,
            'bounds_changed',
            function() {
                var bounds = map.getBounds();

                searchBox.setBounds(bounds);
            }
        );
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
            $id                = $this->id;
            $mapId             = $this->mapId;
            $searchPlaceholder = $this->searchPlaceholder;

            return /** @lang TEXT */ <<<HTML
<input id="gmap-{$id}-{$mapId}-search-input" class="gmap-{$id}-{$mapId}-controls" type="text" placeholder="{$searchPlaceholder}" autocomplete="off" />
<div id="gmap-{$id}-{$mapId}-canvas"></div>
HTML;
        }
        #endregion
    }
