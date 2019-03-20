<?php

    namespace yiitk\behaviors;

    use yii\base\Behavior;
    use yii\base\Exception;
    use yii\base\InvalidArgumentException;
    use yii\db\BaseActiveRecord;
    use yii\i18n\Formatter;
    use yiitk\helpers\ArrayHelper;

    /**
     * Class DateConverterBehavior
     *
     * @property BaseActiveRecord $owner
     */
    class DateConverterBehavior extends Behavior
    {
        const EVENT_SAVE = 'save';
        const EVENT_FIND = 'find';

        /**
         * @var array
         */
        public $attributes = [];

        /**
         * @var Formatter
         */
        public $formatter;

        /**
         * @var string
         */
        public $dateSaveFormat = 'Y-m-d';

        /**
         * @var string
         */
        public $datetimeSaveFormat = 'Y-m-d H:i:s';

        /**
         * @var string
         */
        public $timeSaveFormat = 'H:i:s';

        /**
         * @var string
         */
        public $dateDisplayFormat = 'Y-m-d';

        /**
         * @var string
         */
        public $datetimeDisplayFormat = 'Y-m-d H:i:s';

        /**
         * @var string
         */
        public $timeDisplayFormat = 'H:i:s';

        /**
         * @var string
         */
        public $userTimezone;

        /**
         * @inheritdoc
         */
        public function init()
        {
            if (is_null($this->formatter)) {
                $this->formatter = \Yii::$app->formatter;
            } elseif (is_array($this->formatter)) {
                $this->formatter = \Yii::createObject($this->formatter);
            }

            $this->userTimezone = $this->userTimezone ?? \Yii::$app->timeZone ?? 'GMT';
        }

        /**
         * @inheritdoc
         */
        public function events()
        {
            return [
                BaseActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeSave',
                BaseActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeSave',
                BaseActiveRecord::EVENT_AFTER_FIND    => 'onAfterFind',
            ];
        }

        /**
         * Before save event
         *
         * @return void
         */
        public function onBeforeSave()
        {
            $this->updateAttributes($this->saveFormats(), self::EVENT_SAVE);
        }

        /**
         * After find event
         *
         * @return void
         */
        public function onAfterFind()
        {
            $this->updateAttributes($this->displayFormats(), self::EVENT_FIND);
        }

        /**
         * Update attributes
         *
         * @param array  $formats
         * @param string $event
         *
         * @throws InvalidArgumentException|Exception
         */
        protected function updateAttributes($formats, $event)
        {
            foreach ($this->attributes as $attribute => $format) {
                if ($value = $this->owner->getAttribute($attribute)) {
                    if (!$dateFormat = ArrayHelper::getValue($formats, $format)) {
                        throw new InvalidArgumentException('The $format property has an incorrect value.');
                    }

                    if (!is_int($value)) {
                        $fromFormats = (($event === self::EVENT_SAVE) ? $this->displayFormats() : $this->saveFormats());
                        $date        = \DateTime::createFromFormat(ArrayHelper::getValue($fromFormats, $format), $value);
                        $formatDate  = (($date) ? $date->format('Y-m-d H:i:s') : $value);
                    } else {
                        $formatDate = date('Y-m-d H:i:s', $value);
                    }

                    $userTimezone = new \DateTimeZone($this->userTimezone);
                    $gmtTimezone  = new \DateTimeZone('UTC');
                    $myDateTime   = new \DateTime($formatDate, $gmtTimezone);

                    switch ($event) {
                        case self::EVENT_FIND: {
                            $offset = $userTimezone->getOffset($myDateTime);

                            break;
                        }

                        case self::EVENT_SAVE: {
                            $offset = -1 * $userTimezone->getOffset($myDateTime);

                            break;
                        }

                        default: {
                            throw new Exception('Unknown event.');
                        }
                    }

                    $myInterval = \DateInterval::createFromDateString((string)$offset.'seconds');

                    $myDateTime->add($myInterval);

                    $value = $myDateTime->format($dateFormat);

                    $value = (($dateFormat === 'U') ? (int)$value : $value);

                    $this->owner->setAttribute($attribute, $value);
                }
            }
        }

        /**
         * Returns save date formats
         *
         * @return array
         */
        protected function saveFormats()
        {
            return ['date' => $this->dateSaveFormat, 'datetime' => $this->datetimeSaveFormat, 'time' => $this->timeSaveFormat];
        }

        /**
         * Returns display date formats
         *
         * @return array
         */
        protected function displayFormats()
        {
            return ['date' => $this->dateDisplayFormat, 'datetime' => $this->datetimeDisplayFormat, 'time' => $this->timeDisplayFormat];
        }
    }
