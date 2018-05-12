<?php

    namespace yiitk\exceptions;

    use yii\base\Exception;

    /**
     * Class InvalidVariableTypeException
     */
    class InvalidVariableTypeException extends Exception
    {
        /**
         * @param string $type
         */
        public function __construct($type)
        {
            $this->message = "The variable must be of the type \"{$type}\".";
            $this->code    = 0;
        }
    }
