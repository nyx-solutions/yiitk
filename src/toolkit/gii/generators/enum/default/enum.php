<?php

    /**
     * This is the template for generating a enum class file.
     */

    use yiitk\helpers\InflectorHelper;
    use yiitk\helpers\StringHelper;

    /**
     * @var yii\web\View                        $this
     * @var yiitk\gii\generators\enum\Generator $generator
     */

    echo "<?php\n";

?>

    namespace <?= $generator->findEnumNamespace(); ?>;
    <?php if ($generator->findEnumNamespace() !== $generator->findBaseNamespace()) : ?><?= "\n    "; ?>use <?= trim($generator->baseClass, '\\'); ?>;<?= "\n"; ?><?php endif; ?>

    /**
     * <?= StringHelper::basename($generator->enumClass); ?> Class
     *
     * In order to configure this Enum Type in your model you need to first
     * include to your migration the `\yiitk\db\SchemaBuilderTrait` trait or
     * create your migration file extending the `\yiitk\db\Migration` class
     * and use the `enum` method. Example:
     *
     * ```php
     * $this->enum(<?= "\\".$generator->enumClass; ?>::range())->defaultValue(<?= "\\".$generator->enumClass; ?>::<?= $generator->defaultConstant; ?>)->notNull()
     * ```
     * And in your model you can configure like this:
     *
     * ```php
     * public function rules()
     * {
     *     return [
     *         [['attribute'], 'required'],
     *         [['attribute'], 'in', 'range' => <?= "\\".$generator->enumClass; ?>::range()],
     *     ];
     * }
     *
     * public function enums()
     * {
     *     return [
     *         [['attribute'], 'enumClass' => <?= "\\".$generator->enumClass; ?>::class, 'default' => <?= "\\".$generator->enumClass; ?>::<?= lcfirst(InflectorHelper::camelize(strtolower($generator->defaultConstant))); ?>()],
     *     ];
     * }
     * ```
     *
<?php foreach ($generator->findConstants() as $constant) : ?>
     * @property mixed $<?= lcfirst(InflectorHelper::camelize(strtolower($constant['key']))); ?>

<?php endforeach; ?>
     *
<?php foreach ($generator->findConstants() as $constant) : ?>
     * @property bool $is<?= InflectorHelper::camelize(strtolower($constant['key'])); ?>

<?php endforeach; ?>
     *
<?php foreach ($generator->findConstants() as $constant) : ?>
     * @method static <?= lcfirst(InflectorHelper::camelize(strtolower($constant['key']))); ?>

<?php endforeach; ?>
     */
    class <?= StringHelper::basename($generator->enumClass); ?> extends <?= StringHelper::basename($generator->baseClass)."\n"; ?>
    {
<?php foreach ($generator->findConstants() as $constant) : ?><?= "        "; ?>const <?= $constant['key']; ?> = <?php if ($constant['numeric']) : ?><?= $constant['value']; ?><?php else : ?>'<?= $constant['value']; ?>'<?php endif; ?>;<?= "\n"; ?><?php endforeach; ?>

        /**
         * {@inheritdoc}
         */
        public static function defaultValue()
        {
            return self::<?= $generator->defaultConstant; ?>;
        }

        /**
         * {@inheritdoc}
         */
        protected static function labels()
        {
            return [
<?php foreach ($generator->findConstants() as $constant) : ?><?= "                "; ?>self::<?= $constant['key']; ?> => '<?= $constant['label']; ?>',<?= "\n"; ?><?php endforeach; ?>
            ];
        }
    }
