<?php

    /**
     * @noinspection PhpMissingFieldTypeInspection
     */

    namespace yiitk\i18n;

    use DateTime;
    use Yii;
    use yiitk\enum\base\BaseEnum;
    use yiitk\enum\base\EnumInterface;
    use yiitk\enum\BooleanEnum;
    use yiitk\helpers\HtmlHelper as Html;
    use yiitk\helpers\MaskHelper;
    use yiitk\helpers\NumberHelper;
    use yiitk\helpers\StringHelper;

    /**
     * Formatter
     */
    class Formatter extends \yii\i18n\Formatter
    {
        /**
         * @var null
         */
        public $nullDisplay = null;

        #region Formatters
        #region ENUM
        /**
         * @param string|BaseEnum $value
         * @param bool            $colorable
         * @param bool            $iconable
         * @param string          $iconFontSize
         *
         * @return string
         */
        public function asEnum($value, bool $colorable = false, bool $iconable = false, string $iconFontSize = '20px'): string
        {
            if ($value instanceof BaseEnum) {
                if ($iconable && $value::iconable()) {
                    $label = Html::encode($value->label);

                    if ($colorable && $value::colorable()) {
                        return <<<HTML
<span style="color:{$value->foregroundColor} !important;font-size:{$iconFontSize} !important;" title="{$label}">{$value->icon}</span>
HTML;
                    }

                    return <<<HTML
<span style="font-size:{$iconFontSize} !important;" title="{$label}">{$value->icon}</span>
HTML;
                }

                if ($colorable && $value::colorable()) {
                    return <<<HTML
<span style="color:{$value->foregroundColor} !important;font-weight:bold !important;">{$value->label}</span>
HTML;
                }

                return $value->label;
            }

            return $this->nullDisplay;
        }

        /**
         * @param mixed              $value
         * @param string             $enumClass
         * @param EnumInterface|null $default
         *
         * @return EnumInterface
         */
        public function asGuessEnum(mixed $value, string $enumClass = BooleanEnum::class, EnumInterface $default = null): EnumInterface
        {
            if (is_subclass_of($enumClass, EnumInterface::class)) {
                $enum = $enumClass::guess($value, $default, true);

                if ($enum instanceof EnumInterface) {
                    return $enum;
                }
            }

            return $default;
        }
        #endregion

        #region Brazilian Zicodes (CEP)
        /**
         * @param string|null $value
         *
         * @return string
         */
        public function asZipcode(?string $value): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 8, '0', STR_PAD_LEFT), 'zipcode');
        }
        #endregion

        #region Phones
        /**
         * @param string|null $value
         *
         * @return string
         */
        public function asPhone(?string $value): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return MaskHelper::maskPhone($value);
        }

        /**
         * @param string|null $value
         *
         * @return string
         */
        public function asTel(?string $value): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return 'tel:+55'.StringHelper::justNumbers($value);
        }
        #endregion

        #region Brazilian TaxId (CPF e CNPJ)
        /**
         * @param string|null $value
         *
         * @return string|null
         */
        public function asPersonTaxId(?string $value): ?string
        {
            if (empty($value)) {
                return null;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 11, '0', STR_PAD_LEFT), 'person-tax-id');
        }

        /**
         * @param string|null $value
         *
         * @return string|null
         */
        public function asCompanyTaxId(?string $value): ?string
        {
            if (empty($value)) {
                return null;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 14, '0', STR_PAD_LEFT), 'company-tax-id');
        }

        /**
         * @param string|null $value
         *
         * @return string|null
         */
        public function asTaxId(?string $value): ?string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            if (strlen($value) <= 11) {
                return $this->asPersonTaxId($value);
            }

            return $this->asCompanyTaxId($value);
        }
        #endregion

        #region Name & Surname
        /**
         * @param string|null $value
         *
         * @return string
         */
        public function asName(?string $value): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return StringHelper::asFirstName($value);
        }

        /**
         * @param string|null $value
         *
         * @return string
         */
        public function asSurname(?string $value): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return StringHelper::asLastName($value);
        }
        #endregion

        #region Sensitive
        /**
         * @param string|null $value
         * @param string      $hidden
         *
         * @return string
         */
        public function asSensitive(?string $value, string $hidden = '******'): string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            $value = Html::encode(strip_tags($value));

            return Html::tag('span', $hidden, ['class' => 'sensitive-data', 'onclick' => "$(this).html('{$value}').attr('title', '').css('cursor', 'auto');", 'style' => 'cursor:pointer;', 'title' => Yii::t('yiitk', 'Click to view real content...')]);
        }
        #endregion

        #region Brazilian Reais
        /**
         * @param mixed $amount
         * @param bool  $withPrefix
         * @param float $onNull
         *
         * @return string|null
         */
        public function asBrazilianCurrency($amount, bool $withPrefix = true, $onNull = 0.00): ?string
        {
            if (!is_numeric($amount)) {
                $amount = $onNull;
            }

            if (is_numeric($amount)) {
                $amount = (float)$amount;

                return NumberHelper::toBrazilianCurrency($amount, $withPrefix);
            }

            return null;
        }

        /**
         * @param mixed        $amount
         * @param bool         $withPrefix
         * @param float|string $onNull
         *
         * @return string|null
         */
        public function asMoney(mixed $amount, bool $withPrefix = true, mixed $onNull = 0.00): ?string
        {
            if ($amount === null) {
                return $this->nullDisplay;
            }

            return $this->asBrazilianCurrency($amount, $withPrefix, $onNull);
        }
        #endregion

        #region Percentage
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function asPercent($value, $decimals = null, $options = [], $textOptions = [])
        {
            return $this->asPercentage($value);
        }

        /**
         * @param mixed $value
         * @param bool  $withPrefix
         * @param mixed $onNull
         *
         * @return string|null
         */
        public function asPercentage($value, bool $withPrefix = true, $onNull = 0.00)
        {
            if (!is_numeric($value)) {
                $value = $onNull;
            }

            if (is_numeric($value)) {
                $value = (float)$value;

                return NumberHelper::toPercentText($value, $withPrefix);
            }

            return null;
        }
        #endregion

        #region Numbers
        /**
         * @param mixed  $value
         * @param int    $decimals
         * @param string $decimalSeparator
         * @param string $thousandsSeparator
         *
         * @return string|null
         */
        public function asNumeric(mixed $value, int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = '.'): ?string
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            if (is_numeric($value)) {
                return number_format($value, $decimals, $decimalSeparator, $thousandsSeparator);
            }

            return (string)$value;
        }
        #endregion

        #region HTML
        /**
         * @param mixed $value
         *
         * @return string|null
         */
        public function asHtmlText(mixed $value): ?string
        {
            if ($value === null) {
                return $this->nullDisplay;
            }

            return nl2br((string)$value);
        }
        #endregion

        #region Date Time
        /**
         * @param mixed  $value
         * @param bool   $returnDateTime
         * @param string $returnFormat
         *
         * @return string|DateTime|null
         */
        public function asGuessDate(mixed $value, bool $returnDateTime = false, string $returnFormat = 'Y-m-d'): string|DateTime|null
        {
            $formats = [
                'Y-m-d'       => '/^([\d]{4})\-([\d]{2})\-([\d]{2})$/',
                'Y-m-d H:i'   => '/^([\d]{4})\-([\d]{2})\-([\d]{2}) ([\d]{2})\:([\d]{2})$/',
                'Y-m-d H:i:s' => '/^([\d]{4})\-([\d]{2})\-([\d]{2}) ([\d]{2})\:([\d]{2})\:([\d]{2})$/',
                'd/m/Y'       => '/^([\d]{2})\/([\d]{2})\/([\d]{4})$/',
                'd/m/Y H:i'   => '/^([\d]{2})\/([\d]{2})\/([\d]{4}) ([\d]{2})\:([\d]{2})$/',
                'd/m/Y H:i:s' => '/^([\d]{2})\/([\d]{2})\/([\d]{4}) ([\d]{2})\:([\d]{2})\:([\d]{2})$/',
            ];

            if (!empty($value) && is_string($value)) {
                $value = $this->_normalizeDate($value);

                foreach ($formats as $format => $pattern) {
                    if (preg_match($pattern, $value)) {
                        $date = DateTime::createFromFormat($format, $value);

                        if ($date instanceof DateTime) {
                            if ($returnDateTime) {
                                return $date;
                            }

                            return $date->format($returnFormat);
                        }
                    }
                }
            }

            return null;
        }

        #region Helpers
        /**
         * @param string $value
         *
         * @return string
         */
        private function _normalizeDate(string $value): string
        {
            if (preg_match('/^([\d]{1,2})\/([\d]{1,2})\/([\d]{2,4}) ?([\d]{1,2})?:?([\d]{1,2})?:?([\d]{1,2})?$/', $value, $matches)) {
                $day     = (int)($matches[1] ?? 0);
                $month   = (int)($matches[2] ?? 0);
                $year    = (int)($matches[3] ?? 0);
                $hours   = (int)($matches[4] ?? 0);
                $minutes = (int)($matches[5] ?? 0);
                $seconds = (int)($matches[6] ?? 0);

                $day     = str_pad((string)$day, 2, '0', STR_PAD_LEFT);
                $month   = str_pad((string)$month, 2, '0', STR_PAD_LEFT);
                $year    = ((strlen($year) === 2) ? "20{$year}" : $year);
                $hours   = str_pad((string)$hours, 2, '0', STR_PAD_LEFT);
                $minutes = str_pad((string)$minutes, 2, '0', STR_PAD_LEFT);
                $seconds = str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);

                return sprintf('%s/%s/%s %s:%s:%s', $day, $month, $year, $hours, $minutes, $seconds);
            }

            if (preg_match('/^([\d]{2,4})-([\d]{1,2})-([\d]{1,2}) ?([\d]{1,2})?:?([\d]{1,2})?:?([\d]{1,2})?$/', $value, $matches)) {
                $year    = (int)($matches[1] ?? 0);
                $month   = (int)($matches[2] ?? 0);
                $day     = (int)($matches[3] ?? 0);
                $hours   = (int)($matches[4] ?? 0);
                $minutes = (int)($matches[5] ?? 0);
                $seconds = (int)($matches[6] ?? 0);

                $day     = str_pad((string)$day, 2, '0', STR_PAD_LEFT);
                $month   = str_pad((string)$month, 2, '0', STR_PAD_LEFT);
                $year    = ((strlen($year) === 2) ? "20{$year}" : $year);
                $hours   = str_pad((string)$hours, 2, '0', STR_PAD_LEFT);
                $minutes = str_pad((string)$minutes, 2, '0', STR_PAD_LEFT);
                $seconds = str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);

                return sprintf('%s/%s/%s %s:%s:%s', $day, $month, $year, $hours, $minutes, $seconds);
            }

            return $value;
        }
        #endregion
        #endregion
        #endregion
    }
