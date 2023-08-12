<?php

    namespace yiitk\db\pgsql;

    use yiitk\db\Migration as BaseMigration;
    use yiitk\db\migrations\DomainBuilderInterface;
    use yiitk\db\migrations\EnumBuilderInterface;
    use yiitk\db\SchemaBuilderInterface;

    /**
     * PostgreSQL Migration
     */
    class Migration extends BaseMigration implements SchemaBuilderInterface
    {
        use SchemaBuilderTrait;
    }
