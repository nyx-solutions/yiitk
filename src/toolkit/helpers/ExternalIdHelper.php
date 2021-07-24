<?php

    namespace yiitk\helpers;

    /**
     * Helper: External ID
     */
    class ExternalIdHelper
    {
        /**
         * @return string
         */
        public static function generate(): string
        {
            return HashableHelper::hash(false);
        }
    }
