<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * メールフィールドヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailfieldHelper extends AppHelper
{

	/**
	 * htmlの属性を取得する
	 *
	 * @param array $data メールフィールドデータ
	 * @return array HTML属性
	 */
	public function getAttributes($data)
	{
		if (isset($data['MailField'])) {
			$data = $data['MailField'];
		}

		$attributes['size'] = $data['size'];
		$attributes['rows'] = $data['rows'];
		$attributes['maxlength'] = $data['maxlength'];
		$attributes['separator'] = $data['separator'];
		$attributes['class'] = $data['class'];
		if ($data['type'] === 'multi_check') {
			$attributes['multiple'] = true;
		} elseif ($data['type'] === 'tel') {
			$attributes['type'] = 'tel';
		} elseif ($data['type'] === 'number') {
			$attributes['type'] = 'number';
		}
		if ($data['auto_complete'] != 'none') {
			$attributes['autocomplete'] = $data['auto_complete'];
		}
		if (!empty($data['options'])) {
			$options = preg_split('/(?<!\\\)\|/', $data['options']);
			$options = call_user_func_array('aa', $options);
			$attributes = am($attributes, $options);
		}
		return $attributes;
	}

	/**
	 * コントロールのソースを取得する
	 *
	 * @param array $data メールフィールドデータ
	 * @return array コントロールソース
	 */
	public function getOptions($data)
	{
		if (isset($data['MailField'])) {
			$data = $data['MailField'];
		}
		if (!empty($data['source'])) {
			if ($data['type'] !== "check") {
				$values = explode("\n", str_replace('|', "\n", $data['source']));
				$source = [];
				foreach($values as $value) {
					$source[$value] = $value;
				}
				return $source;
			}
		}
		return [];
	}

}
