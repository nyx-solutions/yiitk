<?php

    /**
     * This view is used by console/controllers/MigrateController.php.
     *
     * The following variables are available in this view:
     */

    /**
     * @var string $migrationSubview
     * @var string $tableName
     * @var string $className
     * @var string $namespace
     * @var string $migrationClass
     * @var string $migrationClassName
     */

    echo "<?php\n";

    if (!empty($namespace)) {
        echo "\n    namespace {$namespace};\n";
    }

?>

    use <?= $migrationClass; ?>;

    /**
     * Class <?= $className."\n"; ?>
     *
     * @noinspection PhpIllegalPsrClassPathInspection
     */
    class <?= $className; ?> extends <?= $migrationClassName; ?>

    {
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $this->tableName = '<?= $tableName; ?>';

            parent::init();
        }

<?= $this->render($migrationSubview, ['tableName' => $tableName, 'className' => $className, 'namespace' => $namespace]); ?>
    }
