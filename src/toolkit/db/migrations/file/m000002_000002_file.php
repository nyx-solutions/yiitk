<?php

    namespace yiitk\db\migrations\file;

    use yiitk\db\Migration;
    use yiitk\file\FileManager;
    use yiitk\enum\BooleanEnum;
    use yii\base\InvalidConfigException;

    /**
     * Class m000000_000000_file
     *
     * @property FileManager $fileManager
     */
    class m000002_000002_file extends Migration
    {
        /**
         * {@inheritdoc}
         */
        public function safeUp()
        {
            $this->tableName = $this->fileManager->fileTable;

            if (!$this->tableExists($this->currentTableName())) {
                $this->createTable(
                    $this->tableName,
                    [
                        'id'              => (($this->fileManager->useBigIntegerPk) ? $this->bigPrimaryKey($this->fileManager->pkLength) : $this->primaryKey($this->fileManager->pkLength)),
                        'name'            => $this->string(255)->notNull(),
                        'basePath'        => $this->string(2000)->null(),
                        'baseUrl'         => $this->string(2000)->null(),
                        'originalName'    => $this->string(255)->null(),
                        'extension'       => $this->string(4)->null(),
                        'data'            => $this->json()->null(),
                        'tmpData'         => $this->json()->null(),
                        'tmpPath'         => $this->string(2000)->null(),
                        'tableName'       => $this->string(100)->null(),
                        'tableColumnName' => $this->string(100)->null(),
                        'tableColumnId'   => $this->bigInteger()->null(),
                        'deleteOriginal'  => $this->enum(BooleanEnum::range())->notNull()->defaultValue(BooleanEnum::NO),
                        'deletable'       => $this->enum(BooleanEnum::range())->notNull()->defaultValue(BooleanEnum::NO),
                        'createdAt'       => $this->dateTime()->notNull(),
                        'updatedAt'       => $this->dateTime()->notNull()
                    ]
                );

                $this->createIndex(
                    $this->withTableName('tableName'),
                    $this->currentTableName(),
                    'tableName'
                );

                $this->createIndex(
                    $this->withTableName('tableColumnName'),
                    $this->currentTableName(),
                    'tableColumnName'
                );

                $this->createIndex(
                    $this->withTableName('tableColumnId'),
                    $this->currentTableName(),
                    'tableColumnId'
                );

                $this->createIndex(
                    $this->withTableName('deletable'),
                    $this->currentTableName(),
                    'deletable'
                );
            }
        }

        #region Getters
        /**
         * @return FileManager
         *
         * @throws InvalidConfigException
         */
        protected function getFileManager()
        {
            /** @var FileManager $fileManager */
            $fileManager = \Yii::$app->get('fileManager', true);

            if (!$fileManager instanceof FileManager) {
                throw new InvalidConfigException('The fileManager component must be an instance of '.FileManager::class);
            }

            return $fileManager;
        }
        #endregion
    }
