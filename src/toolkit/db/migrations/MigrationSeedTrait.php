<?php

    namespace yiitk\db\migrations;

    use DirectoryIterator;
    use JsonException;
    use Yii;
    use yiitk\db\ActiveRecord;
    use yiitk\helpers\DateTimeHelper;

    /**
     * Trait: Migration Seed
     */
    trait MigrationSeedTrait
    {
        #region Seed
        /**
         * @var string
         */
        public string $seedPath = '@storage/data';

        /**
         * @var bool
         */
        protected bool $applySeedActions = true;

        /**
         * @return bool
         *
         * @throws JsonException
         */
        protected function applySeeds(): bool
        {
            if ($this->applySeedActions && !YII_ENV_TEST) {
                $seed = $this->seed($this->seedPath, DateTimeHelper::now('Y-m-d H:i:s'));

                if (is_array($seed) && isset($seed['model'], $seed['source'], $seed['loadData']) && is_callable($seed['loadData']) && is_subclass_of($seed['model'], ActiveRecord::class)) {
                    $seedPath = Yii::getAlias($seed['source']);

                    if (is_dir($seedPath)) {
                        $truncate = false;

                        if (isset($seed['truncate'])) {
                            $truncate = (bool)$seed['truncate'];
                        }

                        if ($truncate) {
                            $this->truncateTable($this->findCurrentTableName());
                        }

                        foreach ($this->loadSeedFromSource($seedPath) as $sourceItem) {
                            $data = $seed['loadData']($sourceItem);

                            if (is_array($data)) {
                                $this->insert($this->findCurrentTableName(), $data);
                            }
                        }

                        return true;
                    }
                }

                return false;
            }

            return true;
        }

        /**
         * @param string $path
         *
         * @return array
         *
         * @throws JsonException
         */
        protected function loadSeedFromSource(string $path): array
        {
            $items = [];

            foreach (new DirectoryIterator($path) as $file) {
                if ($file->isFile() && $file->getExtension() === 'json') {
                    $contents = file_get_contents($path.'/'.$file->getFilename(), LOCK_EX);
                    $contents = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

                    if ($contents && is_array($contents)) {
                        $items = array_values($contents);
                    }
                }
            }

            return $items;
        }

        /**
         * @param string $storage
         * @param string $now
         *
         * @return callable|null
         *
         * @noinspection PhpUnusedParameterInspection
         */
        protected function seed(string $storage, string $now): ?array
        {
            return null;
        }
        #endregion

        #region Helpers
        /**
         * @param array $nullables
         */
        protected function fixNullables(array $nullables): void
        {
            foreach ($nullables as $attribute => $nullableRules) {
                /**
                 * @var bool $string
                 * @var bool $int
                 */
                extract($nullableRules);

                if ($string) {
                    $this->update($this->findCurrentTableName(), [$attribute => null], [$attribute => '']);
                }

                if ($int) {
                    $this->update($this->findCurrentTableName(), [$attribute => null], [$attribute => '0']);
                }
            }
        }

        /**
         * @param array       $indexes
         * @param bool        $unique
         * @param string|null $tableName
         */
        protected function createIndexes(array $indexes, bool $unique = false, ?string $tableName = null): void
        {
            if ($tableName === null) {
                $tableName = $this->findCurrentTableName();
            }

            foreach ($indexes as $indexKey => $indexValue) {
                $name   = $indexValue;
                $column = $indexValue;

                if (!is_numeric($indexKey)) {
                    $name = $indexKey;
                }

                $this->createIndex(
                    $this->withTableName($name),
                    $tableName,
                    $column,
                    $unique
                );
            }
        }
        #endregion
    }
