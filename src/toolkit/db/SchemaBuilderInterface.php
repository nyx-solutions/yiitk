<?php

    namespace yiitk\db;

    use yii\base\Exception as YiiException;
    use yii\base\NotSupportedException;
    use yii\db\ColumnSchemaBuilder;

    /**
     * Interface: Schema Builder
     */
    interface SchemaBuilderInterface
    {
        #region Columns
        #region Text
        /**
         * Creates a medium text column.
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function mediumText(array $options = []): ColumnSchemaBuilder;

        /**
         * Creates a long text column.
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function longText(array $options = []): ColumnSchemaBuilder;

        /**
         * Creates a tiny text column.
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function tinyText(array $options = []): ColumnSchemaBuilder;
        #endregion

        #region ENUM
        /**
         * Creates a ENUM column
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function enum(array $options = []): ColumnSchemaBuilder;
        #endregion

        #region JSON
        /**
         * Creates a JSON column.
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function json();
        #endregion

        #region Hash
        /**
         * @param array $options
         *
         * @return ColumnSchemaBuilder
         */
        public function hash(array $options = []): ColumnSchemaBuilder;

        /**
         * @param string   $table
         * @param string   $column
         * @param bool     $dropIfExists
         * @param string   $afterAttribute
         * @param string   $idAttribute
         * @param int|null $length
         * @param int      $maxUpdatePerCicle
         *
         * @return bool
         *
         * @throws YiiException
         */
        public function addHashableColumn(string $table, string $column = 'hash', bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', ?int $length = 45, int $maxUpdatePerCicle = 500): bool;
        #endregion

        #region External ID
        /**
         * @param array $options
         *
         * @return ColumnSchemaBuilder
         */
        public function externalId(array $options = []): ColumnSchemaBuilder;

        /**
         * @param string $table
         * @param string $column
         * @param string $uniqueIndexName
         * @param array  $uniqueIndexAdditionalColumns
         * @param bool   $dropIfExists
         * @param string $afterAttribute
         * @param string $idAttribute
         * @param int    $length
         * @param int    $maxUpdatePerCicle
         *
         * @return bool
         */
        public function addExternalIdColumn(string $table, string $column = 'externalId', string $uniqueIndexName = 'external_id', array $uniqueIndexAdditionalColumns = [], bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', int $length = 45, int $maxUpdatePerCicle = 500): bool;
        #endregion
        #endregion
    }
