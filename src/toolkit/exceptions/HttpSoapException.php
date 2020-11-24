<?php

    namespace yiitk\exceptions;

    use yii\base\Exception;

    /**
     * Class HttpSoapException
     */
    class HttpSoapException extends Exception
    {
        /**
         * @return string
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function getName()
        {
            return 'HttpSoapClient Exception.';
        }
    }
