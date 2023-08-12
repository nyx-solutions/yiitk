<?php

    namespace yiitk\models;

    use Yii;
    use yii\base\InvalidConfigException;
    use yiitk\db\ActiveRecord;
    use yiitk\enum\BooleanEnum;
    use yiitk\file\FileManager;
    use yiitk\Module as YiiTkModule;

    /**
     * File Model
     *
     * @property int    $id
     * @property string $name
     * @property string $basePath
     * @property string $baseUrl
     * @property string $originalName
     * @property string $extension
     * @property string $data
     * @property string $tmpData
     * @property string $tmpPath
     * @property string $tableName
     * @property string $tableColumnName
     * @property int    $tableColumnId
     * @property string $deleteOriginal
     * @property string $deletable
     * @property string $createdAt
     * @property string $updatedAt
     */
    class File extends ActiveRecord
    {
        #region Table Name
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public static function tableName()
        {
            return static::findFileManager()->fileTable;
        }
        #endregion

        #region Rulesets
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
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
        public function enums(): array
        {
            return [
                [['deleteOriginal', 'deletable'], 'enumClass' => BooleanEnum::class, 'default' => BooleanEnum::no()]
            ];
        }
        #endregion

        #region Attribute Labels
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function attributeLabels()
        {
            return [
                'id'              => Yii::t('yiitk', 'ID'),
                'name'            => Yii::t('yiitk', 'Name'),
                'basePath'        => Yii::t('yiitk', 'Base Path'),
                'baseUrl'         => Yii::t('yiitk', 'Base URL'),
                'originalName'    => Yii::t('yiitk', 'Original Name'),
                'extension'       => Yii::t('yiitk', 'Extension'),
                'data'            => Yii::t('yiitk', 'Data'),
                'tmpData'         => Yii::t('yiitk', 'Temporary Data'),
                'tmpPath'         => Yii::t('yiitk', 'Temporary Path'),
                'tableName'       => Yii::t('yiitk', 'Table Name'),
                'tableColumnName' => Yii::t('yiitk', 'Table Column Name'),
                'tableColumnId'   => Yii::t('yiitk', 'Table Column ID'),
                'deleteOriginal'  => Yii::t('yiitk', 'Delete Original?'),
                'deletable'       => Yii::t('yiitk', 'Deletable?'),
                'createdAt'       => Yii::t('yiitk', 'Created At'),
                'updatedAt'       => Yii::t('yiitk', 'Updated At'),
            ];
        }
        #endregion

        #region File Manager

        /**
         * @return FileManager|null
         *
         * @throws InvalidConfigException
         */
        protected static function findFileManager(): ?FileManager
        {
            $yiitk = YiiTkModule::getInstance();

            if ($yiitk instanceof YiiTkModule) {
                /** @var FileManager $fileManager */

                $fileManager = $yiitk->get('fileManager');

                return $fileManager;
            }

            return null;
        }
        #endregion
    }
