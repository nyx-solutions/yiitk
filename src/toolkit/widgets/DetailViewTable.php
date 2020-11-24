<?php

    namespace yiitk\widgets;

    use Closure;
    use Yii;
    use yii\base\Arrayable;
    use yii\base\InvalidConfigException;
    use yii\base\Model;
    use yii\base\Widget;
    use yii\i18n\Formatter;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\HtmlHelper as Html;
    use yiitk\helpers\InflectorHelper as Inflector;

    class DetailViewTable extends Widget
    {
        /**
         * @var array|object the data model whose details are to be displayed. This can be a [[Model]] instance,
         * an associative array, an object that implements [[Arrayable]] interface or simply an object with defined
         * public accessible non-static properties.
         */
        public $model;

        /**
         * @var array|null a list of attributes to be displayed in the detail view. Each array element
         * represents the specification for displaying one particular attribute.
         *
         * An attribute can be specified as a string in the format of `attribute`, `attribute:format` or `attribute:format:label`,
         * where `attribute` refers to the attribute name, and `format` represents the format of the attribute. The `format`
         * is passed to the [[Formatter::format()]] method to format an attribute value into a displayable text.
         * Please refer to [[Formatter]] for the supported types. Both `format` and `label` are optional.
         * They will take default values if absent.
         *
         * An attribute can also be specified in terms of an array with the following elements:
         *
         * - `attribute`: the attribute name. This is required if either `label` or `value` is not specified.
         * - `label`: the label associated with the attribute. If this is not specified, it will be generated from the attribute name.
         * - `value`: the value to be displayed. If this is not specified, it will be retrieved from [[model]] using the attribute name
         *   by calling [[ArrayHelper::getValue()]]. Note that this value will be formatted into a displayable text
         *   according to the `format` option. Since version 2.0.11 it can be defined as closure with the following
         *   parameters:
         *
         *   ```php
         *   function ($model, $widget)
         *   ```
         *
         *   `$model` refers to displayed model and `$widget` is an instance of `DetailView` widget.
         *
         * - `format`: the type of the value that determines how the value would be formatted into a displayable text.
         *   Please refer to [[Formatter]] for supported types and [[Formatter::format()]] on how to specify this value.
         * - `visible`: whether the attribute is visible. If set to `false`, the attribute will NOT be displayed.
         * - `contentOptions`: the HTML attributes to customize value tag. For example: `['class' => 'bg-red']`.
         *   Please refer to [[\yii\helpers\BaseHtml::renderTagAttributes()]] for the supported syntax.
         * - `captionOptions`: the HTML attributes to customize label tag. For example: `['class' => 'bg-red']`.
         *   Please refer to [[\yii\helpers\BaseHtml::renderTagAttributes()]] for the supported syntax.
         */
        public ?array $attributes = null;

        /**
         * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
         * what container tag should be used. It defaults to `table` if not set.
         * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
         */
        public array $options = ['class' => 'table table-bordered detail-view detail-view-table mb-5'];

        /**
         * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
         * what container tag should be used. It defaults to `table` if not set.
         * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
         */
        public array $tableRowOptions = ['class' => ' detail-view-table-row'];

        /**
         * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
         * what container tag should be used. It defaults to `table` if not set.
         * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
         */
        public array $tableColLabelOptions = [];

        /**
         * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
         * what container tag should be used. It defaults to `table` if not set.
         * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
         */
        public array $tableColValueOptions = [];

        /**
         * @var array|Formatter the formatter used to format model attribute values into displayable texts.
         * This can be either an instance of [[Formatter]] or an configuration array for creating the [[Formatter]]
         * instance. If this property is not set, the `formatter` application component will be used.
         */
        public $formatter;

        //region Initialization
        /**
         * Initializes the detail view.
         * This method will initialize required property values.
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            if ($this->model === null) {
                throw new InvalidConfigException('Please specify the "model" property.');
            }

            if ($this->formatter === null) {
                $this->formatter = Yii::$app->getFormatter();
            } elseif (is_array($this->formatter)) {
                $this->formatter = Yii::createObject($this->formatter);
            }

            if (!$this->formatter instanceof Formatter) {
                throw new InvalidConfigException('The "formatter" property must be either a Format object or a configuration array.');
            }

            if (!is_array($this->options)) {
                $this->options = [];
            }

            if (!is_array($this->tableRowOptions)) {
                $this->tableRowOptions = [];
            }

            if (!is_array($this->tableColLabelOptions)) {
                $this->tableColLabelOptions = [];
            }

            if (!is_array($this->tableColValueOptions)) {
                $this->tableColValueOptions = [];
            }

            $this->normalizeAttributes();

            if (!isset($this->options['id'])) {
                $this->options['id'] = $this->getId();
            }
        }
        //endregion

        //region Run
        /**
         * Renders the detail view.
         * This is the main entry of the whole detail view rendering.
         */
        public function run()
        {
            $rows = [];
            $cols = 0;

            foreach ($this->attributes as $attribute) {
                if (!empty($attribute) && !ArrayHelper::isAssociative($attribute, false)) {
                    $attribute = array_values($attribute);

                    $totalCols = (count($attribute) * 2);

                    if ($totalCols > $cols) {
                        $cols = $totalCols;
                    }
                }
            }

            $defaultLabelWidth = '25%';
            $defaultValueWidth = '75%';

            if ($cols === 4) {
                $defaultLabelWidth = '20%';
                $defaultValueWidth = '30%';
            } elseif ($cols > 4) {
                $defaultLabelWidth = round(100 / $cols).'%';
                $defaultValueWidth = round(100 / $cols).'%';
            }

            $this->attributes = array_values($this->attributes);

            /** @noinspection SlowArrayOperationsInLoopInspection */
            /** @noinspection ForeachInvariantsInspection */
            for ($i = 0; $i < count($this->attributes); $i++) {
                $html           = '';
                $attribute      = $this->attributes[$i];
                $availableCols  = $cols;
                $currentHtmlRow = '';

                if (!empty($attribute) && !ArrayHelper::isAssociative($attribute, false)) {
                    $attribute = array_values($attribute);

                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    /** @noinspection ForeachInvariantsInspection */
                    for ($j = 0; $j < count($attribute); $j++) {
                        $availableCols -= 2;

                        if ((count($attribute) - 1) === $j) {
                            $currentHtmlRow .= $this->renderAttribute($attribute[$j], ($availableCols + 1), $defaultLabelWidth, $defaultValueWidth);
                        } else {
                            $currentHtmlRow .= $this->renderAttribute($attribute[$j], 0, $defaultLabelWidth, $defaultValueWidth);
                        }
                    }
                } else {
                    $isGroupRow = $this->isGroupRow($attribute);

                    if ($isGroupRow) {
                        if (isset($attribute['visible']) && !$attribute['visible']) {
                            continue;
                        }

                        $currentRowClasses = 'detail-view-table-group';

                        $options = ['class' => 'detail-view-table-group', 'colspan' => $cols];

                        if (isset($attribute['options']) && is_array($attribute['options'])) {
                            if (isset($attribute['options']['class'])) {
                                $attribute['options']['class'] .= " {$currentRowClasses}";
                            }

                            $options = ArrayHelper::merge($options, $attribute['options']);
                        }

                        $currentHtmlRow .= Html::tag('th', $attribute['label'], $options);
                    } else {
                        $currentHtmlRow .= $this->renderAttribute($attribute, ($availableCols - 1), $defaultLabelWidth, $defaultValueWidth);
                    }
                }

                $html .= Html::tag('tr', $currentHtmlRow, $this->tableRowOptions);

                $rows[] = $html;
            }

            unset($totalCols);

            $options = $this->options;

            $tag = ArrayHelper::remove($options, 'tag', 'table');

            echo Html::tag($tag, implode("\n", $rows), $options);
        }
        //endregion

        //region Render
        /**
         * Renders a single attribute.
         *
         * @param array  $attribute the specification of the attribute to be rendered.
         * @param int    $colspan   the zero-based index of the attribute in the [[attributes]] array
         * @param string $defaultLabelWidth
         * @param string $defaultValueWidth
         *
         * @return string the rendering result
         */
        protected function renderAttribute(array $attribute, int $colspan, string $defaultLabelWidth, string $defaultValueWidth): string
        {
            $userLabelOptions = ((isset($attribute['labelOptions']) && is_array($attribute['labelOptions'])) ? $attribute['labelOptions'] : []);
            $userValueOptions = ((isset($attribute['valueOptions']) && is_array($attribute['valueOptions'])) ? $attribute['valueOptions'] : []);

            $labelOptions = ArrayHelper::merge($this->tableColLabelOptions, $userLabelOptions, []);
            $valueOptions = ArrayHelper::merge($this->tableColValueOptions, $userValueOptions, []);

            $labelWidth = 'width:'.((!empty($attribute['labelWidth'])) ? $attribute['labelWidth'] : $defaultLabelWidth).';';
            $valueWidth = 'width:'.((!empty($attribute['valueWidth'])) ? $attribute['valueWidth'] : $defaultValueWidth).';';

            if ($colspan > 1) {
                $valueOptions['colspan'] = $colspan;
            }

            $labelClass = 'detail-view-table-label';
            $valueClass = 'detail-view-table-value';

            if (isset($labelOptions['class'])) {
                $labelOptions['class'] .= " {$labelClass}";
            } else {
                $labelOptions['class'] = $labelClass;
            }

            if (isset($valueOptions['class'])) {
                $valueOptions['class'] .= " {$valueClass}";
            } else {
                $valueOptions['class'] = $valueClass;
            }

            if (isset($labelOptions['style'])) {
                $labelOptions['style'] .= ";{$labelWidth}";
            } else {
                $labelOptions['style'] = $labelWidth;
            }

            if ($colspan === 0) {
                if (isset($valueOptions['style'])) {
                    $valueOptions['style'] .= ";{$valueWidth}";
                } else {
                    $valueOptions['style'] = $valueWidth;
                }
            }

            $label = Html::tag('th', $attribute['label'], $labelOptions);
            $value = Html::tag('td', $this->formatter->format($attribute['value'], $attribute['format']), $valueOptions);

            return "{$label}{$value}";
        }
        //endregion

        //region Normalize Attributes
        /**
         * Normalizes the attribute specifications.
         * @throws InvalidConfigException
         */
        protected function normalizeAttributes(): void
        {
            if ($this->attributes === null) {
                if ($this->model instanceof Model) {
                    $this->attributes = $this->model->attributes();
                } elseif (is_object($this->model)) {
                    $this->attributes = (($this->model instanceof Arrayable) ? array_keys($this->model->toArray()) : array_keys(get_object_vars($this->model)));
                } elseif (is_array($this->model)) {
                    $this->attributes = array_keys($this->model);
                } else {
                    throw new InvalidConfigException('The "model" property must be either an array or an object.');
                }

                sort($this->attributes);

                $this->attributes = array_values($this->attributes);
            }

            foreach ($this->attributes as $i => $attribute) {
                if (is_array($attribute) && isset($attribute['columns'])) {
                    $attribute = $attribute['columns'];

                    if (!is_array($attribute)) {
                        $attribute = [];
                    }

                    $this->attributes[$i] = $attribute;
                }

                if (is_array($attribute) && !empty($attribute)) {
                    if ($this->isGroupRow($attribute)) {
                        $this->attributes[$i] = $attribute;

                        continue;
                    }

                    foreach ($attribute as $j => $innerAttribute) {
                        $innerAttribute = $this->normalizeAttribute($innerAttribute);

                        if ($innerAttribute !== false) {
                            $attribute[$j] = $innerAttribute;
                        } else {
                            unset($attribute[$j]);
                        }
                    }

                    if (!empty($attribute)) {
                        $this->attributes[$i] = $attribute;
                    } else {
                        unset($this->attributes[$i]);
                    }
                } else {
                    $attribute = $this->normalizeAttribute($attribute);

                    if ($attribute !== false) {
                        $this->attributes[$i] = $attribute;
                    }
                }
            }
        }

        /**
         * Normalizes singe attribute specifications.
         *
         * @param $attribute
         *
         * @return array|bool
         *
         * @throws InvalidConfigException
         */
        protected function normalizeAttribute($attribute)
        {
            if (is_string($attribute)) {
                if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $attribute, $matches)) {
                    throw new InvalidConfigException('The attribute must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
                }

                $attribute = [
                    'attribute' => $matches[1],
                    'format'    => ($matches[3] ?? 'text'),
                    'label'     => ($matches[5] ?? null),
                ];
            }

            if (!is_array($attribute)) {
                throw new InvalidConfigException('The attribute configuration must be an array.');
            }

            if (isset($attribute['visible']) && !$attribute['visible']) {
                return false;
            }

            if (!isset($attribute['format'])) {
                $attribute['format'] = 'text';
            }

            if (isset($attribute['attribute'])) {
                $attributeName = $attribute['attribute'];

                if (!isset($attribute['label'])) {
                    $attribute['label'] = (($this->model instanceof Model) ? $this->model->getAttributeLabel($attributeName) : Inflector::camel2words($attributeName, true));
                }

                if (!array_key_exists('value', $attribute)) {
                    $attribute['value'] = ArrayHelper::getValue($this->model, $attributeName);
                }
            } elseif (!isset($attribute['label']) || !array_key_exists('value', $attribute)) {
                throw new InvalidConfigException('The attribute configuration requires the "attribute" element to determine the value and display label.');
            }

            if ($attribute['value'] instanceof Closure) {
                $attribute['value'] = call_user_func($attribute['value'], $this->model, $this);
            }

            if (is_string($attribute['value'])) {
                $attribute['value'] = trim($attribute['value']);
            }

            if (empty($attribute['value'])) {
                $attribute['value'] = null;
            }

            return $attribute;
        }
        //endregion

        //region Helpers
        /**
         * @param array $row
         *
         * @return bool
         */
        protected function isGroupRow(array $row): bool
        {
            return ($row['group'] && !empty($row['label']));
        }
        //endregion
    }
