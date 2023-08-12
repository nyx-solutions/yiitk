<?php

    namespace yiitk\db;

    use Throwable;
    use Yii;
    use yii\db\Connection;
    use yii\db\Exception as DbException;
    use yii\db\Transaction;

    /**
     * Trait: Transaction
     */
    trait TransactionTrait
    {
        /**
         * @param Connection|null $db
         *
         * @return Transaction
         *
         * @throws DbException
         */
        protected function transaction(?Connection $db = null): Transaction
        {
            if ($db === null) {
                $db = Yii::$app->db;

                /** @noinspection CallableParameterUseCaseInTypeContextInspection */
                if (!$db instanceof Connection) {
                    throw new DbException('The system could not locate an valid data base connection.');
                }
            }

            $transaction = $db->transaction;

            if ($transaction instanceof Transaction && $transaction->isActive) {
                return $transaction;
            }

            $db->beginTransaction();

            $transaction = $db->transaction;

            if ($transaction instanceof Transaction) {
                return $transaction;
            }

            throw new DbException('The system could not locate an valid data base connection.');
        }

        /**
         * @param Connection|null $db
         *
         * @return Transaction
         *
         * @throws DbException
         */
        protected function dbTransaction(?Connection $db = null): Transaction
        {
            return $this->transaction($db);
        }

        /**
         * @param callable      $runner
         * @param callable|null $onSuccess
         * @param callable|null $onError
         * @param bool          $useOnErrorTransaction
         *
         * @return bool
         *
         * @throws Throwable
         *
         * @noinspection BadExceptionsProcessingInspection
         */
        protected function runOnDbTransaction(callable $runner, ?callable $onSuccess = null, ?callable $onError = null, bool $useOnErrorTransaction = true): bool
        {
            if (!is_callable($onSuccess)) {
                $onSuccess = static fn (): bool => true;
            }

            if (!is_callable($onError)) {
                $onError = static fn (?Throwable $exception = null): mixed => null;
            }

            $transaction = $this->dbTransaction();

            $onErrorDbTransaction = function (?Throwable $exception = null) use ($onError) {
                $transaction = $this->dbTransaction();

                try {
                    $onError($exception);

                    $transaction->commit();

                    return true;
                } catch (Throwable $exception) {
                    $transaction->rollBack();

                    Yii::error(sprintf('%s / DB Transaction Error (ON_ERROR_EVENT): %s', static::class, $exception->getMessage()));

                    return false;
                }
            };

            try {
                if ($runner()) {
                    $transaction->commit();

                    $onSuccess();

                    return true;
                }

                $transaction->rollBack();

                if ($useOnErrorTransaction) {
                    $onErrorDbTransaction();

                    return false;
                }

                $onError();

                return false;
            } catch (Throwable $exception) {
                $transaction->rollBack();

                Yii::error(sprintf('%s / DB Transaction Error: %s', static::class, $exception->getMessage()));

                if ($useOnErrorTransaction) {
                    $onErrorDbTransaction($exception);

                    return false;
                }

                $onError($exception);

                return false;
            }
        }
    }
