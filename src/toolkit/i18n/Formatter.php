<?php

    namespace yiitk\i18n;

    use Yii;
    use yiitk\enum\base\BaseEnum;
    use yiitk\helpers\HtmlHelper as Html;
    use yiitk\helpers\MaskHelper;
    use yiitk\helpers\NumberHelper;
    use yiitk\helpers\StringHelper;

    /**
     * Formatter
     */
    class Formatter extends \yii\i18n\Formatter
    {
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
        #endregion
    }
