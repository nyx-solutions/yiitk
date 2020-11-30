<?php

    namespace yiitk\hashids;

    use yii\base\Behavior;
    use yii\di\Instance;
    use yiitk\db\ActiveRecord;

    /**
     * @property ActiveRecord $owner owner ActiveRecord instance.
     *
     * @noinspection PhpUnused
     */
    class HashidsBehavior extends Behavior
    {
        /**
         * @var string|array|Hashids The `hashids` component
         */
        public $hashids = 'hashids';

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            $this->hashids = Instance::ensure($this->hashids, Hashids::class);
        }

        /**
         * @return string
         */
        public function getHashid(): string
        {
            $primaryKey = $this->owner->getPrimaryKey();

            return (string)$this->hashids->encode($this->owner->{$primaryKey});
        }

    }
