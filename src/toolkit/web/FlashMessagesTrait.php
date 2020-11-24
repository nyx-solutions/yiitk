<?php

    namespace yiitk\web;

    use Exception;
    use Yii;
    use yii\web\Session;

    /**
     * Trait FlashMessagesTrait
     *
     * @package yiitk\web
     */
    trait FlashMessagesTrait
    {
        /**
         * @var bool
         */
        protected bool $enableFlashMessages = true;

        //region Session

        /**
         * @return bool|Session
         */
        private function _getSession()
        {
            if (!$this->enableFlashMessages) {
                return false;
            }

            try {
                /** @var Session $session */
                $session = Yii::$app->get('session', false);

                if ($session instanceof Session) {
                    return $session;
                }
            } catch (Exception $exception) {
            }

            return false;
        }
        //endregion

        //region Flash Messages
        /**
         * @param string $message           flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         *                                  it is accessed. If false, the flash message will be automatically removed after the next request,
         *                                  regardless if it is accessed or not. If true (default value), the flash message will remain until after
         *                                  it is accessed.
         */
        public function addSuccessMessage(string $message, bool $removeAfterAccess = true): void
        {
            if (!$this->enableFlashMessages) {
                return;
            }

            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('success', $message, $removeAfterAccess);
            }
        }

        /**
         * @param string $message           flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         *                                  it is accessed. If false, the flash message will be automatically removed after the next request,
         *                                  regardless if it is accessed or not. If true (default value), the flash message will remain until after
         *                                  it is accessed.
         */
        public function addErrorMessage(string $message, bool $removeAfterAccess = true): void
        {
            if (!$this->enableFlashMessages) {
                return;
            }

            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('error', $message, $removeAfterAccess);
            }
        }

        /**
         * @param string $message           flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         *                                  it is accessed. If false, the flash message will be automatically removed after the next request,
         *                                  regardless if it is accessed or not. If true (default value), the flash message will remain until after
         *                                  it is accessed.
         */
        public function addWarningMessage(string $message, bool $removeAfterAccess = true): void
        {
            if (!$this->enableFlashMessages) {
                return;
            }

            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('warning', $message, $removeAfterAccess);
            }
        }

        /**
         * @param string $message           flash message
         * @param bool   $removeAfterAccess whether the flash message should be automatically removed only if
         *                                  it is accessed. If false, the flash message will be automatically removed after the next request,
         *                                  regardless if it is accessed or not. If true (default value), the flash message will remain until after
         *                                  it is accessed.
         */
        public function addInfoMessage(string $message, bool $removeAfterAccess = true): void
        {
            if (!$this->enableFlashMessages) {
                return;
            }

            if (($session = $this->_getSession()) !== false) {
                $session->addFlash('info', $message, $removeAfterAccess);
            }
        }
        //endregion
    }
