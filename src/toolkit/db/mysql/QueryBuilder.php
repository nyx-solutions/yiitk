<?php

    namespace yiitk\db\mysql;

    use yiitk\db\QueryBuilderInterface;
    use yiitk\db\QueryBuilderTrait;
    use yii\db\mysql\QueryBuilder as YiiQueryBuilder;

    /**
     * Query Builder
     */
    class QueryBuilder extends YiiQueryBuilder implements QueryBuilderInterface
    {
        use QueryBuilderTrait;
    }
