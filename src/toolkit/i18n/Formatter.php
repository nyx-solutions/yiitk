<?php

    namespace yiitk\i18n;

    use yiitk\enum\base\BaseEnum;
    use yiitk\helpers\HtmlHelper as Html;
    use yiitk\helpers\MaskHelper;
    use yiitk\helpers\StringHelper;

    /**
     * Class Formatter
     *
     * @package yiitk\i18n
     */
    class Formatter extends \yii\i18n\Formatter
    {
        #region Formatters
        #region ENUM
        /**
         * @param string $value
         *
         * @return string
         */
        public function asEnum($value)
        {
            if ($value instanceof BaseEnum) {
                return $value->label;
            }

            return $this->nullDisplay;
        }
        #endregion

        #region Brazilian Zicodes (CEP)
        /**
         * @param string $value
         *
         * @return string
         */
        public function asZipcode($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 8, '0', STR_PAD_LEFT), 'zipcode');
        }
        #endregion

        #region Phones
        /**
         * @param string $value
         *
         * @return string
         */
        public function asPhone($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return MaskHelper::maskPhone($value);
        }

        /**
         * @param string $value
         *
         * @return string
         */
        public function asTel($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return ((!empty($value)) ? 'tel:+55'.StringHelper::justNumbers($value) : '');
        }
        #endregion

        #region Brazilian TaxId (CPF e CNPJ)
        /**
         * @param string $value
         *
         * @return string
         */
        public function asPersonTaxId($value)
        {
            if (empty($value)) {
                return null;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 11, '0', STR_PAD_LEFT), 'person-tax-id');
        }

        /**
         * @param string $value
         *
         * @return string
         */
        public function asCompanyTaxId($value)
        {
            if (empty($value)) {
                return null;
            }

            return MaskHelper::mask(str_pad(StringHelper::justNumbers($value), 14, '0', STR_PAD_LEFT), 'company-tax-id');
        }

        /**
         * @param string $value
         *
         * @return string
         */
        public function asTaxId($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            if (strlen((string)$value) <= 11) {
                return $this->asPersonTaxId($value);
            }

            return $this->asCompanyTaxId($value);
        }
        #endregion

        #region Name & Surname
        /**
         * @param string $value
         *
         * @return string
         */
        public function asName($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return StringHelper::asFirstName($value);
        }

        /**
         * @param string $value
         *
         * @return string
         */
        public function asSurname($value)
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            return StringHelper::asLastName($value);
        }
        #endregion

        #region Sensitive
        /**
         * @param string $value
         *
         * @param string $hidden
         *
         * @return string
         */
        public function asSensitive($value, $hidden = '******')
        {
            if (empty($value)) {
                return $this->nullDisplay;
            }

            $value = Html::encode(strip_tags((string)$value));

            return Html::tag('span', $hidden, ['class' => 'sensitive-data', 'onclick' => "$(this).html('{$value}').attr('title', '').css('cursor', 'auto');", 'style' => 'cursor:pointer;', 'title' => \Yii::t('yiitk', 'Click to view real content...')]);
        }
        #endregion
        #endregion
    }
