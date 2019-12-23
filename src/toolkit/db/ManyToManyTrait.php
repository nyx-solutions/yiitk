<?php

    namespace yiitk\db;

    use yii\base\UnknownPropertyException;

    /**
     * Trait ManyToManyTrait
     *
     * @package yiitk\db
     */
    trait ManyToManyTrait
    {
        /**
         * Returns a value indicating whether the model has an attribute with the specified name.
         * @param string $name the name of the attribute
         * @return bool whether the model has an attribute with the specified name.
         */
        abstract public function hasAttribute($name);

        /**
         * Returns a value indicating whether a property is defined for this component.
         *
         * A property is defined if:
         *
         * - the class has a getter or setter method associated with the specified name
         *   (in this case, property name is case-insensitive);
         * - the class has a member variable with the specified name (when `$checkVars` is true);
         * - an attached behavior has a property of the given name (when `$checkBehaviors` is true).
         *
         * @param string $name the property name
         * @param bool $checkVars whether to treat member variables as properties
         * @param bool $checkBehaviors whether to treat behaviors' properties as properties of this component
         * @return bool whether the property is defined
         * @see canGetProperty()
         * @see canSetProperty()
         */
        abstract public function hasProperty($name, $checkVars = true, $checkBehaviors = true);

        /**
         * Saves the current record.
         *
         * This method will call [[insert()]] when [[isNewRecord]] is `true`, or [[update()]]
         * when [[isNewRecord]] is `false`.
         *
         * For example, to save a customer record:
         *
         * ```php
         * $customer = new Customer; // or $customer = Customer::findOne($id);
         * $customer->name = $name;
         * $customer->email = $email;
         * $customer->save();
         * ```
         *
         * @param bool $runValidation whether to perform validation (calling [[validate()]])
         * before saving the record. Defaults to `true`. If the validation fails, the record
         * will not be saved to the database and this method will return `false`.
         * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
         * meaning all attributes that are loaded from DB will be saved.
         * @return bool whether the saving succeeded (i.e. no validation errors occurred).
         */
        abstract public function save($runValidation = true, $attributeNames = null);

        /**
         * @return array
         */
        public function linkManyToManyRelations()
        {
            return [];
        }

        /**
         * @param string                   $relation
         * @param integer[]|ActiveRecord[] $values
         * @param bool                     $save
         *
         * @return bool
         */
        public function updateManyToManyRelations(string $relation, array $values, bool $save = false)
        {
            $ids = [];

            foreach ($values as $value) {
                if (is_numeric($value)) {
                    $ids[] = (int)$value;
                } elseif ($value instanceof self && ($value->hasProperty('id') || $value->hasAttribute('id'))) {
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $ids[] = (int)$value->id;
                }
            }

            $ids = array_unique($ids);

            $populateProperty = "{$relation}Ids";

            if ($this->hasProperty($populateProperty)) {
                $this->$populateProperty = $ids;

                if ($save) {
                    if ($this->save()) {
                        return true;
                    }
                } else {
                    return true;
                }
            }

            return false;
        }

        /**
         * @param string       $relation
         * @param ActiveRecord $record
         * @param bool         $save
         *
         * @return bool
         * @throws UnknownPropertyException
         */
        public function addManyToManyRelation(string $relation, ActiveRecord $record, bool $save = false)
        {
            $currentRelations = $this->findManyToManyRelations($relation);

            $currentRelations[] = $record;

            return $this->updateManyToManyRelations($relation, $currentRelations, $save);
        }

        /**
         * @param string  $relation
         * @param integer $id
         * @param bool    $save
         *
         * @return bool
         * @throws UnknownPropertyException
         */
        public function addManyToManyRelationById(string $relation, int $id, bool $save = false)
        {
            $currentRelations = $this->findManyToManyRelations($relation);

            $currentRelations[] = $id;

            return $this->updateManyToManyRelations($relation, $currentRelations, $save);
        }

        /**
         * @param string       $relation
         * @param ActiveRecord $record
         * @param bool         $save
         *
         * @return bool
         * @throws UnknownPropertyException
         */
        public function removeFromManyToManyRelation(string $relation, ActiveRecord $record, bool $save = false)
        {
            $currentRelations = $this->findManyToManyRelations($relation);

            if ($record->hasProperty('id') || $record->hasAttribute('id')) {
                $id = (int)$record->id;

                if (($key = array_search($id, $currentRelations)) !== false) {
                    unset($currentRelations[$key]);
                }
            }

            return $this->updateManyToManyRelations($relation, $currentRelations, $save);
        }

        /**
         * @param string  $relation
         * @param integer $id
         * @param bool    $save
         *
         * @return bool
         * @throws UnknownPropertyException
         */
        public function removeFromManyToManyRelationById(string $relation, int $id, bool $save = false)
        {
            $currentRelations = $this->findManyToManyRelations($relation);

            $id = (int)$id;

            if (($key = array_search($id, $currentRelations)) !== false) {
                unset($currentRelations[$key]);
            }

            return $this->updateManyToManyRelations($relation, $currentRelations, $save);
        }

        /**
         * @param string $relation
         *
         * @return integer[]
         *
         * @throws UnknownPropertyException
         */
        public function findManyToManyRelations(string $relation)
        {
            $populateProperty = "{$relation}Ids";

            if ($this->hasProperty($populateProperty)) {
                $ids = $this->$populateProperty;

                if (is_array($ids) && count($ids) > 0) {
                    $ids = array_map(
                        function ($id) {
                            return intval($id);
                        },
                        $ids
                    );
                } else {
                    $ids = [];
                }

                return $ids;
            }

            throw new UnknownPropertyException("The application could not find the relation for {$relation}.");
        }
    }
