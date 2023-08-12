<?php

    namespace yiitk\db;

    use Throwable;
    use Yii;
    use yii\db\ActiveQuery as BaseActiveQuery;
    use yii\db\Command;
    use yii\db\Connection;
    use yii\db\Transaction;

    /**
     * Trait: Lock
     */
    trait LockTrait
    {
        /**
         * @return string
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        abstract public static function tableName();

        /**
         * @param string|array|null $tablesNames
         * @param Connection|null   $connection
         *
         * @return bool
         */
        protected static function lockTables(string|array|null $tablesNames = null, ?Connection $connection = null): bool
        {
            if ($tablesNames === null) {
                $tablesNames = static::tableName();
            }

            if (!is_array($tablesNames)) {
                $tablesNames = [$tablesNames];
            }

            if ($connection === null) {
                $connection = Yii::$app->getDb();
            }

            $transaction = $connection->getTransaction();

            if ($transaction instanceof Transaction && $transaction->isActive) {
                return false;
            }

            try {
                $connection->createCommand(sprintf(
                    'LOCK TABLE%s %s WRITE',
                    ((count($tablesNames) > 1) ? 'S' : ''),
                    implode(', ', $tablesNames)
                ))->execute();
            } catch (Throwable) {
                return false;
            }

            return true;
        }

        /**
         * @param Connection|null   $connection
         *
         * @return bool
         */
        protected static function unlockTables(?Connection $connection = null): bool
        {
            if ($connection === null) {
                $connection = Yii::$app->getDb();
            }

            $transaction = $connection->getTransaction();

            if ($transaction instanceof Transaction && $transaction->isActive) {
                return false;
            }

            try {
                $connection->createCommand('UNLOCK TABLES')->execute();
            } catch (Throwable) {
                return false;
            }

            return true;
        }

        /**
         * @param BaseActiveQuery $query
         *
         * @return Command
         */
        protected static function forUpdate(BaseActiveQuery $query): Command
        {
            $sql = $query
                ->createCommand()
                ->getRawSql();

            $sql .= ' FOR UPDATE';

            /** @var Connection $db */
            $db = static::getDb();

            return $db->createCommand()->setRawSql($sql);
        }
    }
