<?php

    namespace yiitk\web;

    use yii\web\Session;

    /**
     * Trait FlashMessagesTrait
     *
     * @package yiitk\web
     */
    trait FlashMessagesTrait
    {
        #region Session
        /**
         * @return bool|Session
         */
        private function _getSession()
        {
            /** @var Session $session */
            $session = \Yii::$app->get('session', false);

            if ($session instanceof Session) {
                return $session;
            }

            return false;
        }
        #endregion

        #region Flash Messages
        /**
         * @param string $message flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         * it is accessed. If false, the flash message will be automatically removed after the next request,
         * regardless if it is accessed or not. If true (default value), the flash message will remain until after
         * it is accessed.
         */
        public function addSuccessMessage($message, $removeAfterAccess = true)
        {
            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('success', (string)$message, (bool)$removeAfterAccess);
            }
        }

        /**
         * @param string $message flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         * it is accessed. If false, the flash message will be automatically removed after the next request,
         * regardless if it is accessed or not. If true (default value), the flash message will remain until after
         * it is accessed.
         */
        public function addErrorMessage($message, $removeAfterAccess = true)
        {
            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('error', (string)$message, (bool)$removeAfterAccess);
            }
        }

        /**
         * @param string $message flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         * it is accessed. If false, the flash message will be automatically removed after the next request,
         * regardless if it is accessed or not. If true (default value), the flash message will remain until after
         * it is accessed.
         */
        public function addWarningMessage($message, $removeAfterAccess = true)
        {
            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('warning', (string)$message, (bool)$removeAfterAccess);
            }
        }

        /**
         * @param string $message flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         * it is accessed. If false, the flash message will be automatically removed after the next request,
         * regardless if it is accessed or not. If true (default value), the flash message will remain until after
         * it is accessed.
         */
        public function addInfoMessage($message, $removeAfterAccess = true)
        {
            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('info', (string)$message, (bool)$removeAfterAccess);
            }
        }
        #endregion
    }
