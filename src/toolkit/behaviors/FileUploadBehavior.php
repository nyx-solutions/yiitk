<?php

    namespace yiitk\behaviors;

    use Closure;
    use yiitk\enum\BooleanEnum;
    use yiitk\helpers\FileManagerHelper;
    use yiitk\models\File;
    use Yii;
    use yii\base\Behavior;
    use yii\base\Exception;
    use yii\base\InvalidConfigException;
    use yii\base\InvalidArgumentException;
    use yii\db\BaseActiveRecord;
    use yii\helpers\ArrayHelper;
    use yii\helpers\FileHelper;
    use yii\helpers\Inflector;
    use yii\web\UploadedFile;

    /**
     * FileUploadBehavior automatically uploads file and fills the specified attribute
     * with a value of the name of the uploaded file.
     *
     * To use FileUploadBehavior, insert the following code to your ActiveRecord class:
     *
     * ```php
     * use yiitk\behaviors\FileUploadBehavior;
     * use yiitk\helpers\FileManagerHelper;
     *
     * function behaviors()
     * {
     *     return [
     *         [
     *             'class'     => FileUploadBehavior::class,
     *             'fileClass' => FileManagerHelper::class,
     *             'attribute' => 'file',
     *             'scenarios' => ['insert', 'update'],
     *             'path'      => '@webroot/upload/{id}',
     *             'url'       => '@web/upload/{id}',
     *         ]
     *     ];
     * }
     * ```
     *
     * @property string $uploadPath
     * @property string $uploadUrl
     * @property string $uploadedFile
     * @property string $fileName
     */
    class FileUploadBehavior extends Behavior
    {
        /**
         * @event Event an event that is triggered after a file is uploaded.
         */
        const EVENT_AFTER_UPLOAD = 'afterUpload';

        /**
         * @var string
         */
        public $fileClass = FileManagerHelper::class;

        /**
         * @var string the attribute which holds the attachment.
         */
        public $attribute;

        /**
         * @var string the attribute which holds the relation with the attachment.
         */
        public $idAttribute;

        /**
         * @var string
         */
        public $name;

        /**
         * @var array the scenarios in which the behavior will be triggered
         */
        public $scenarios = [];

        /**
         * @var string the base path or path alias to the directory in which to save files.
         */
        public $path;

        /**
         * @var string the base path or path alias to the temp directory in which to save files.
         */
        public $tmpPath;

        /**
         * @var string the base URL or path alias for this file
         */
        public $url;

        /**
         * @var bool Getting file instance by name
         */
        public $instanceByName = false;

        /**
         * @var boolean|callable generate a new unique name for the file
         * set true or anonymous function takes the old filename and returns a new name.
         *
         * @see self::generateFileName()
         */
        public $generateNewName = true;

        /**
         * @var boolean If `true` current attribute file will be deleted
         */
        public $unlinkOnSave = true;

        /**
         * @var boolean If `true` current attribute file will be deleted after model deletion.
         */
        public $unlinkOnDelete = true;

        /**
         * @var boolean $deleteTempFile whether to delete the temporary file after saving.
         */
        public $deleteTempFile = true;

        /**
         * @var Closure
         */
        public $afterUploadCallback;

        /**
         * @var array
         */
        public $variations = [];

        /**
         * @var File
         */
        protected $fileModel;

        /**
         * @var string
         */
        protected $originalFileName;

        /**
         * @var UploadedFile the uploaded file instance.
         */
        private $_file;

        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if (is_null($this->attribute)) {
                throw new InvalidConfigException('The "attribute" property must be set.');
            }

            if (is_null($this->tmpPath)) {
                throw new InvalidConfigException('The "tmpPath" property must be set.');
            }

            if (is_null($this->path)) {
                throw new InvalidConfigException('The "path" property must be set.');
            }

            if (is_null($this->url)) {
                throw new InvalidConfigException('The "url" property must be set.');
            }
        }
        #endregion

        #region Model Events
        /**
         * @inheritdoc
         */
        public function events()
        {
            return [
                BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
                BaseActiveRecord::EVENT_BEFORE_INSERT   => 'beforeSave',
                BaseActiveRecord::EVENT_BEFORE_UPDATE   => 'beforeSave',
                BaseActiveRecord::EVENT_AFTER_INSERT    => 'afterSave',
                BaseActiveRecord::EVENT_AFTER_UPDATE    => 'afterSave',
                BaseActiveRecord::EVENT_AFTER_DELETE    => 'afterDelete',
            ];
        }

        /**
         * This method is invoked before validation starts.
         */
        public function beforeValidate()
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            if (in_array($model->scenario, $this->scenarios)) {
                if (($file = $model->getAttribute($this->attribute)) instanceof UploadedFile) {
                    $this->_file = $file;
                } else {
                    if ($this->instanceByName === true) {
                        $this->_file = UploadedFile::getInstanceByName($this->attribute);
                    } else {
                        $this->_file = UploadedFile::getInstance($model, $this->attribute);
                    }
                }

                if ($this->_file instanceof UploadedFile) {
                    $this->_file->name = $this->getFileName($this->_file);

                    $model->setAttribute($this->attribute, $this->_file);
                }
            }
        }

        /**
         * This method is called at the beginning of inserting or updating a record.
         */
        public function beforeSave()
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            if (in_array($model->scenario, $this->scenarios)) {
                if ($this->_file instanceof UploadedFile) {
                    if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                        if ($this->unlinkOnSave === true) {
                            $this->delete($this->attribute, true);
                        }
                    }

                    $model->setAttribute($this->attribute, $this->_file->name);
                } else {
                    // Protect attribute
                    unset($model->{$this->attribute});
                }
            } else {
                if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                    if ($this->unlinkOnSave === true) {
                        $this->delete($this->attribute, true);
                    }
                }
            }
        }

        /**
         * This method is called at the end of inserting or updating a record.
         *
         * @throws InvalidArgumentException
         */
        public function afterSave()
        {
            if ($this->_file instanceof UploadedFile) {
                $path = $this->getUploadPath($this->attribute);

                if (is_string($path) && FileHelper::createDirectory(dirname($path))) {
                    $this->save($this->_file, $path);

                    $this->loadFileModel();

                    if ($this->afterUploadCallback instanceof Closure) {
                        /** @var BaseActiveRecord $model */
                        $model = $this->owner;

                        call_user_func($this->afterUploadCallback, $model, $this->fileModel);
                    }

                    $this->afterUpload();
                } else {
                    throw new InvalidArgumentException("Directory specified in 'path' attribute doesn't exist or cannot be created.");
                }
            }
        }

        /**
         * This method is invoked after deleting a record.
         */
        public function afterDelete()
        {
            $attribute = $this->attribute;

            if ($this->unlinkOnDelete && $attribute) {
                $this->delete($attribute);
            }
        }
        #endregion

        #region Internal Events
        /**
         * This method is invoked after uploading a file.
         * The default implementation raises the [[EVENT_AFTER_UPLOAD]] event.
         * You may override this method to do postprocessing after the file is uploaded.
         * Make sure you call the parent implementation so that the event is raised properly.
         */
        protected function afterUpload()
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            $model->trigger(self::EVENT_AFTER_UPLOAD);
        }
        #endregion

        #region Getters
        /**
         * Returns file path for the attribute.
         *
         * @param string  $attribute
         * @param boolean $old
         *
         * @return string|null the file path.
         */
        public function getUploadPath($attribute, $old = false)
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            $path = $this->resolvePath($this->tmpPath);

            $fileName = (($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute);

            return (($fileName) ? Yii::getAlias("{$path}/{$fileName}") : null);
        }

        /**
         * Returns file url for the attribute.
         *
         * @param string $attribute
         *
         * @return string|null
         */
        public function getUploadUrl($attribute)
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            $url = $this->resolvePath($this->url);

            $fileName = $model->getOldAttribute($attribute);

            return (($fileName) ? Yii::getAlias("{$url}/{$fileName}") : null);
        }

        /**
         * Returns the UploadedFile instance.
         *
         * @return UploadedFile
         */
        public function getUploadedFile()
        {
            return $this->_file;
        }

        /**
         * @param UploadedFile $file
         *
         * @return string
         */
        protected function getFileName($file)
        {
            if ($this->generateNewName) {
                return (($this->generateNewName instanceof Closure) ? call_user_func($this->generateNewName, $file) : $this->generateFileName($file));
            } else {
                return $this->sanitize($file->name);
            }
        }
        #endregion

        #region Save / Delete
        /**
         * Saves the uploaded file.
         *
         * @param UploadedFile $file the uploaded file instance
         * @param string       $path the file path used to save the uploaded file
         *
         * @return boolean true whether the file is saved successfully
         */
        protected function save($file, $path)
        {
            return $file->saveAs($path, (bool)$this->deleteTempFile);
        }

        /**
         * Deletes old file.
         *
         * @param string $attribute
         * @param boolean $old
         */
        protected function delete($attribute, $old = false)
        {
            $path = $this->getUploadPath($attribute, $old);

            if (is_file($path)) {
                unlink($path);
            }
        }
        #endregion

        #region Strategic Helpers
        /**
         * Replaces all placeholders in path variable with corresponding values.
         *
         * @param string $path
         *
         * @return mixed
         */
        protected function resolvePath($path)
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            return preg_replace_callback(
                '/{([^}]+)}/',
                function ($matches) use ($model) {
                    $name = $matches[1];

                    $attribute = ArrayHelper::getValue($model, $name);

                    if (is_string($attribute) || is_numeric($attribute)) {
                        $attribute = Inflector::slug($attribute, '-');

                        return $attribute;
                    } else {
                        $attribute = Inflector::slug($matches[0], '-');

                        return $attribute;
                    }
                },
                $path
            );
        }
        /**
         * @param string $name
         *
         * @return mixed
         */
        protected function parseName($name)
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            return preg_replace_callback(
                '/{([^}]+)}/',
                function ($matches) use ($model) {
                    $name = $matches[1];

                    $attribute = ArrayHelper::getValue($model, $name);

                    if (is_string($attribute) || is_numeric($attribute)) {
                        return $attribute;
                    }

                    return '';
                },
                $name
            );
        }

        /**
         * Replaces characters in strings that are illegal/unsafe for filename.
         *
         * #my*  unsaf<e>&file:name?".png
         *
         * @param string $filename the source filename to be "sanitized"
         *
         * @return boolean string the sanitized filename
         */
        public static function sanitize($filename)
        {
            return Inflector::slug($filename, '-');
        }

        /**
         * Generates random filename.
         *
         * @param UploadedFile $file
         *
         * @return string
         */
        protected function generateFileName($file)
        {
            $uid = sha1(uniqid(rand().date('YmdHis'), true));
            $ext = strtolower($file->extension);

            return "{$uid}.{$ext}";
        }
        #endregion

        #region Internal File
        /**
         * @throws Exception
         */
        protected function loadFileModel()
        {
            /** @var BaseActiveRecord $model */
            $model = $this->owner;

            $idAttribute = $this->idAttribute;

            $name         = $this->parseName($this->name);
            $path         = $this->resolvePath($this->path);
            $url          = $this->resolvePath($this->url);
            $ext          = strtolower($this->_file->extension);
            $baseFileName = $this->_file->baseName;
            $fileName     = "{$baseFileName}.{$ext}";
            $tmpFile      = $this->resolvePath($this->tmpPath)."/{$fileName}";

            if ($model->hasAttribute($idAttribute) && (int)$model->$idAttribute > 0) {
                $id = $model->$idAttribute;

                $file = File::findOne($id);

                if ($file && $file instanceof File) {
                    $file->name            = $this->parseName($this->name);
                    $file->tmpData         = $this->generateFileData($name, $tmpFile, $path, $url, $baseFileName, $ext, $this->variations);
                    $file->tmpPath         = "{$path}/{$fileName}";
                    $file->tableName       = call_user_func([$this->fileClass, 'tableName']);
                    $file->tableColumnName = call_user_func([$this->fileClass, 'tableColumnName']);
                    $file->tableColumnId   = $model->getAttribute('id');
                    $file->deleteOriginal  = BooleanEnum::YES;
                    $file->deletable       = BooleanEnum::NO;

                    if (!$file->save()) {
                        throw new Exception('The system could not complete the upload process.');
                    }

                    $this->fileModel = $file;
                }
            }

            $file = new File(['scenario' => File::SCENARIO_INSERT]);

            $file->name            = $this->parseName($this->name);
            $file->basePath        = $this->resolvePath($this->path);
            $file->baseUrl         = $this->resolvePath($this->url);
            $file->originalName    = $this->_file->baseName;
            $file->extension       = $ext;
            $file->data            = $this->generateFileData($name, $tmpFile, $path, $url, $baseFileName, $ext, $this->variations);
            $file->tmpData         = $this->generateFileData($name, $tmpFile, $path, $url, $baseFileName, $ext, $this->variations);
            $file->tmpPath         = "{$path}/{$fileName}";
            $file->tableName       = call_user_func([$this->fileClass, 'tableName']);
            $file->tableColumnName = call_user_func([$this->fileClass, 'tableColumnName']);
            $file->tableColumnId   = $model->getAttribute('id');
            $file->deleteOriginal  = BooleanEnum::NO;
            $file->deletable       = BooleanEnum::NO;

            if (!$file->save()) {
                throw new Exception('The system could not complete the upload process.');
            }

            $this->fileModel = $file;
        }

        /**
         * @param string $name
         * @param string $tmpFile
         * @param string $path
         * @param string $url
         * @param string $file
         * @param string $ext
         * @param array  $variations
         *
         * @return array
         */
        protected function generateFileData($name, $tmpFile, $path, $url, $file, $ext, $variations = [])
        {
            $data = [
                'name'       => $name,
                'path'       => "{$path}/{$file}.{$ext}",
                'url'        => "{$url}/{$file}.{$ext}",
                'variations' => []
            ];

            list($width, $height) = getimagesize(Yii::getAlias($tmpFile));

            $width  = (int)$width;
            $height = (int)$height;

            $data['variations']['original'] = ['name' => $name, 'path' => "{$path}/{$file}.{$ext}", 'url' => "{$url}/{$file}.{$ext}", 'width' => $width, 'height' => $height, 'quality' => 90];

            foreach ($variations as $variationId => $variation) {
                if (isset($variation['name'], $variation['width'], $variation['height'], $variation['quality'])) {
                    if ($variationId == 'original') {
                        continue;
                    }

                    $data['variations'][$variationId] = [
                        'name'    => $this->parseName($variation['name']),
                        'path'    => "{$path}/{$file}-{$variation['width']}x{$variation['quality']}.{$ext}",
                        'url'     => "{$path}/{$file}-{$variation['width']}x{$variation['quality']}.{$ext}",
                        'width'   => (int)$variation['width'],
                        'height'  => (int)$variation['height'],
                        'quality' => (int)$variation['quality']
                    ];
                }
            }

            return $data;
        }
        #endregion
    }
