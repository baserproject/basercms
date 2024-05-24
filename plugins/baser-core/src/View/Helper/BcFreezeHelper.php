<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;

use Cake\Utility\Inflector;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcFreezeHelper
 */
class BcFreezeHelper extends BcFormHelper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * 凍結状態
     *
     * @var boolean
     */
    public $freezed = false;

    /**
     * フォームを凍結させる
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function freeze()
    {
        $this->freezed = true;
    }

    /**
     * テキストボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options html属性
     * - 凍結時に、$attributes["value"]が指定されている場合、その値がvalueになる。
     * 　指定されてない場合、$this->request->data[$model][$field]がvalueになる。
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function text(string $fieldName, array $options = []): string
    {
        if ($this->freezed) {
            if (strpos($fieldName, '.') !== false) {
                [, $field] = explode('.', $fieldName);
            } else {
                $field = $fieldName;
            }
            if (isset($options)) {
                $options = $options + ['type' => 'hidden'];
            } else {
                $options = ['type' => 'hidden'];
            }
            if (isset($options["value"])) {
                $value = $options["value"];
            } else {
                $value = $this->getSourceValue($field);
            }
            return parent::text($fieldName, $options) . h($value);
        } else {
            return parent::text($fieldName, $options);
        }
    }

    /**
     * select プルダウンメニューを表示
     *
     * @param string $fieldName フィールド文字列
     * @param iterable $options コントロールソース
     * @param array $attributes html属性
     * - $attributes['cols']が指定されている場合、値の文字の横幅を指定できる
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function select(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        if ($this->freezed) {
            return $this->freezeControll($fieldName, $options, $attributes);
        } else {
            // 横幅を設定する
            // 指定した文字数より足りない文字数分スペースを埋める処理としている為、
            // 等幅フォントを設定しないとちゃんとした横幅にはならない
            if (!empty($attributes['cols'])) {
                foreach($options as $key => $option) {
                    if ($attributes['cols'] > mb_strlen($option)) {
                        $pad = str_repeat('　', $attributes['cols'] - mb_strlen($option));
                        $options[$key] = $option . $pad;
                    }
                }
            }
            return parent::select($fieldName, $options, $attributes);
        }
    }

    /**
     * 日付タグを表示
     *
     * @param string $fieldName フィールド文字列
     * @param string $dateFormat 日付フォーマット
     * @param string $timeFormat 時間フォーマット
     * @param array $attributes html属性
     * - 凍結時、$attributes['selected']に要素を格納することで日付を選択する
     * (例) $attributes['selected'] = array('selected' => array('year' => '2010', 'month' => '4', 'day' => '1'))
     * @return string htmlタグ
     */
    public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = []): string
    {
        if ($this->freezed) {
            $year = $month = $day = $hour = $min = $meridian = $showEmpty = $selected = null;
            if (isset($attributes['selected'])) {
                $selected = $attributes['selected'];
            }
            if (isset($attributes['empty'])) {
                $showEmpty = $attributes['empty'];
            }

            if (empty($selected)) {
                $selected = $this->getSourceValue($fieldName);
            }

            if ($selected === null && $showEmpty != true) {
                $selected = time();
            }

            if (!empty($selected)) {
                if (is_array($selected)) {
                    extract($selected);
                } else {
                    if (is_numeric($selected)) {
                        $selected = strftime('%Y-%m-%d %H:%M:%S', $selected);
                    }
                    $meridian = 'am';
                    $pos = strpos($selected, '-');
                    if ($pos !== false) {
                        $date = explode('-', $selected);
                        $days = explode(' ', $date[2]);
                        $day = $days[0];
                        $month = $date[1];
                        $year = $date[0];
                    } else {
                        $days[1] = $selected;
                    }

                    if ($timeFormat != 'NONE' && !empty($timeFormat)) {
                        $time = explode(':', $days[1]);
                        $check = str_replace(':', '', $days[1]);

                        if (($check > 115959) && $timeFormat == '12') {
                            $time[0] = $time[0] - 12;
                            $meridian = 'pm';
                        } elseif ($time[0] == '00' && $timeFormat == '12') {
                            $time[0] = 12;
                        } elseif ($time[0] > 12) {
                            $meridian = 'pm';
                        }
                        if ($time[0] == 0 && $timeFormat == '12') {
                            $time[0] = 12;
                        }
                        $hour = $time[0];
                        $min = $time[1];
                    }
                }
            }

            $elements = ['Day', 'Month', 'Year', 'Hour', 'Minute', 'Meridian'];
            $defaults = [
                'minYear' => null, 'maxYear' => null, 'separator' => '&nbsp;'
            ];
            $attributes = array_merge($defaults, (array)$attributes);
            $minYear = $attributes['minYear'];
            $maxYear = $attributes['maxYear'];
            $separator = $attributes['separator'];
            if (isset($attributes['id'])) {
                if (is_string($attributes['id'])) {
                    // build out an array version
                    foreach($elements as $element) {
                        $selectAttrName = 'select' . $element . 'Attr';
                        ${$selectAttrName} = $attributes;
                        ${$selectAttrName}['id'] = $attributes['id'] . $element;
                    }
                } elseif (is_array($attributes['id'])) {
                    // check for missing ones and build selectAttr for each element
                    foreach($elements as $element) {
                        $selectAttrName = 'select' . $element . 'Attr';
                        ${$selectAttrName} = $attributes;
                        ${$selectAttrName}['id'] = $attributes['id'][strtolower($element)];
                    }
                }
            } else {
                // build the selectAttrName with empty id's to pass
                foreach($elements as $element) {
                    $selectAttrName = 'select' . $element . 'Attr';
                    ${$selectAttrName} = $attributes;
                }
            }
            $selects = [];
            if (preg_match('/^W/', $dateFormat)) {
                $selects[] = $this->wyear($fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty) . "年";
            } else {
                $selectYearAttr['value'] = $year;
                $selects[] = $this->freezeControll($fieldName . ".year", [], $selectYearAttr) . "年";
            }

            // TODO 値の出力はBcTextにまとめた方がよいかも
            // メール本文への出力も同じ処理を利用する。（改行の処理などはどうするか。。）
            $selectMonthAttr['value'] = $month;
            $selectDayAttr['value'] = $day;
            $selects[] = $this->freezeControll($fieldName . ".month", [], $selectMonthAttr) . "月";
            $selects[] = $this->freezeControll($fieldName . ".day", [], $selectDayAttr) . "日";
            if ($timeFormat) {
                $selects[] = $this->freezeControll($fieldName . ".hour", [], ['value' => $hour]) . "時";
                $selects[] = $this->freezeControll($fieldName . ".min", [], ['value' => $min]) . "分";
            }
            return implode($separator, $selects);
        } else {
            return parent::dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
        }
    }

    /**
     * 和暦年
     *
     * @param string $fieldName Prefix name for the SELECT element
     * @param integer $minYear First year in sequence
     * @param integer $maxYear Last year in sequence
     * @param string $selected Option which is selected.
     * @param array $attributes Attribute array for the select elements.
     * @param boolean $showEmpty Show/hide the empty select option
     * @return string
     */
    public function wyear($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = [], $showEmpty = true)
    {

        if ($this->freezed) {
            if ((empty($selected) || $selected === true) && $value = $this->getSourceValue($fieldName)) {
                if (is_array($value)) {
                    extract($value);
                    $selected = $year;
                } else {
                    if (empty($value)) {
                        if (!$showEmpty && !$maxYear) {
                            $selected = 'now';
                        } elseif (!$showEmpty && $maxYear && !$selected) {
                            $selected = $maxYear;
                        }
                    } else {
                        $selected = $value;
                    }
                }
            }
            $freezeText = '';
            if (strlen($selected) > 4 || $selected === 'now') {
                $wareki = $this->BcTime->convertToWareki(date('Y-m-d', strtotime($selected)));
                $w = $this->BcTime->wareki($wareki);
                $wyear = $this->BcTime->wyear($wareki);
                $selected = $w . '-' . $wyear;
                $freezeText = $this->BcTime->nengo($w) . ' ' . $wyear;
            } elseif ($selected === false) {
                $selected = null;
            } elseif (strpos($selected, '-') === false) {
                $wareki = $this->BcTime->convertToWareki($this->getSourceValue($fieldName));
                if ($wareki) {
                    $w = $this->BcTime->wareki($wareki);
                    $wyear = $this->BcTime->wyear($wareki);
                    $selected = $w . '-' . $wyear;
                    $freezeText = $this->BcTime->nengo($w) . ' ' . $wyear;
                } else {
                    $selected = null;
                }
            } else {
                $wareki = $this->BcTime->convertToWareki($this->getSourceValue($fieldName));
                if ($wareki) {
                    $w = $this->BcTime->wareki($wareki);
                    $wyear = $this->BcTime->wyear($wareki);
                    $selected = $w . '-' . $wyear;
                    $freezeText = $this->BcTime->nengo($w) . ' ' . $wyear;
                } else {
                    $selected = null;
                }
            }
            return $freezeText . $this->hidden($fieldName . ".wareki", ['value' => true]) . $this->hidden($fieldName . ".year", ['value' => $selected]);
        } else {
            return parent::wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
        }
    }

    /**
     * チェックボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options html属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function checkbox(string $fieldName, array $options = []): array|string
    {
        if ($this->freezed) {
            $label = '';
            if (isset($options['label'])) {
                $label = $options['label'];
            }
            $options = [0 => '', 1 => $label];
            return $this->freezeControll($fieldName, $options, $options);
        } else {
            return parent::checkbox($fieldName, $options);
        }
    }

    /**
     * テキストエリアを表示する
     *
     * @param string フィールド文字列
     * @param array html属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function textarea($fieldName, $options = []): string
    {
        if ($this->freezed) {
            if (strpos($fieldName, '.') !== false) {
                [, $field] = explode('.', $fieldName);
            } else {
                $field = $fieldName;
            }
            $options = $options + ['type' => 'hidden'];
            if (isset($options["value"])) {
                $value = $options["value"];
            } else {
                $value = $this->getSourceValue($field);
            }
            if ($value) {
                return parent::text($fieldName, $options) . nl2br(h($value));
            } else {
                return "&nbsp;";
            }
        } else {
            return parent::textarea($fieldName, $options);
        }
    }

    /**
     * ラジオボタンを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options コントロールソース
     * @param array $attributes html属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function radio($fieldName, $options = [], $attributes = []): string
    {
        if ($this->freezed) {
            return $this->freezeControll($fieldName, $options, $attributes);
        } else {
            return parent::radio($fieldName, $options, $attributes);
        }
    }

    /**
     * ファイルタグを出力
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     */
    public function file($fieldName, $options = []): string
    {
        if ($this->freezed) {
            $value = $this->getSourceValue($fieldName);
            $sessionKey = $this->getSourceValue($fieldName . '_tmp');
            if ($sessionKey) {
                return parent::hidden($fieldName . "_tmp", ['value' => $sessionKey]) . $this->BcUpload->fileLink($fieldName, $this->_getContext()->entity(), $options);
            } else {
                $delValue = $this->getSourceValue($fieldName . '_delete');
                if ($delValue) {
                    return parent::hidden($fieldName, ['value' => $value]) . parent::hidden($fieldName . '_delete', ['value' => true]) . $this->BcUpload->fileLink($fieldName, $this->_getContext()->entity(), $options) . '<br>' . __d('baser_core', '削除する');
                } else {
                    return parent::hidden($fieldName, ['value' => $value]) . $this->BcUpload->fileLink($fieldName, $this->_getContext()->entity(), $options);
                }
            }
        } else {
            return parent::file($fieldName, $options);
        }
    }

    /**
     * ファイルコントロール（画像）を表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @param array $imageAttributes 画像属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function image($fieldName, $attributes = [], $imageAttributes = [])
    {

        if (!$attributes) {
            $attributes = [];
        }

        $output = "";
        $imageAttributes = array_merge(['ext' => 'jpg', 'alt' => '', 'dir' => '', 'id' => ''], $imageAttributes);

        if (!empty($imageAttributes['subdir'])) {
            $imageAttributes['subdir'] .= DS;
        }

        [$model, $field] = explode('.', $fieldName);

        if ($this->freezed) {

            $attributes = array_merge($attributes, ['type' => 'hidden']);

            // 確認画面
            if (!empty($this->request->data[$model][$field]['name'])) {
                $path = "tmp" . DS . Inflector::underscore($model) . DS . "img" . DS . $field . $imageAttributes['ext'] . "?" . rand();
                unset($imageAttributes['ext']);
                $output = parent::text($fieldName . "_exists", $attributes);
                $output .= sprintf($this->Html->_tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
                return $output;
                // 通常表示
            } else {
                if (!empty($this->request->data[$model][$field . '_exists'])) {
                    $path = DS . $imageAttributes['dir'] . DS . Inflector::tableize($model) . DS . $imageAttributes['id'] . DS . $field . "." . $imageAttributes['ext'] . "?" . rand();
                    unset($imageAttributes['ext']);
                    return sprintf($this->Html->_tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
                } else {
                    return "&nbsp;";
                }
            }
        } else {
            if (!empty($this->request->data[$model][$field . '_exists'])) {
                $path = DS . $imageAttributes['dir'] . DS . Inflector::tableize($model) . DS . $imageAttributes['id'] . DS . $field . "." . $imageAttributes['ext'] . "?" . rand();
                unset($imageAttributes['ext']);
                $output = sprintf($this->Html->_tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
                $output .= "<br />" . $this->checkbox($fieldName . "_delete", ['label' => __d('baser_core', '削除する')]);
            }
            return parent::file($fieldName, $attributes) . "<br />" . $output;
        }
    }

    /**
     * TELボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function tel($fieldName, $attributes = [])
    {
        if ($this->freezed) {
            if (isset($attributes["value"])) {
                $value = $attributes["value"];
            } else {
                $value = $this->getSourceValue($fieldName);
            }
            return parent::hidden($fieldName, $attributes) . h($value);
        } else {
            return parent::tel($fieldName, $attributes);
        }
    }

    /**
     * テキストボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options html属性
     * @return    string    htmlタグ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function email($fieldName, $options = [])
    {
        if ($this->freezed) {
            if (isset($options["value"])) {
                $value = $options["value"];
            } else {
                $value = $this->getSourceValue($fieldName);
            }
            return parent::hidden($fieldName, $options) . h($value);
        } else {
            return parent::email($fieldName, $options);
        }
    }

    /**
     * 数値ボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options html属性
     * @return    string    htmlタグ
     * @checked
     * @noTodo
     */
    public function number($fieldName, $options = [])
    {
        if ($this->freezed) {
            if (isset($options["value"])) {
                $value = $options["value"];
            } else {
                $value = $this->getSourceValue($fieldName);
            }
            return parent::hidden($fieldName, $options) . h($value);
        } else {
            return parent::number($fieldName, $options);
        }
    }

    /**
     * パスワードボックスを表示する
     *
     * @param string $fieldName フィールド文字列
     * @param array $options html属性
     * - 凍結時に、valueはマスクして表示する。
     * @return    string    htmlタグ
     * @checked
     * @noTodo
     */
    public function password($fieldName, $options = [])
    {
        if ($this->freezed) {
            if (isset($options["value"])) {
                $value = $options["value"];
            } else {
                $value = $this->getSourceValue($fieldName);
            }
            $value = preg_replace('/./', '*', $value);
            return parent::hidden($fieldName, $options) . h($value);
        } else {
            return parent::password($fieldName, $options);
        }
    }

    /**
     * カレンダーコントロール付きのテキストフィールド
     * jquery-ui-1.7.2 必須
     *
     * @param string $fieldName フィールド文字列
     * @param array $options HTML属性
     * @return string html
     * @checked
     * @noTodo
     */
    public function datepicker($fieldName, $options = [])
    {

        if ($this->freezed) {
            if (isset($options)) {
                $options = array_merge($options, ['type' => 'hidden']);
            } else {
                $options = ['type' => 'hidden'];
            }
            if (!empty($this->getSourceValue($fieldName))) {
                $value = date('Y/m/d', strtotime($this->getSourceValue($fieldName)));
            } else {
                $value = "";
            }
            return parent::text($fieldName, $options) . $value;
        } else {
            return parent::datepicker($fieldName, $options);
        }
    }

    /**
     * 凍結時用のコントロールを取得する
     * @param string $fieldName フィールド文字列
     * @param array $options コントロールソース
     * @param array $attributes html属性
     * @return string htmlタグ
     * @checked
     * @noTodo
     */
    public function freezeControll(string $fieldName, array $options, array $attributes = [])
    {
        $attributes = array_merge([
            'class' => ''
        ], $attributes);

        // 値を取得
        if (isset($attributes["value"])) {
            $value = $attributes["value"];
        } else {
            $value = $this->getSourceValue($fieldName);
        }

        // optionsによるソース有 「0」は通す
        if ($options && $value !== '' && !is_null($value)) {

            // HABTAM
            if (!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox') {
                $li = [];
                foreach($value as $id) {
                    if ($id && isset($options[$id])) {
                        $li[] = $this->Html->tag('li', $options[$id]);
                    }
                }
                $out = $this->Html->tag('ul', implode('', $li), array_merge($attributes, ['escape' => false]));
                $out = parent::hidden($fieldName, $attributes) . $out;

                // マルチチェック
            } elseif (!empty($attributes["multiple"]) && $attributes["multiple"] === 'checkbox') {

                $li = [];
                foreach($value as $data) {
                    if (isset($options[$data])) {
                        $li[] = $this->Html->tag('li', $options[$data]);
                    }
                }
                $out = $this->Html->tag('ul', implode('', $li), array_merge($attributes, ['escape' => false]));
                $out .= $this->hidden($fieldName, ['value' => $value, 'multiple' => true]);

                // 一般
            } elseif (empty($detail)) {

                if (isset($options[$value])) {
                    $value = $options[$value];
                } else {
                    $value = '';
                }

                $out = parent::hidden($fieldName, $attributes) . $value;

                // datetime
            } else {
                if (!empty($value[$detail])) {
                    $value = $options[$value[$detail]];
                } else {
                    $value = "";
                }
                $out = parent::hidden($fieldName, $attributes) . $value;
            }

            // 値なし
        } else {

            if ($options) {
                if (!$value && !empty($options[0])) {
                    $value = $options[0];
                } else {
                    $value = "";
                }
            } elseif (empty($detail)) {
                if (empty($value)) {
                    $value = null;
                }
            } elseif (is_array($value) && isset($value[$detail])) {
                $value = $value[$detail];
            }

            $out = parent::hidden($fieldName, $attributes) . $value;
        }

        return $out;
    }

}
