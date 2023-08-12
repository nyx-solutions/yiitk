<?php

    namespace yiitk\db\pgsql;

    use Yii;

    /**
     * Schema
     */
    class Schema extends \yii\db\pgsql\Schema
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
