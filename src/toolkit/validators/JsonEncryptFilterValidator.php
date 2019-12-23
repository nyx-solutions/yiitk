<?php

    namespace yiitk\validators;

    use yiitk\helpers\StringHelper;

    /**
     * Class JsonEncryptFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class JsonEncryptFilterValidator extends FilterValidator
    {
        /**
         * @inheritdoc
         */
        public function init()
        {
            $this->addFilter(
                function ($value) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);

                    $cipher = 'aes-128-gcm';

                    if (in_array($cipher, openssl_get_cipher_methods()))
                    {
                        $ivlen = openssl_cipher_iv_length($cipher);
                        $iv = openssl_random_pseudo_bytes($ivlen);
                        $ciphertext = openssl_encrypt($value, $cipher, $key, $options=0, $iv, $tag);
                        //store $cipher, $iv, and $tag for decryption later
                        $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
                        echo $original_plaintext."\n";
                    }
                    return StringHelper::justNumbers($value);
                }
            );

            parent::init();
        }
    }
