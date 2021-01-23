<?php

    namespace yiitk\behaviors;

    use DateInterval;
    use DateTime;
    use DateTimeZone;
    use Yii;
    use yii\base\Behavior;
    use yii\base\Exception as YiiException;
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
        public const EVENT_SAVE = 'save';
        public const EVENT_FIND = 'find';

        /**
         * @var array
         */
        public array $attributes = [];

        /**
         * @var Formatter|null
         */
        public ?Formatter $formatter = null;

        /**
         * @var string
         */
        public string $dateSaveFormat = 'Y-m-d';

        /**
         * @var string
         */
        public string $datetimeSaveFormat = 'Y-m-d H:i:s';

        /**
         * @var string
         */
        public string $timeSaveFormat = 'H:i:s';

        /**
         * @var string
         */
        public string $dateDisplayFormat = 'Y-m-d';

        /**
         * @var string
         */
        public string $datetimeDisplayFormat = 'Y-m-d H:i:s';

        /**
         * @var string
         */
        public string $timeDisplayFormat = 'H:i:s';

        /**
         * @var string|null
         */
        public ?string $userTimezone = null;

        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            if (is_null($this->formatter)) {
                $this->formatter = Yii::$app->formatter;
            } elseif (is_array($this->formatter)) {
                $this->formatter = Yii::createObject($this->formatter);
            }

            $this->userTimezone = $this->userTimezone ?? Yii::$app->timeZone ?? 'GMT';
        }
        //endregion

        //region Events
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
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
         *
         * @throws YiiException
         */
        public function onBeforeSave(): void
        {
            $this->updateAttributes($this->saveFormats(), self::EVENT_SAVE);
        }

        /**
         * After find event
         *
         * @return void
         *
         * @throws YiiException
         */
        public function onAfterFind(): void
        {
            $this->updateAttributes($this->displayFormats(), self::EVENT_FIND);
        }
        //endregion

        /**
         * Update attributes
         *
         * @param array  $formats
         * @param string $event
         *
         * @throws InvalidArgumentException|YiiException
         */
        protected function updateAttributes(array $formats, string $event): void
        {
            foreach ($this->attributes as $attribute => $format) {
                if ($value = $this->owner->getAttribute($attribute)) {
                    if (!$dateFormat = ArrayHelper::getValue($formats, $format)) {
                        throw new InvalidArgumentException('The $format property has an incorrect value.');
                    }

                    if (is_int($value)) {
                        $formatDate = date('Y-m-d H:i:s', $value);
                    } else {
                        $fromFormats = (($event === self::EVENT_SAVE) ? $this->displayFormats() : $this->saveFormats());
                        $date        = DateTime::createFromFormat(ArrayHelper::getValue($fromFormats, $format), $value);
                        $formatDate  = (($date) ? $date->format('Y-m-d H:i:s') : $value);
                    }

                    $userTimezone = new DateTimeZone($this->userTimezone);
                    $gmtTimezone  = new DateTimeZone('UTC');
                    $myDateTime   = new DateTime($formatDate, $gmtTimezone);

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
                            throw new YiiException('Unknown event.');
                        }
                    }

                    $myInterval = DateInterval::createFromDateString("{$offset}seconds");

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
        protected function saveFormats(): array
        {
            return ['date' => $this->dateSaveFormat, 'datetime' => $this->datetimeSaveFormat, 'time' => $this->timeSaveFormat];
        }

        /**
         * Returns display date formats
         *
         * @return array
         */
        protected function displayFormats(): array
        {
            return ['date' => $this->dateDisplayFormat, 'datetime' => $this->datetimeDisplayFormat, 'time' => $this->timeDisplayFormat];
        }
    }
