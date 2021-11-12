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

App::uses('BcHtmlHelper', 'View/Helper');
App::uses('BcFreezeHelper', 'View/Helper');

/**
 * メールフォームヘルパー
 *
 * @package Mail.View.Helper
 * @property BcBaserHelper $BcBaser
 * @property BcContentsHelper $BcContents
 */
class MailformHelper extends BcFreezeHelper
{

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['Html', 'BcTime', 'BcText', 'Js', 'BcUpload', 'BcCkeditor', 'BcBaser', 'BcContents', 'BcArray'];

	/**
	 * メールフィールドのデータよりコントロールを生成する
	 *
	 * @param string $type コントロールタイプ
	 * @param string $fieldName フィールド文字列
	 * @param array $options コントロールソース
	 * @param array $attributes HTML属性
	 * @return string フォームコントロールのHTMLタグ
	 */
	public function control($type, $fieldName, $options, $attributes = [])
	{
		$attributes['escape'] = true;
		$out = '';
		if ($this->freezed) {
			unset($attributes['type']);
		}

		unset($attributes['regex']);

		switch($type) {

			case 'text':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->text($fieldName, $attributes);
				break;

			case 'email':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->email($fieldName, $attributes);
				break;

			case 'radio':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['legend'] = false;
				if (!empty($attributes['separator'])) {
					$attributes['separator'] = $attributes['separator'];
				} else {
					$attributes['separator'] = "&nbsp;&nbsp;";
				}
				// CakePHPでは、初期値を指定していない場合に、hiddenタグを出力する仕様
				// 初期値が設定されている、かつ、空の選択肢を選択して送信する場合に、
				// フィールド自身が送信されないため、validatePost に引っかかってしまう
				// hiddenタグを強制的に出すため、falseを明示的に指定
				$attributes['hiddenField'] = false;
				$out = $this->hidden($fieldName, ['value' => '']);
				$out .= $this->radio($fieldName, $options, $attributes);
				break;

			case 'select':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				if (isset($attributes['empty'])) {
					if (strtolower($attributes['empty']) === 'false' ||
						strtolower($attributes['empty']) === 'null') {
						$showEmpty = false;
					} else {
						$showEmpty = $attributes['empty'];
					}
				} else {
					$showEmpty = true;
				}
				$attributes['value'] = null;
				$attributes['empty'] = $showEmpty;
				$out = $this->select($fieldName, $options, $attributes);
				break;

			case 'pref':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->prefTag($fieldName, null, $attributes, true);
				break;

			case 'autozip':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$count = 0;
				foreach($options as $option) {
					switch($count) {
						case 0:
							$address1 = $this->_name([], $option);
							break;
						case 1:
							$address2 = $this->_name([], $option);
							break;
						default:
							break;
					}
					$count++;
				}
				if (!isset($address1['name'])) {
					$address1['name'] = '';
					$address2['name'] = '';
				} elseif (!isset($address2['name'])) {
					$address2['name'] = $address1['name'];
				}
				$attributes['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$address1['name']}','{$address2['name']}')";
				$out = $this->Html->script('admin/vendors/ajaxzip3.js') . $this->text($fieldName, $attributes);
				break;

			case 'check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->checkbox($fieldName, $attributes);
				break;

			case 'multi_check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				if ($this->freezed) {
					unset($attributes['separator']);
				}
				$attributes['multiple'] = 'checkbox';
				$attributes['value'] = null;
				$attributes['empty'] = false;
				$out = $this->select($fieldName, $options, $attributes);
				break;
			case 'file':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['escape']);
				if (empty($attributes['width'])) {
					$attributes['width'] = 400;
				}
				$attributes['delCheck'] = false;
				if (!empty($attributes['maxFileSize'])) {
					$out = '<input type="hidden" name="MAX_FILE_SIZE" value="' . $attributes['maxFileSize'] * 1024 * 1024 . '" />';
				}
				unset($attributes['maxFileSize']);
				unset($attributes['fileExt']);
				$out .= $this->file($fieldName, $attributes);

				break;

			case 'date_time_calender':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$out = $this->datepicker($fieldName, $attributes);
				break;

			case 'date_time_wareki':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['monthNames'] = false;
				$attributes['separator'] = '&nbsp;';
				if (isset($attributes['minYear']) && $attributes['minYear'] === 'today') {
					$attributes['minYear'] = (int)date('Y');
				}
				if (isset($attributes['maxYear']) && $attributes['maxYear'] === 'today') {
					$attributes['maxYear'] = (int)date('Y');
				}
				$out = $this->dateTime($fieldName, 'WMD', null, $attributes);
				break;

