<?php

    namespace yiitk\web\http\soap;

    use Exception;
    use SoapClient;
    use SoapFault;
    use yii\base\Component;
    use yii\base\InvalidConfigException;
    use yiitk\exceptions\HttpSoapException;
    use yiitk\helpers\UrlHelper;

    /**
     * Class HttpSoapClient
     *
     * @package yiitk\web\http\soap
     */
    class HttpSoapClient extends Component
    {
        /**
         * @var string|null
         */
        public ?string $endpoint;

        /**
         * @var array the array of SOAP client options.
         */
        public array $options = [];

        /**
         * @var SoapClient|null the SOAP client instance.
         */
        private ?SoapClient $_soapClient;

        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            if (is_null($this->endpoint) || empty($this->endpoint) || UrlHelper::validate($this->endpoint)) {
                throw new InvalidConfigException('The Endpoint URL property must be set.');
            }

            try {
                $this->_soapClient = new SoapClient($this->endpoint, $this->options);
            } catch (SoapFault $exception) {
                throw new HttpSoapException($exception->getMessage(), (int)$exception->getCode(), $exception);
            }
        }
        #endregion

        #region Magic Methods
        /**
         * @param string $name
         * @param array  $arguments
         *
         * @return mixed
         *
         * @throws Exception
         *
         * @noinspection PhpMissingParamTypeInspection
         */
        public function __call($name, $arguments)
        {
            try {
                return call_user_func_array([$this->_soapClient, $name], $arguments);
            } catch (Exception $exception) {
                throw new HttpSoapException($exception->getMessage(), (int)$exception->getCode(), $exception);
            }
        }
        #endregion
    }
