<?php

    namespace yiitk\file;

    use yii\base\Component;
    use yii\base\InvalidConfigException;
    use yii\db\Migration;
    use yiitk\Module;

    /**
     * Class FileManager
     */
    class FileManager extends Component
    {
        /**
         * @var string
         */
        public $fileTable = '{{%file}}';

        /**
         * @var bool
         */
        public $useBigIntegerPk = true;

        /**
         * @var bool
         */
        public $useBigIntegerFk = true;

        /**
         * @var integer
         */
        public $pkLength = 20;

        /**
         * @var integer
         */
        public $fkLength = 20;

        /**
         * @var string
         */
        public $fkFieldSuffix = 'Id';

        //region Migrations Helpers
        /**
         * @param Migration $migration
         * @param string    $name
         * @param string    $table
         * @param string    $afterColumn
         * @param string    $refFkName
         */
        public static function addFileAttributes(Migration $migration, $name, $table, $afterColumn, $refFkName)
        {
            $fileManager = static::findFileManager();

            $dataFieldName = "{$name}";
            $idFieldName   = "{$name}{$fileManager->fkFieldSuffix}";

            $migration->addColumn($table, $dataFieldName, $migration->json()->null()->after($afterColumn));

            if ($fileManager->useBigIntegerFk) {
                $migration->addColumn($table, $idFieldName, $migration->bigInteger($fileManager->fkLength)->null()->after($dataFieldName));
            } else {
                $migration->addColumn($table, $idFieldName, $migration->integer($fileManager->fkLength)->null()->after($dataFieldName));
            }

            $migration->addForeignKey($refFkName, $table, $idFieldName, $fileManager->fileTable, 'id', 'RESTRICT', 'RESTRICT');
        }

        /**
         * @param Migration $migration
         * @param string    $name
         * @param string    $table
         */
        public static function dropFileAttributes(Migration $migration, $name, $table)
        {
            $fileManager = static::findFileManager();

            $dataFieldName = "{$name}";
            $idFieldName   = "{$name}{$fileManager->fkFieldSuffix}";

            $migration->dropColumn($table, $dataFieldName);
            $migration->dropColumn($table, $idFieldName);
        }

        /**
         * @return static
         *
         * @throws InvalidConfigException
         */
        protected static function findFileManager()
        {
            /** @var Module $yiitk */
            $yiitk = Module::getInstance();

            /** @var static $fileManager */
            $fileManager = $yiitk->get('fileManager', true);

            if (!$fileManager instanceof static) {
                throw new InvalidConfigException('The fileManager component must be an instance of '.static::class);
            }

            return $fileManager;
        }
        //endregion
    }
