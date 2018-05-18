<?php

    namespace yiitk\models;

    use yiitk\db\ActiveRecord;
    use yiitk\enum\BooleanEnum;
    use yiitk\file\FileManager;
    use yiitk\Module;

    /**
     * File Model
     *
     * @property integer $id
     * @property string  $name
     * @property string  $basePath
     * @property string  $baseUrl
     * @property string  $originalName
     * @property string  $extension
     * @property string  $data
     * @property string  $tmpData
     * @property string  $tmpPath
     * @property string  $tableName
     * @property string  $tableColumnName
     * @property integer $tableColumnId
     * @property string  $deleteOriginal
     * @property string  $deletable
     * @property string  $createdAt
     * @property string  $updatedAt
     */
    class File extends ActiveRecord
    {
        #region Table Name
        /**
         * {@inheritdoc}
         */
        public static function tableName()
        {
            $fileManager = static::findFileManager();

            return $fileManager->fileTable;
        }
        #endregion

        #region Rulesets
        /**
         * {@inheritdoc}
         */
        public function rules()
        {
            return [
                [['name', 'deleteOriginal', 'deletable'], 'required'],
                [['extension'], 'string', 'max' => 4],
                [['tableName', 'tableColumnName'], 'string', 'max' => 100],
                [['name', 'originalName'], 'string', 'max' => 255],
                [['name', 'basePath', 'baseUrl', 'tmpPath'], 'string', 'max' => 2000],
                [['tableColumnId'], 'integer'],
                [['deleteOriginal', 'deletable'], 'in' => BooleanEnum::range()],
                [['data', 'tmpData'], 'safe']
            ];
        }
        #endregion

        #region Enums
        /**
         * {@inheritdoc}
         */
        public function enums()
        {
            return [
                [['deleteOriginal', 'deletable'], 'enumClass' => BooleanEnum::class, 'default' => BooleanEnum::no()]
            ];
        }
        #endregion

        #region Attribute Labels
        /**
         * {@inheritdoc}
         */
        public function attributeLabels()
        {
            return [
                'id'              => \Yii::t('yiitk', 'ID'),
                'name'            => \Yii::t('yiitk', 'Name'),
                'basePath'        => \Yii::t('yiitk', 'Base Path'),
                'baseUrl'         => \Yii::t('yiitk', 'Base URL'),
                'originalName'    => \Yii::t('yiitk', 'Original Name'),
                'extension'       => \Yii::t('yiitk', 'Extension'),
                'data'            => \Yii::t('yiitk', 'Data'),
                'tmpData'         => \Yii::t('yiitk', 'Temporary Data'),
                'tmpPath'         => \Yii::t('yiitk', 'Temporary Path'),
                'tableName'       => \Yii::t('yiitk', 'Table Name'),
                'tableColumnName' => \Yii::t('yiitk', 'Table Column Name'),
                'tableColumnId'   => \Yii::t('yiitk', 'Table Column ID'),
                'deleteOriginal'  => \Yii::t('yiitk', 'Delete Original?'),
                'deletable'       => \Yii::t('yiitk', 'Deletable?'),
                'createdAt'       => \Yii::t('yiitk', 'Created At'),
                'updatedAt'       => \Yii::t('yiitk', 'Updated At'),
            ];
        }
        #endregion

        #region File Manager

        /**
         * @return FileManager
         *
         * @throws \yii\base\InvalidConfigException
         */
        protected static function findFileManager()
        {
            $yiitk = Module::getInstance();

            /** @var FileManager $fileManager */
            $fileManager = $yiitk->get('fileManager');

            return $fileManager;
        }
        #endregion
    }
