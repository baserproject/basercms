<?php

/**
 * メールフィールドヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * メールフィールドヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailfieldHelper extends AppHelper {

/**
 * htmlの属性を取得する
 *
 * @param array $data メールフィールドデータ
 * @return array HTML属性
 */
	public function getAttributes($data) {
		if (isset($data['MailField'])) {
			$data = $data['MailField'];
		}

		$attributes['size'] = $data['size'];
		$attributes['rows'] = $data['rows'];
		$attributes['maxlength'] = $data['maxlength'];
		$attributes['separator'] = $data['separator'];
		$attributes['class'] = $data['class'];

		if (!empty($data['options'])) {
			$options = explode("|", $data['options']);
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
	public function getOptions($data) {
		if (isset($data['MailField'])) {
			$data = $data['MailField'];
		}

		$attributes = $this->getAttributes($data);

		// コントロールソースを変換
		if (!empty($data['source'])) {

			if ($data['type'] != "check") {
				$values = explode("|", $data['source']);
				$i = 0;
				foreach ($values as $value) {
					$i++;
					$source[$i] = $value;
				}

				return $source;
			}
		}
	}

}
