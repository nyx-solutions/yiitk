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
        public const SCENARIO_DEFAULT = 'default';
        public const SCENARIO_INSERT  = 'insert';
        public const SCENARIO_UPDATE  = 'update';

        /**
         * @var string
         */
        public static string $id = 'file';

        /**
         * @var bool
         */
        public static bool $image = false;

        /**
         * @var string|null
         */
        public static ?string $modelClass = null;

        /**
         * @var string
         */
        public static string $attribute = 'file';

        /**
         * @var string
         */
        public static string $tmpPath = '@webroot/tmp';

        /**
         * @var string
         */
        public static string $path = '@webroot/upload';

        /**
         * @var string
         */
        public static string $url = '@web/upload';

        /**
         * @var bool
         */
        public static bool $keepFileName = false;

        /**
         * @var bool
         */
        public static bool $requiredOnInsert = true;

        /**
         * @var string
         */
        public static string $filesContentTypes = 'application/pdf, application/x-pdf, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/rtf, text/richtext, application/vnd.oasis.opendocument.text';

        /**
         * @var string
         */
        public static string $filesExtensions = 'doc, docx, rtf, odt, pdf';

        /**
         * @var string
         */
        public static string $imagesContentTypes = 'image/gif, image/jpeg, image/png';

        /**
         * @var string
         */
        public static string $imagesExtensions = 'jpeg, jpg, gif, png';

        /**
         * @var int|null
         */
        public static ?int $imageMinWidth = null;

        /**
         * @var int|null
         */
        public static ?int $imageMaxWidth = null;

        /**
         * @var int|null
         */
        public static ?int $imageMinHeight = null;

        /**
         * @var int|null
         */
        public static ?int $imageMaxHeight = null;

        /**
         * @var array
         */
        public static array $variations = [];

        /**
         * @return string|null
         */
        public static function tableName(): ?string
        {
            $modelClass = static::$modelClass;

            if (!empty($modelClass)) {
                return call_user_func([$modelClass, 'tableName']);
            }

            return null;
        }

        /**
         * @return string
         *
         * @throws InvalidConfigException
         */
        public static function tableColumnName(): string
        {
            $fileManager = static::fileManager();

            return static::$attribute.$fileManager->fkFieldSuffix;
        }

        /**
         * @return array
         *
         * @throws InvalidConfigException
         */
        public static function rules(): array
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
         *
         * @throws InvalidConfigException
         */
        public static function behavior(string $name, bool $instanceByName = false): array
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
                'generateNewName' => static function ($file) use ($keepFileName) {
                    /** @var UploadedFile $file */
                    $extension = strtolower($file->extension);

                    if ($keepFileName) {
                        return Inflector::slug($file->baseName, '-', true).".{$extension}";
                    }

                    return sha1(uniqid(mt_rand().date('YmdHis'), true)).".{$extension}";
                },
                'afterUploadCallback' => static function ($model, $file) {

                },
                'unlinkOnSave'    => true,
                'unlinkOnDelete'  => true,
                'deleteTempFile'  => true,
                'instanceByName'  => $instanceByName,
            ];
        }

        /**
         * @return FileManager
         *
         * @throws InvalidConfigException
         */
        protected static function fileManager(): FileManager
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