			case 'textarea':
				$attributes['cols'] = $attributes['size'];
				unset($attributes['separator']);
				unset($attributes['empty']);
				unset($attributes['size']);
				if ($attributes['maxlength'] === null) {
					unset($attributes['maxlength']);
				}
				$out = $this->textarea($fieldName, $attributes);
				break;

			case 'tel':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$attributes['type'] = 'tel';
				$out = $this->tel($fieldName, $attributes);
				break;

			case 'number':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$attributes['type'] = 'number';
				$out = $this->number($fieldName, $attributes);
				break;

			case 'password':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->password($fieldName, $attributes);
				break;

			case 'hidden':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->hidden($fieldName, $attributes);
		}
		return $out;
	}


	/**
	 * create
	 * ファイル添付の対応のためにデフォルト値を変更
	 *
	 * @param array $model
	 * @param array $options
	 * @return string
	 */
	public function create($model = null, $options = [])
	{
		if (!isset($options['type'])) {
			$options['type'] = 'file';
		}
		if (!empty($options['url']) && !empty($this->request->params['Site']['same_main_url'])) {
			$options['url'] = $this->BcContents->getPureUrl($options['url'], $this->request->params['Site']['id']);
		}
		return parent::create($model, $options);
	}

	/**
	 * 認証キャプチャを表示する
	 *
	 * @param array $options オプション（初期値 : []）
	 *    - `separate` : 画像と入力欄の区切り（初期値：''）
	 *    - `class` : CSSクラス名（初期値：auth-captcha-image）
	 */
	public function authCaptcha($fieldName, $options = [])
	{
		$options = array_merge([
			'separate' => '',
			'class' => 'auth-captcha-image'
		], $options);
		$captchaId = mt_rand(0, 99999999);
		$url = $this->request->params['Content']['url'];
		if (!empty($this->request->params['Site']['same_main_url'])) {
			$url = $this->BcContents->getPureUrl($url, $this->request->params['Site']['id']);
		}
		$output = $this->BcBaser->getImg($url . '/captcha/' . $captchaId, ['alt' => __('認証画像'), 'class' => $options['class']]);
		$output .= $options['separate'] . $this->text($fieldName);
		$output .= $this->input('MailMessage.captcha_id', ['type' => 'hidden', 'value' => $captchaId]);
		echo $output;
	}

	/**
	 * 指定したgroup_validをもつフィールドのエラーを取得する
	 *
	 * @param array $mailFields
	 * @param string $groupValid
	 * @param array $options
	 * @param bool $distinct 同じエラーメッセージをまとめる
	 * @return array
	 */
	public function getGroupValidErrors($mailFields, $groupValid, $options = [], $distinct = true)
	{
		$errors = [];
		foreach($mailFields as $mailField) {
			if ($mailField['MailField']['group_valid'] !== $groupValid) {
				continue;
			}
			if (!in_array('VALID_GROUP_COMPLATE', explode(',', $mailField['MailField']['valid_ex']))) {
				continue;
			}
			if (!empty($this->validationErrors['MailMessage'][$mailField['MailField']['field_name']])) {
				foreach($this->validationErrors['MailMessage'][$mailField['MailField']['field_name']] as $key => $error) {
					if ($error === true) {
						unset($this->validationErrors['MailMessage'][$mailField['MailField']['field_name']][$key]);
					}
				}
			}
			$errorMessage = $this->error("MailMessage." . $mailField['MailField']['field_name'], null, $options);
			if ($errorMessage && (!$distinct || !array_search($errorMessage, $errors))) {
				$errors[$mailField['MailField']['field_name']] = $errorMessage;
			}
		}
		return $errors;
	}

	/**
	 * メールフィールドのグループの最後か判定する
	 * @param array $mailFields
	 * @param array $currentMailField
	 * @return bool
	 */
	public function isGroupLastField($mailFields, $currentMailField)
	{
		if (empty($currentMailField['group_field'])) {
			return false;
		}
		if (isset($currentMailField['MailField'])) {
			$currentMailField = $currentMailField['MailField'];
		}
		foreach($mailFields as $key => $mailField) {
			if ($currentMailField === $mailField['MailField']) {
				break;
			}
		}
		if (empty($mailFields[$key + 1]['MailField']['group_field']) ||
			$currentMailField['group_field'] !== $mailFields[$key + 1]['MailField']['group_field']) {
			return true;
		}
		return false;
	}
}
