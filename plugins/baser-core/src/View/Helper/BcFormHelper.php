<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use \Cake\View\Helper\FormHelper;

/**
 * FormHelper 拡張クラス
 *
 * @package Baser.View.Helper
 * @property BcHtmlHelper $BcHtml
 */
class BcFormHelper extends FormHelper
{
    /**
     * Other helpers used by FormHelper
     *
     * @var array
     */
    public $helpers = [
        'Url',
        'Js',
        'Html',
        'BaserCore.BcHtml',
        'BaserCore.BcTime',
        'BaserCore.BcText',
        'BaserCore.BcUpload',
        'BaserCore.BcCkeditor'
    ];

    public function dispatchAfterForm($type = '')
    {

    }

    /**
     * コントロールソースを取得する
     * Model側でメソッドを用意しておく必要がある
     *
     * @param string $field フィールド名
     * @param array $options
     * @return Query|false コントロールソース
     */
    public function getControlSource($field, $options = [])
    {
        $count = preg_match_all('/\./is', $field, $matches);
        if ($count === 1) {
            [$modelName, $field] = explode('.', $field);
            $plugin = $this->_View->getPlugin();
            if ($plugin) {
                $modelName = $plugin . '.' . $modelName;
            }
        } elseif ($count === 2) {
            [$plugin, $modelName, $field] = explode('.', $field);
            $modelName = $plugin . '.' . $modelName;
        }
        if (empty($modelName)) {
            return false;
        }
        $model = TableRegistry::getTableLocator()->get($modelName);
        if ($model && method_exists($model, 'getControlSource')) {
            return $model->getControlSource($field, $options);
        } else {
            return false;
        }
    }

	/**
	 * カレンダーピッカー
     *
	 * jquery-ui-1系 必須
	 *
	 * @param string フィールド文字列
	 * @param array オプション
	 * @return string html
	 */
	public function datePicker($fieldName, $options = [])
	{
	    $options = array_merge([
            'autocomplete' => 'off',
            'id' => $this->_domId($fieldName),
            'value' => $this->context()->val($fieldName)
        ], $options);
		if ($options['value']) {
			[$options['value'],] = explode(" ", str_replace('-', '/', $options['value']));
		}
		unset($options['type']);
		$input = $this->text($fieldName, $options);
		$script = <<< SCRIPT_END
<script>
jQuery(function($){
	$("#{$options['id']}").datepicker();
});
</script>
SCRIPT_END;
		return $input . "\n" . $script;
	}

    /**
     * カレンダピッカーとタイムピッカー
     *
     * jquery.timepicker.js 必須
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     */
    public function dateTimePicker($fieldName, $options = [])
    {
        $options = array_merge([
            'div' => ['tag' => 'span'],
            'dateInput' => [],
            'dateDiv' => ['tag' => 'span'],
            'dateLabel' => ['text' => '日付'],
            'timeInput' => [],
            'timeDiv' => ['tag' => 'span'],
            'timeLabel' => ['text' => '時間'],
            'id' => $this->_domId($fieldName)
        ], $options);

        $dateOptions = array_merge($options, [
            'type' => 'datepicker',
            'div' => $options['dateDiv'],
            'label' => $options['dateLabel'],
            'autocomplete' => 'off'
        ], $options['dateInput']);

        $timeOptions = array_merge($options, [
            'type' => 'text',
            'div' => $options['timeDiv'],
            'label' => $options['timeLabel'],
            'autocomplete' => 'off',
            'size' => 8,
            'maxlength' => 8,
            'escape' => true,
            'id' => $options['id'] . '-time'
        ], $options['timeInput']);

        unset($options['dateDiv'], $options['dateLabel'], $options['timeDiv'], $options['timeLabel'], $options['dateInput'], $options['timeInput']);
        unset($dateOptions['dateDiv'], $dateOptions['dateLabel'], $dateOptions['timeDiv'], $dateOptions['timeLabel'], $dateOptions['dateInput'], $dateOptions['timeInput']);
        unset($timeOptions['dateDiv'], $timeOptions['dateLabel'], $timeOptions['timeDiv'], $timeOptions['timeLabel'], $timeOptions['dateInput'], $timeOptions['timeInput']);

        if (!isset($options['value'])) {
            $value = $this->context()->val($fieldName);
        } else {
            $value = $options['value'];
            unset($options['value']);
        }

        if ($value && $value != '0000-00-00 00:00:00') {
            [$dateValue, $timeValue] = explode(' ', $value);
            $dateOptions['value'] = $dateValue;
            $timeOptions['value'] = $timeValue;
        }

        $dateDivOptions = $timeDivOptions = $dateLabelOptions = $timeLabelOptions = null;
        if (!empty($dateOptions['div'])) {
            $dateDivOptions = $dateOptions['div'];
            unset($dateOptions['div']);
        }
        if (!empty($timeOptions['div'])) {
            $timeDivOptions = $timeOptions['div'];
            unset($timeOptions['div']);
        }
        if (!empty($dateOptions['label'])) {
            $dateLabelOptions = $dateOptions;
            unset($dateOptions['type'], $dateOptions['label']);
        }
        if (!empty($timeOptions['label'])) {
            $timeLabelOptions = $timeOptions;
            unset($timeOptions['type'], $timeOptions['label']);
        }

        $dateTag = $this->datePicker($fieldName . '_date', $dateOptions);
        if ($dateLabelOptions['label']) {
            $dateTag = $this->_getLabel($fieldName, $dateLabelOptions) . $dateTag;
        }
        if ($dateDivOptions) {
            $tag = 'div';
            if (!empty($dateDivOptions['tag'])) {
                $tag = $dateDivOptions['tag'];
                unset($dateDivOptions['tag']);
            }
            $dateTag = $this->BcHtml->tag($tag, $dateTag, $dateDivOptions);
        }

        $timeTag = $this->text($fieldName . '_time', $timeOptions);
        if ($timeLabelOptions['label']) {
            $timeTag = $this->_getLabel($fieldName, $timeLabelOptions) . $timeTag;
        }
        if ($timeDivOptions) {
            $tag = 'div';
            if (!empty($timeDivOptions['tag'])) {
                $tag = $timeDivOptions['tag'];
                unset($timeDivOptions['tag']);
            }
            $timeTag = $this->BcHtml->tag($tag, $timeTag, $timeDivOptions);
        }
        $hiddenTag = $this->hidden($fieldName, ['value' => $value]);
        $script = <<< SCRIPT_END
<script>
$(function(){
    var id = "{$options['id']}";
    var time = $("#" + id + "-time");
    var date = $("#" + id + "-date");
    time.timepicker({ 'timeFormat': 'H:i' });
    $([time, date]).change(function(){
        if(date.val() && !time.val()) {
            time.val('00:00');
        }
        var value = date.val().replace(/\//g, '-');
        if(time.val()) {
            value += ' ' + time.val();
        }
        $("#" + id).val(value);
    });
});
</script>
SCRIPT_END;
        return $dateTag . $timeTag . $hiddenTag . $script;
    }

}
