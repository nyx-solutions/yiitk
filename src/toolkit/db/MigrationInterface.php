<?php

    namespace yiitk\db;

    use yii\db\Exception as DbException;

    /**
     * Interface: Migration
     */
    interface MigrationInterface
    {
        #region Tables
        /**
         * @param string $table
         *
         * @return bool
         */
        public function tableExists(string $table): bool;
        #endregion

        #region Columns
        /**
         * @param string $table
         * @param string $column
         *
         * @return bool
         */
        public function columnExists(string $table, string $column): bool;

        /**
         * @param string $table
         * @param string $column
         *
         * @return bool
         */
        public function fieldExists(string $table, string $column): bool;
        #endregion

        #region Foreign Keys
        /**
         * Builds a SQL statement for adding a foreign key constraint to an existing table.
         * The method will properly quote the table and column names.
         *
         * @param string       $name       the name of the foreign key constraint.
         * @param string       $table      the table that the foreign key constraint will be added to.
         * @param string|array $columns    the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
         * @param string       $refTable   the table that the foreign key references to.
         * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
         * @param string|null  $delete     the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string|null  $update     the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         */
        public function addUniqueForeignKey(
            string $name,
            string $table,
            string|array $columns,
            string $refTable,
            string|array $refColumns,
            ?string $delete = null,
            ?string $update = null
        ): void;

        /**
         * Builds a SQL statement for adding a foreign key constraint to an existing table (without index creation).
         * The method will properly quote the table and column names.
         *
         * @param string       $name       the name of the foreign key constraint.
         * @param string       $table      the table that the foreign key constraint will be added to.
         * @param string|array $columns    the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
         * @param string       $refTable   the table that the foreign key references to.
         * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
         * @param string|null  $delete     the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string|null  $update     the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         *
         * @see addForeignKey
         */
        public function addForeignKeyWithoutIndex(
            string $name,
            string $table,
            string|array $columns,
            string $refTable,
            string|array $refColumns,
            ?string $delete = null,
            ?string $update = null
        ): void;
        #endregion

        #region Views
        /**
         * @param string $view
         *
         * @return bool
         */
        public function viewExists(string $view): bool;

        /**
         * @param string $view
         * @param string $select
         */
        public function createView(string $view, string $select): void;

        /**
         * @param string $view
         */
        public function dropView(string $view): void;
        #endregion

        #region Table & Field Names
        /**
         * @param string $name
         *
         * @return string
         */
        public function findTableName(string $name = ''): string;

        /**
         * @return string
         */
        public function findCurrentTableName(): string;

        /**
         * @return string
         */
        public function currentTableName(): string;

        /**
         * @param string $name
         * @param int    $max
         * @param bool   $exception
         *
         * @return string
         *
         * @throws DbException
         */
        public function findFieldName(string $name, int $max = -1, bool $exception = false): string;

        /**
         * @param string $name
         *
         * @return string
         */
        public function withPrefix(string $name): string;

        /**
         * @param string $name
         *
         * @return string
         */
        public function withTableName(string $name): string;
        #endregion
    }
