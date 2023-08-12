<?php

    namespace yiitk\db\mysql;

    use Yii;

    /**
     * Schema
     */
    class Schema extends \yii\db\mysql\Schema
    {
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingParentCallCommonInspection
         */
        public function createQueryBuilder()
        {
            return Yii::createObject(QueryBuilder::class, [$this->db]);
        }
    }
