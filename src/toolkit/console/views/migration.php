<?php

    /**
     * This view is used by console/controllers/MigrateController.php.
     *
     * The following variables are available in this view:
     */

    /**
     * @var string $migrationSubview
     * @var string $tableName
     * @var string $tableSimpleName
     * @var string $className
     * @var string $namespace
     */

    echo "<?php\n";

    if (!empty($namespace)) {
        echo "\n    namespace {$namespace};\n";
    }

?>

    use common\components\db\Migration;

    /**
     * Class <?= $className."\n"; ?>
     */
    class <?= $className; ?> extends Migration
    {
        /**
         * @inheritdoc
         */
        protected $tableName = '<?= $tableName; ?>';
<?= $this->render($migrationSubview, ['tableName' => $tableName, 'tableSimpleName' => $tableSimpleName, 'className' => $className, 'namespace' => $namespace]); ?>
    }
