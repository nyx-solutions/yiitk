<?php

    namespace yiitk\exceptions;

    use yii\base\Exception;

    /**
     * Class HttpSoapException
     *
     * @package yiitk\exceptions
     */
    class HttpSoapException extends Exception
    {
        /**
         * @return string
         */
        public function getName()
        {
            return 'HttpSoapClient Exception.';
        }
    }
