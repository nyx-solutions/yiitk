<?php

    namespace yiitk\helpers;

    use yii\base\Exception;

    /**
     * HashableHelper
     */
    class HashableHelper extends UrlHelper
    {
        /**
         * This method can be used to generate a unique hash.
         *
         * @param bool   $useCurlyBrackets Indicate to the method that the hash must be placed between curly braces.
         * @param string $customPrefix     Custom prefix to the php [[uniqid]] function.
         *
         * @return string
         */
        public static function hash(bool $useCurlyBrackets = true, string $customPrefix = ''): string
        {
            $prefix = $customPrefix;

            if (empty($prefix)) {
                $prefix = mt_rand().date('YmdHis');
            }

            $hash = sha1(uniqid($prefix, true));

            if ($useCurlyBrackets) {
                $hash = sprintf('{%s}', $hash);
            }

            return $hash;
        }

        /**
         * This method can be used to generate a unique hash validated by a callable callback method passed as an argument.
         * Note that in order to realize a correct verification, the callback method must return a boolean. For performance reasons,
         * this method receives a parameter indicating the maximum loops that the method can perform.
         *
         * ```php
         * use yiitk\helpers\HashableHelper;
         *
         * HashableHelper(
         *     fn ($hash) => true
         * );
         * ```
         *
         * @param callable $validator        The callable validator is used to indicate if the generated hash is unique. If the return value is equal to true, then the generator tries to get a new hash and then validate it.
         * @param bool     $useCurlyBrackets Indicate to the method that the hash must be placed between curly braces.
         * @param string   $customPrefix     Custom prefix to the php [[uniqid]] function.
         * @param int      $maxLoops         The maximum loops that this method can perform. Throws an exception if the maximum amount is reached.
         *
         * @return string
         *
         * @throws Exception
         */
        public static function uniqueHash(callable $validator, bool $useCurlyBrackets = true, string $customPrefix = '', int $maxLoops = 100): string
        {
            if (!is_callable($validator)) {
                return static::hash($useCurlyBrackets, $customPrefix);
            }

            $hash = static::hash($useCurlyBrackets, $customPrefix);

            $loop = 1;

            while ($validator($hash)) {
                if ($loop > $maxLoops) {
                    throw new Exception("The maximum amount of {$maxLoops} loops has been reached in the hash generation process. Consider verifying if the validator callback is returning the boolean value correctly.");
                }

                $hash = static::hash($useCurlyBrackets, $customPrefix);

                $loop++;
            }

            return $hash;
        }
    }
