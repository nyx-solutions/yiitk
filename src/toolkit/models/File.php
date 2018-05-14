<?php

    namespace yiitk\models;

    use yiitk\db\ActiveRecord;

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
        /**
         * {@inheritdoc}
         */
        public static function tableName()
        {
            return '{{%file}}';
        }
    }
