<?php

    namespace yiitk\helpers;

    use yii\base\BaseObject;
    use yii\base\InvalidConfigException;
    use yii\helpers\Inflector;
    use yii\web\UploadedFile;
    use yiitk\behaviors\FileUploadBehavior;
    use yiitk\file\FileManager;
    use yiitk\models\File;
    use yiitk\Module;

    /**
     * Class FileManagerHelper
     */
    class FileManagerHelper extends BaseObject
    {
        const SCENARIO_DEFAULT = 'default';
        const SCENARIO_INSERT  = 'insert';
        const SCENARIO_UPDATE  = 'update';

        /**
         * @var string
         */
        public static $id = 'file';

        /**
         * @var bool
         */
        public static $image = false;

        /**
         * @var string
         */
        public static $modelClass = null;

        /**
         * @var string
         */
        public static $attribute = 'file';

        /**
         * @var string
         */
        public static $tmpPath = '@webroot/tmp';

        /**
         * @var string
         */
        public static $path = '@webroot/upload';

        /**
         * @var string
         */
        public static $url = '@web/upload';

        /**
         * @var bool
         */
        public static $keepFileName = false;

        /**
         * @var bool
         */
        public static $requiredOnInsert = true;

        /**
         * @var string
         */
        public static $filesContentTypes = 'application/pdf, application/x-pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/rtf, text/richtext, application/vnd.oasis.opendocument.text';

        /**
         * @var string
         */
        public static $filesExtensions = 'doc, docx, rtf, odt, pdf';

        /**
         * @var string
         */
        public static $imagesContentTypes = 'image/gif, image/jpeg, image/png';

        /**
         * @var string
         */
        public static $imagesExtensions = 'jpeg, jpg, gif, png';

        /**
         * @var integer
         */
        public static $imageMinWidth = null;

        /**
         * @var integer
         */
        public static $imageMaxWidth = null;

        /**
         * @var integer
         */
        public static $imageMinHeight = null;

        /**
         * @var integer
         */
        public static $imageMaxHeight = null;

        /**
         * @var array
         */
        public static $variations = [];

        /**
         * @return string
         */
        public static function tableName()
        {
            $modelClass = static::$modelClass;

            if (!empty($modelClass)) {
                return call_user_func([$modelClass, 'tableName']);
            }

            return null;
        }

        /**
         * @return string
         */
        public static function tableColumnName()
        {
            $fileManager = static::fileManager();

            return static::$attribute.$fileManager->fkFieldSuffix;
        }

        /**
         * @return array
         */
        public static function rules()
        {
            $fileManager = static::fileManager();

            $rules = [];

            if (static::$requiredOnInsert) {
                $rules[] = [static::$attribute, 'required', 'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_INSERT]];
            }

            if (static::$image) {
                $rules[] = [
                    static::$attribute,
                    'image',
                    'extensions'               => static::$imagesExtensions,
                    'checkExtensionByMimeType' => false,
                    'mimeTypes'                => static::$imagesContentTypes,
                    'minWidth'                 => (((int)static::$imageMinWidth > 0) ? (int)static::$imageMinWidth : null),
                    'maxWidth'                 => (((int)static::$imageMaxWidth > 0) ? (int)static::$imageMaxWidth : null),
                    'minHeight'                => (((int)static::$imageMinHeight > 0) ? (int)static::$imageMinHeight : null),
                    'maxHeight'                => (((int)static::$imageMaxHeight > 0) ? (int)static::$imageMaxHeight : null),
                    'skipOnEmpty'              => (((bool)static::$requiredOnInsert) ? false : true),
                    'enableClientValidation'   => true,
                    'on'                       => [self::SCENARIO_DEFAULT, self::SCENARIO_INSERT]
                ];

                $rules[] = [
                    static::$attribute,
                    'image',
                    'extensions'               => static::$imagesExtensions,
                    'checkExtensionByMimeType' => false,
                    'mimeTypes'                => static::$imagesContentTypes,
                    'minWidth'                 => (((int)static::$imageMinWidth > 0) ? (int)static::$imageMinWidth : null),
                    'maxWidth'                 => (((int)static::$imageMaxWidth > 0) ? (int)static::$imageMaxWidth : null),
                    'minHeight'                => (((int)static::$imageMinHeight > 0) ? (int)static::$imageMinHeight : null),
                    'maxHeight'                => (((int)static::$imageMaxHeight > 0) ? (int)static::$imageMaxHeight : null),
                    'enableClientValidation'   => true,
                    'skipOnEmpty'              => true,
                    'on'                       => [self::SCENARIO_UPDATE]
                ];
            } else {
                $rules[] = [
                    static::$attribute,
                    'image',
                    'extensions'               => static::$filesExtensions,
                    'checkExtensionByMimeType' => false,
                    'mimeTypes'                => static::$filesContentTypes,
                    'skipOnEmpty'              => (((bool)static::$requiredOnInsert) ? false : true),
                    'enableClientValidation'   => true,
                    'on'                       => [self::SCENARIO_DEFAULT, self::SCENARIO_INSERT]
                ];

                $rules[] = [
                    static::$attribute,
                    'image',
                    'extensions'               => static::$filesExtensions,
                    'checkExtensionByMimeType' => false,
                    'mimeTypes'                => static::$filesContentTypes,
                    'enableClientValidation'   => true,
                    'skipOnEmpty'              => true,
                    'on'                       => [self::SCENARIO_UPDATE]
                ];
            }

            $idAttribute = static::$attribute.$fileManager->fkFieldSuffix;

            $rules[] = [$idAttribute, 'integer', 'skipOnEmpty' => true];
            $rules[] = [$idAttribute, 'exist', 'skipOnEmpty' => true, 'targetClass' => File::class, 'targetAttribute' => [$idAttribute => 'id']];

            return $rules;
        }

        /**
         * @param string $name
         * @param bool   $instanceByName
         *
         * @return array
         */
        public static function behavior($name, $instanceByName = false)
        {
            $fileManager  = static::fileManager();
            $keepFileName = static::$keepFileName;
            $attribute    = static::$attribute;

            return [
                'class'           => FileUploadBehavior::class,
                'name'            => $name,
                'fileClass'       => static::class,
                'attribute'       => $attribute,
                'idAttribute'     => "{$attribute}{$fileManager->fkFieldSuffix}",
                'scenarios'       => [static::SCENARIO_INSERT, static::SCENARIO_UPDATE],
                'path'            => static::$path,
                'tmpPath'         => static::$tmpPath,
                'url'             => static::$url,
                'variations'      => static::$variations,
                'generateNewName' => function ($file) use ($keepFileName) {
                    /** @var UploadedFile $file */
                    $extension = strtolower($file->extension);

                    if ($keepFileName) {
                        return Inflector::slug($file->baseName, '-', true).".{$extension}";
                    } else {
                        return sha1(uniqid(rand().date('YmdHis'), true)).".{$extension}";
                    }
                },
                'afterUploadCallback' => function ($model, $file) {

                },
                'unlinkOnSave'    => true,
                'unlinkOnDelete'  => true,
                'deleteTempFile'  => true,
                'instanceByName'  => (bool)$instanceByName,
            ];
        }

        /**
         * @return FileManager
         *
         * @throws InvalidConfigException
         */
        protected static function fileManager()
        {
            /** @var Module $yiitk */
            $yiitk = Module::getInstance();

            /** @var FileManager $fileManager */
            $fileManager = $yiitk->get('fileManager', true);

            if (!$fileManager instanceof FileManager) {
                throw new InvalidConfigException('The fileManager component must be an instance of '.FileManager::class);
            }

            return $fileManager;
        }
    }
