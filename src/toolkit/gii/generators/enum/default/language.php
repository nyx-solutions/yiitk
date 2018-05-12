<?php

    /**
     * This is the template for generating an language file.
     */

    /**
     * @var yii\web\View                        $this
     * @var yiitk\gii\generators\enum\Generator $generator
     * @var string                              $language the language ID
     */

    echo "<?php\n";
?>

    /**
     * Internationalization file for '<?= $language; ?>'. Related Enum Class: <?= "\\".$generator->enumClass."\n"; ?>
     */

    return [
<?php foreach ($generator->findConstants() as $constant) : ?><?= "        "; ?>'<?= $constant['label']; ?>' => '<?= $constant['label']; ?>',<?= "\n"; ?><?php endforeach; ?>
    ];
