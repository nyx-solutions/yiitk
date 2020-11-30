<?php

    namespace yiitk\hashids;

    use Hashids\Hashids as BaseHashids;
    use yii\base\BaseObject;

    /**
     * This is a wrapper for Hashids.
     *
     * @method string encode(...$params)
     * @method mixed decode(string $id)
     * @method string encodeHex(string $id)
     * @method string decodeHex(string $id)
     *
     * @version 1.0.2
     *
     * @author  lichunqiang<light-li@hotmail.com>
     * @license MIT
     */
    class Hashids extends BaseObject
    {
        /**
         * The salt.
         *
         * @var string|null
         */
        public ?string $salt = null;

        /**
         * The min hash length.
         *
         * @var int
         */
        public int $minHashLength = 0;

        /**
         * The alphabet for hashids.
         *
         * @var string
         */
        public string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        /**
         * The instance of the Hashids.
         *
         * @var BaseHashids|null
         */
        private ?BaseHashids $_hashids = null;

        /**
         * {@inheritdoc}
         */
        public function init()
        {
            parent::init();

            $this->_hashids = new BaseHashids($this->salt, $this->minHashLength, $this->alphabet);
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function __call($name, $params)
        {
            if (method_exists($this->_hashids, $name)) {
                return call_user_func_array([$this->_hashids, $name], $params);
            }

            return parent::__call($name, $params);
        }
    }
