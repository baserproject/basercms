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

namespace BcMail\View\Helper;

use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\View\Helper\BcFreezeHelper;
use BcMail\Model\Entity\MailField;
use Cake\ORM\ResultSet;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフォームヘルパー
 *
 * @property BcBaserHelper $BcBaser
 */
class MailformHelper extends BcFreezeHelper
{

    /**
     * ヘルパー
     *
     * @var array
     */
    public $helpers = ['Html', 'BcTime', 'BcText', 'Js', 'BcUpload', 'BcCkeditor', 'BcBaser', 'BcContents', 'BcArray', 'Url'];

    /**
     * メールフィールドのデータよりコントロールを生成する
     *
     * @param string $type コントロールタイプ
     * @param string $fieldName フィールド文字列
     * @param array $options コントロールソース
     * @param array $attributes HTML属性
     * @return string フォームコントロールのHTMLタグ
     *
     * TODO ucmitz 2022/09/08
     * FormHelper に control が追加になった事によりシグネチャが変わった
     * メソッド名を変える必要あり。
     * 以前のシグネチャは以下のとおり
     * $type, $fieldName, $options, $attributes = [])
     */
    public function control(string $fieldName, array $attributes = []): string
    {
        $attributes = array_merge([
            'type' => 'text',
            'escape' => true,
            'options' => []
        ], $attributes);

        $type = $attributes['type'];
        $options = $attributes['options'];
        if(!empty($attributes['text_rows'])) $attributes['rows'] = $attributes['text_rows'];
        unset($attributes['options'], $attributes['regex'], $attributes['text_rows']);

        if ($this->freezed) {
            unset($attributes['type']);
        }

        $out = '';
        switch ($type) {

            case 'text':
            case 'email':
                unset($attributes['rows']);
                unset($attributes['empty']);
                $out = $this->text($fieldName, $attributes);
                break;

            case 'radio':
                unset($attributes['size']);
                unset($attributes['rows']);
                unset($attributes['maxlength']);
                unset($attributes['empty']);
                $attributes['legend'] = false;
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
                if (isset($attributes['empty'])) {
                    if (
                        strtolower($attributes['empty']) === 'false' ||
                        strtolower($attributes['empty']) === 'null'
                    ) {
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
                unset($attributes['empty']);
                $out = $this->prefTag($fieldName, null, $attributes, true);
                break;

            case 'autozip':
                unset($attributes['rows']);
                unset($attributes['empty']);
                $address1 = isset($options[key($options)])? $options[key($options)] : '';
                next($options);
                $address2 = isset($options[key($options)])? $options[key($options)] : '';
                if (!$address1) {
                    $address1 = '';
                    $address2 = '';
                } elseif (!$address2) {
                    $address2 = $address1;
                }
                $attributes['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$address1}','{$address2}')";
                unset($attributes['type']);
                $out = $this->BcBaser->js('vendor/ajaxzip3', false) .
                    $this->text($fieldName, array_merge($attributes, [
                        'style' => 'width:auto!important'
                    ]));
                break;

            case 'check':
                unset($attributes['size']);
                unset($attributes['rows']);
                unset($attributes['maxlength']);
                unset($attributes['empty']);
                $out = $this->checkbox($fieldName, $attributes);
                break;

            case 'multi_check':
                unset($attributes['size']);
                unset($attributes['rows']);
                unset($attributes['maxlength']);
                unset($attributes['empty']);
                $attributes['multiple'] = 'checkbox';
                $attributes['value'] = null;
                $attributes['empty'] = false;
                $attributes['templateVars']['tag'] = 'span';
                $out = $this->select($fieldName, $options, $attributes);
                break;
            case 'file':
                unset($attributes['size']);
                unset($attributes['rows']);
                unset($attributes['maxlength']);
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
                unset($attributes['empty']);
                unset($attributes['size']);
                if ($attributes['maxlength'] === null) {
                    unset($attributes['maxlength']);
                }
                $out = $this->textarea($fieldName, $attributes);
                break;

            case 'tel':
                unset($attributes['rows']);
                unset($attributes['empty']);
                $attributes['type'] = 'tel';
                $out = $this->tel($fieldName, $attributes);
                break;

            case 'password':
                unset($attributes['rows']);
                unset($attributes['empty']);
                $out = $this->password($fieldName, $attributes);
                break;

            case 'hidden':
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
    public function create($context = null, $options = []): string
    {
        if (!isset($options['type'])) {
            $options['type'] = 'file';
        }
        if (!empty($options['url']) && !empty($this->_View->getRequest()->getAttribute('currentSite')->same_main_url)) {
            $options['url'] = $this->BcContents->getPureUrl($options['url'], $this->_View->getRequest()->getAttribute('currentSite')->id);
        }
        return parent::create($context, $options);
    }

    /**
     * 認証キャプチャを表示する
     *
     * @param $fieldName
     * @param array $options オプション（初期値 : []）
     *    - `separate` : 画像と入力欄の区切り（初期値：''）
     *    - `class` : CSSクラス名（初期値：auth-captcha-image）
     */
    public function authCaptcha(string $fieldName, array $options = [])
    {
        $options = array_merge([
            'separate' => '',
            'class' => 'auth-captcha-image'
        ], $options);
        $captchaId = mt_rand(0, 99999999);
        $request = $this->getView()->getRequest();
        $url = $request->getAttribute('currentContent')->url;
        if (!empty($request->getAttribute('currentSite')->same_main_url)) {
            $url = $this->BcContents->getPureUrl($url, $request->getAttribute('currentSite')->id);
        }
        $options['helper']->element('auth_captcha', [
            'fieldName' => $fieldName,
            'captchaUrl' => $url . 'captcha/' . $captchaId,
            'captchaId' => $captchaId,
            'options' => $options,
        ]);
    }

    /**
     * 指定したgroup_validをもつフィールドのエラーを取得する
     *
     * @param array $mailFields
     * @param string $groupValid
     * @param array $options
     * @param bool $distinct 同じエラーメッセージをまとめる
     * @return array
     * @checked
     * @noTodo
     */
    public function getGroupValidErrors(array $mailFields, string $groupValid, array $options = [], bool $distinct = true)
    {
        $errors = [];
        foreach ($mailFields as $mailField) {
            if ($mailField->group_valid !== $groupValid) continue;
            if (!in_array('VALID_GROUP_COMPLATE', explode(',', $mailField->valid_ex))) continue;

            $errorMessage = $this->error($mailField->field_name, null, $options);
            if ($errorMessage && (!$distinct || !array_search($errorMessage, $errors))) {
                $errors[$mailField->field_name] = $errorMessage;
            }

            $context = $this->_getContext();
            if ($context->hasError($mailField->field_name)) {
                foreach ($context->error($mailField->field_name) as $key => $error) {
                    if ($error !== true) {
                        $entity = $context->entity();
                        $entity->setError($mailField->field_name, [], true);
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * メールフィールドのグループの最後か判定する
     *
     * - 次のフィールドがないもの
     * - 次のフィールドがある、次のフィールドとグループが違うもの
     *
     * @param ResultSet $mailFields
     * @param MailField $currentMailField
     * @return bool
     */
    public function isGroupLastField(ResultSet $mailFields, MailField $currentMailField)
    {
        if (empty($currentMailField->group_field)) return false;
        $mailFields = clone $mailFields;
        foreach ($mailFields as $mailField) {
            if ($currentMailField === $mailField) break;
        }

        $mailFields->next();
        if(!$mailFields->valid()) return true;
        $nextField = $mailFields->current();
        if(!$nextField->group_field || $currentMailField->group_field !== $nextField->group_field) {
            return true;
        }

        return false;
    }

}
