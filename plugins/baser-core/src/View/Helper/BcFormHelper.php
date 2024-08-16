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

use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\Form\EntityContext;
use Cake\View\Helper\FormHelper;
use Cake\Datasource\EntityInterface;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * FormHelper 拡張クラス
 *
 * @property BcHtmlHelper $BcHtml
 * @property BcUploadHelper $BcUpload
 * @property BcCkeditorHelper $BcCkeditor
 */
#[\AllowDynamicProperties]
class BcFormHelper extends FormHelper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * Other helpers used by FormHelper
     *
     * @var array
     */
    public array $helpers = [
        'Url',
        'Js',
        'Html',
        'BaserCore.BcHtml',
        'BaserCore.BcTime',
        'BaserCore.BcText',
        'BaserCore.BcUpload',
        'BaserCore.BcCkeditor'
    ];

// CUSTOMIZE ADD 2014/07/02 ryuring
// >>>
    /**
     * フォームID
     *
     * @var string
     */
    private $formId = null;
// <<<

    /**
     * フォームの最後のフィールドの後に発動する前提としてイベントを発動する
     *
     * ### 発動側
     * フォームの</table>の直前に記述して利用する
     *
     * ### コールバック処理
     * プラグインのコールバック処理で CakeEvent::data['fields'] に
     * 配列で行データを追加する事でフォームの最後に行を追加する事ができる。
     *
     * ### イベント名
     * コントローラー名.Form.afterForm Or コントローラー名.Form.afterOptionForm
     *
     * ### 行データのキー（配列）
     * - title：見出欄
     * - input：入力欄
     *
     * ### 行データの追加例
     *  $View = $event->subject();    // $event は、CakeEvent
     *  $input = $View->BcForm->input('Page.add_field', ['type' => 'input']);
     *  $event->setData('fields', [
     *      [
     *          'title'    => '追加フィールド',
     *          'input'    => $input
     *      ]
     *  ]);
     *
     * @param string $type フォームのタイプ タイプごとにイベントの登録ができる
     * @return string 行データ
     * @checked
     * @noTodo
     */
    public function dispatchAfterForm($type = ''): string
    {
        if ($type) {
            $type = Inflector::camelize($type);
        }
        $event = $this->dispatchLayerEvent('after' . $type . 'Form', ['fields' => [], 'id' => $this->formId], ['class' => 'Form', 'plugin' => '']);
        $out = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $out .= "<tr>";
                    $out .= "<th class=\"bca-form-table__label\">" . $field['title'] . "</th>\n";
                    $out .= "<td class=\"bca-form-table__input\">" . $field['input'] . "</td>\n";
                    $out .= "</tr>";
                }
            }
        }
        return $out;
    }

    /**
     * コントロールソースを取得する
     * Model側でメソッドを用意しておく必要がある
     *
     * @param string $field フィールド名
     * @param array $options
     * @return array|false コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field, $options = [])
    {
        $count = preg_match_all('/\./is', $field, $matches);
        $plugin = $modelName = null;
        if ($count === 1) {
            [$modelName, $field] = explode('.', $field);
            $plugin = $this->_View->getPlugin();
        } elseif ($count === 2) {
            [$plugin, $modelName, $field] = explode('.', $field);
        }
        if (!$modelName) return false;
        $serviceName = (($plugin)?: 'App') . '\\Service\\' . $modelName . 'ServiceInterface';
        $modelName = (($plugin)? $plugin . '.' : '') . $modelName;
        if (method_exists($serviceName, 'getControlSource')) {
            return $this->getService($serviceName)->getControlSource($field, $options);
        }
        if (empty($modelName)) return false;
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
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
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
            'id' => $this->_domId($fieldName),
            'timeStep' => 30
        ], $options);

        $dateOptions = array_merge($options, [
            'type' => 'datepicker',
            'div' => $options['dateDiv'],
            'label' => $options['dateLabel'],
            'autocomplete' => 'off',
            'id' => $options['id'] . '-date',
        ], $options['dateInput']);

        $timeOptions = array_merge($options, [
            'type' => 'text',
            'div' => $options['timeDiv'],
            'label' => $options['timeLabel'],
            'autocomplete' => 'off',
            'size' => 8,
            'maxlength' => 8,
            'escape' => true,
            'id' => $options['id'] . '-time',
            'step' => $options['timeStep'],
        ], $options['timeInput']);

        unset($options['dateDiv'], $options['dateLabel'], $options['timeDiv'], $options['timeLabel'], $options['dateInput'], $options['timeInput'], $options['timeStep']);
        unset($dateOptions['dateDiv'], $dateOptions['dateLabel'], $dateOptions['timeDiv'], $dateOptions['timeLabel'], $dateOptions['dateInput'], $dateOptions['timeInput']);
        unset($timeOptions['dateDiv'], $timeOptions['dateLabel'], $timeOptions['timeDiv'], $timeOptions['timeLabel'], $timeOptions['dateInput'], $timeOptions['timeInput']);

        if (!isset($options['value'])) {
            $value = $this->context()->val($fieldName);
        } else {
            $value = $options['value'];
            unset($options['value']);
        }

        if ($value && $value != '0000-00-00 00:00:00') {
            if (strpos($value, ' ') !== false) {
                [$dateValue, $timeValue] = explode(' ', $value);
            } else {
                $dateValue = $value;
                $timeValue = '00:00:00';
            }
            $dateOptions['value'] = $dateValue;
            $timeOptions['value'] = $timeValue;
        }

        $step = $timeOptions['step'];
        unset($timeOptions['step']);

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
        $hiddenTag = $this->hidden($fieldName, ['value' => $value, 'id' => $options['id']]);
        if ($this->formProtector) {
            $this->unlockField($fieldName);
        }
        $script = <<< SCRIPT_END
<script>
$(function(){
    var id = "{$options['id']}";
    var time = $("#" + id + "-time");
    var date = $("#" + id + "-date");
    time.timepicker({ 'timeFormat': 'H:i', 'step': {$step} });
    date.add(time).change(function(){
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

    /**
     * Returns an HTML form element.
     *
     * ### Options:
     *
     * - `type` Form method defaults to autodetecting based on the form context. If
     *   the form context's isCreate() method returns false, a PUT request will be done.
     * - `method` Set the form's method attribute explicitly.
     * - `url` The URL the form submits to. Can be a string or a URL array.
     * - `encoding` Set the accept-charset encoding for the form. Defaults to `Configure::read('App.encoding')`
     * - `enctype` Set the form encoding explicitly. By default `type => file` will set `enctype`
     *   to `multipart/form-data`.
     * - `templates` The templates you want to use for this form. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     * - `context` Additional options for the context class. For example the EntityContext accepts a 'table'
     *   option that allows you to set the specific Table class the form should be based on.
     * - `idPrefix` Prefix for generated ID attributes.
     * - `valueSources` The sources that values should be read from. See FormHelper::setValueSources()
     * - `templateVars` Provide template variables for the formStart template.
     *
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use `null` to make a context-less form.
     * @param array $options An array of html attributes and options.
     * @return string An formatted opening FORM tag.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#Cake\View\Helper\FormHelper::
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create($context = null, $options = []): string
    {


        // CUSTOMIZE ADD 2014/07/03 ryuring
        // ブラウザの妥当性のチェックを除外する
        // >>>
        $options = array_merge([
            'novalidate' => true
        ], $options);

        $formId = $this->setId($this->createId($context, $options));

        // EVENT Form.beforeCreate
        $event = $this->dispatchLayerEvent('beforeCreate', [
            'id' => $formId,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }
        // <<<

        // 第１引数の $model が $context に変わった
        // >>>
        $out = parent::create($context, $options);
        // <<<

        // CUSTOMIZE ADD 2014/07/03 ryuring
        // >>>
        // EVENT Form.afterCreate
        $event = $this->dispatchLayerEvent('afterCreate', [
            'id' => $formId,
            'out' => $out
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
        // <<<

    }

    /**
     * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden
     * input fields where appropriate.
     *
     * Resets some parts of the state, shared among multiple FormHelper::create() calls, to defaults.
     *
     * @param array $secureAttributes Secure attributes which will be passed as HTML attributes
     *   into the hidden input elements generated for the Security Component.
     * @return string A closing FORM tag.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#closing-the-form
     * @checked
     * @noTodo
     * @unitTest
     */
    public function end(array $secureAttributes = []): string
    {
        $formId = $this->getId();
        $this->setId(null);

        // EVENT Form.beforeEnd
        $event = $this->dispatchLayerEvent('beforeEnd', [
            'id' => $formId,
            'secureAttributes' => $secureAttributes
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $secureAttributes = ($event->getResult() === null || $event->getResult() === true)? $event->getData('secureAttributes') : $event->getResult();
        }

        $out = parent::end($secureAttributes);

        // EVENT Form.afterEnd
        $event = $this->dispatchLayerEvent('afterEnd', [
            'id' => $formId,
            'out' => $out
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
    }

    /**
     * Creates a hidden input field.
     *
     * @param string $fieldName Name of a field, in the form of "Modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @return string A generated hidden input
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::hidden
     * @checked
     * @noTodo
     */
    public function hidden($fieldName, $options = []): string
    {
        $options += ['required' => false, 'secure' => true];

        $secure = $options['secure'];
        unset($options['secure']);

        // CUSTOMIZE ADD 2010/07/24 ryuring
        // セキュリティコンポーネントのトークン生成の仕様として、
        // ・hiddenタグ以外はフィールド情報のみ
        // ・hiddenタグはフィールド情報と値
        // をキーとして生成するようになっている。
        // その場合、生成の元のなる値は、multipleを想定されておらず、先頭の値のみとなるが
        // multiple な hiddenタグの場合、送信される値は配列で送信されるので値違いで認証がとおらない。
        // という事で、multiple の場合は、あくまでhiddenタグ以外のようにフィールド情報のみを
        // トークンのキーとする事で認証を通すようにする。
        // CUSTOMIZE ADD 2022/12/21 by ryuring
        // CakePHP4になり、動作しなくなったため、unlockField を追加
        // >>>
        if (!empty($options['multiple'])) {
            $secure = false;
            $this->unlockField($fieldName);
        }
        // <<<

        $options = $this->_initInputField($fieldName, array_merge(
            $options,
            ['secure' => static::SECURE_SKIP]
        ));

        if ($secure === true && $this->formProtector) {
            $this->formProtector->addField(
                $options['name'],
                true,
                $options['val'] === false? '0' : (string)$options['val']
            );
        }

        $options['type'] = 'hidden';

        // CUSTOMIZE ADD 2010/07/24 ryuring
        // 配列用のhiddenタグを出力できるオプションを追加
        // CUSTOMIZE 2010/08/01 ryuring
        // class属性を指定できるようにした
        // CUSTOMIZE 2011/03/11 ryuring
        // multiple で送信する値が配列の添字となっていたので配列の値に変更した
        // >>>
        $multiple = false;
        $value = '';
        if (!empty($options['multiple'])) {
            $multiple = true;
            $options['id'] = null;
            if (!isset($options['val'])) {
                $value = $this->getSourceValue($fieldName);
            } else {
                $value = $options['val'];
            }
            if (is_array($value) && !$value) {
                unset($options['val']);
            }
            unset($options['multiple']);
        }
        // <<<

        // CUSTOMIZE MODIFY 2010/07/24 ryuring
        // >>>
        // return $this->widget('hidden', $options);
        // ---
        if ($multiple && is_array($value)) {
            $out = [];
            $options['name'] = $options['name'] . '[]';
            foreach($value as $v) {
                $options['val'] = $v;
                $out[] = $this->widget('hidden', $options);;
            }
            return implode("\n", $out);
        } else {
            return $this->widget('hidden', $options);
        }
        // <<<
    }

    /**
     * Creates a submit button element. This method will generate `<input />` elements that
     * can be used to submit, and reset forms by using $options. image submits can be created by supplying an
     * image path for $caption.
     *
     * ### Options
     *
     * - `div` - Include a wrapping div?  Defaults to true. Accepts sub options similar to
     *   FormHelper::input().
     * - `before` - Content to include before the input.
     * - `after` - Content to include after the input.
     * - `type` - Set to 'reset' for reset inputs. Defaults to 'submit'
     * - `confirm` - JavaScript confirmation message.
     * - Other attributes will be assigned to the input element.
     *
     * ### Options
     *
     * - `div` - Include a wrapping div?  Defaults to true. Accepts sub options similar to
     *   FormHelper::input().
     * - Other attributes will be assigned to the input element.
     *
     * @param string $caption The label appearing on the button OR if string contains :// or the
     *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
     *  exists, AND the first character is /, image is relative to webroot,
     *  OR if the first character is not /, image is relative to webroot/img.
     * @param array $options Array of options. See above.
     * @return string A HTML submit button
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::submit
     * @checked
     * @noTodo
     * @unitTest
     */
    public function submit(string $caption = null, array $options = []): string
    {
        // CUSTOMIZE ADD 2016/06/08 ryuring
        // >>>
        // EVENT Form.beforeSubmit
        $event = $this->dispatchLayerEvent('beforeSubmit', [
            'id' => $this->getId(),
            'caption' => $caption,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        $output = parent::submit($caption, $options);

        // EVENT Form.afterSubmit
        $event = $this->dispatchLayerEvent('afterSubmit', [
            'id' => $this->getId(),
            'caption' => $caption,
            'out' => $output
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
        // <<<

    }

// CUSTOMIZE ADD 2014/07/02 ryuring

    /**
     * フォームのIDを作成する
     * BcForm::create より呼出される事が前提
     *
     * @param EntityInterface $context
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function createId($context, $options = [])
    {
        $request = $this->getView()->getRequest();
        if (!isset($options['id'])) {
            if (!empty($context)) {
                if (is_array($context)) {
                    // 複数$contextに設定されてる場合先頭のエンティティを優先
                    $context = array_shift($context);
                }
                if ($context instanceof EntityInterface) {
                    [, $context] = pluginSplit($context->getSource());
                } else {
                    $context = null;
                }
            }
            if (!$context) {
                $context = empty($request->getParam('controller'))? false : $request->getParam('controller');
            }
            if ($domId = isset($options['url']['action'])? $options['url']['action'] : $request->getParam('action')) {
                $formId = Inflector::classify($context) . $request->getParam('prefix') . Inflector::camelize($domId) . 'Form';
            } else {
                $formId = null;
            }
        } else {
            $formId = $options['id'];
        }
        return $formId;
    }

    /**
     * フォームのIDを取得する
     *
     * BcFormHelper::create() の後に呼び出される事を前提とする
     *
     * @return string フォームID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getId()
    {
        return $this->formId;
    }

    /**
     * フォームのIDを設定する
     *
     * BcFormHelper::create() の後に呼び出される事を前提とする
     * @param $id フォームID
     * @return string 新規フォームID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setId($id)
    {
        return $this->formId = $id;
    }

    /**
     * CKEditorを出力する
     *
     * @param string $fieldName
     * @param array $options
     * @param array $editorOptions
     * @param array $styles
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function ckeditor($fieldName, $options = [])
    {
        $options = array_merge(['type' => 'textarea'], $options);
        return $this->BcCkeditor->editor($fieldName, $options);
    }

    /**
     * エディタを表示する
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function editor($fieldName, $options = [])
    {
        $options = array_merge([
            'editor' => 'BaserCore.BcCkeditor',
            'style' => 'width:99%;height:540px'
        ], $options);

        if($options['editor'] === 'none') $options['editor'] = '';
        if ($options['editor']) {
            [$plugin] = pluginSplit($options['editor']);
            if (!Plugin::isLoaded($plugin)) {
                $options['editor'] = '';
            }
        }

        if (!$options['editor']) {
            /** @var BcCkeditorHelper $bcCkeditor */
            $bcCkeditor = $this->getView()->BcCkeditor;
            return $bcCkeditor->editor($fieldName, $options);
        }

        $className = $options['editor'];
        [, $editor] = pluginSplit($options['editor']);
        $this->getView()->loadHelper($editor, ['className' => $className]);
        if (isset($this->getView()->helpers()->{$editor})) {
            return $this->getView()->{$editor}->editor($fieldName, $options);
        } elseif ($editor === 'none') {
            $_options = [];
            foreach($options as $key => $value) {
                if (!preg_match('/^editor/', $key)) {
                    $_options[$key] = $value;
                }
            }
            return $this->input($fieldName, array_merge(['type' => 'textarea'], $_options));
        } else {
            /** @var BcCkeditorHelper $bcCkeditor */
            $bcCkeditor = $this->getView()->BcCkeditor;
            return $bcCkeditor->editor($fieldName, $options);
        }
    }

    /**
     * 都道府県用のSELECTタグを表示する
     *
     * @param string $fieldName Name attribute of the SELECT
     * @param mixed $selected Selected option
     * @param array $attributes Array of HTML options for the opening SELECT element
     * @param array $convertKey true value = "value" / false value = "key"
     * @return string 都道府県用のSELECTタグ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function prefTag($fieldName, $selected = null, $attributes = [], $convertKey = false)
    {
        $prefs = $this->BcText->prefList();
        if ($convertKey) {
            $options = [];
            foreach($prefs as $key => $value) {
                if ($key) {
                    $options[$value] = $value;
                } else {
                    $options[$key] = $value;
                }
            }
        } else {
            $options = $prefs;
        }
        $attributes['value'] = $selected;
        $attributes['empty'] = false;
        return $this->select($fieldName, $options, $attributes);
    }

    /**
     * ファイルインプットボックス出力
     *
     * 画像の場合は画像タグ、その他の場合はファイルへのリンク
     * そして削除用のチェックボックスを表示する
     *
     * 《オプション》
     * imgsize    画像のサイズを指定する
     * rel        A タグの rel 属性を指定
     * title    A タグの title 属性を指定
     * link        大きいサイズへの画像へのリンク有無
     * delCheck    削除用チェックボックスの利用可否
     * force    ファイルの存在有無に関わらず強制的に画像タグを表示するかどうか
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function file($fieldName, $options = []): string
    {
        $options = $this->_initInputField($fieldName, $options);

        $table = $this->getTable($fieldName);
        if (!$table || !$table->hasBehavior('BcUpload')) {
            return parent::file($fieldName, $options);
        }

        $options = array_merge([
            'imgsize' => 'medium', // 画像サイズ
            'rel' => '', // rel属性
            'title' => '', // タイトル属性
            'link' => true, // 大きいサイズの画像へのリンク有無
            'delCheck' => true,
            'force' => false,
            'width' => '',
            'height' => '',
            'class' => '',
            'div' => false,
            'deleteSpan' => [],
            'deleteCheckbox' => [],
            'deleteLabel' => [],
            'figure' => [],
            'img' => ['class' => ''],
            'figcaption' => [],
            'table' => null
        ], $options);

        $linkOptions = [
            'imgsize' => $options['imgsize'],
            'rel' => $options['rel'],
            'title' => $options['title'],
            'link' => $options['link'],
            'delCheck' => $options['delCheck'],
            'force' => $options['force'],
            'width' => $options['width'],
            'height' => $options['height'],
            'figure' => $options['figure'],
            'img' => $options['img'],
            'figcaption' => $options['figcaption'],
            'table' => $options['table']
        ];

        $deleteSpanOptions = $deleteCheckboxOptions = $deleteLabelOptions = [];
        if (!empty($options['deleteSpan'])) {
            $deleteSpanOptions = $options['deleteSpan'];
        }
        if (!empty($options['deleteCheckbox'])) {
            $deleteCheckboxOptions = $options['deleteCheckbox'];
        }
        if (!empty($options['deleteLabel'])) {
            $deleteLabelOptions = $options['deleteLabel'];
        }
        if (!empty($options['div'])) {
            $divOptions = $options['div'];
        }
        if (empty($options['class'])) {
            unset($options['class']);
        }
        unset($options['imgsize'], $options['rel'], $options['title'], $options['link']);
        unset($options['delCheck'], $options['force'], $options['width'], $options['height']);
        unset($options['deleteSpan'], $options['deleteCheckbox'], $options['deleteLabel']);
        unset($options['figure'], $options['img'], $options['figcaption'], $options['div'], $options['table']);

        $fileLinkTag = $this->BcUpload->fileLink($fieldName, $this->_getContext()->entity(), $linkOptions);
        $fileTag = parent::file($fieldName, $options);

        if (empty($options['value'])) {
            $value = $this->getSourceValue($fieldName);
        } else {
            $value = $options['value'];
        }

        // PHP5.3対応のため、is_string($value) 判別を実行
        $delCheckTag = '';
        if ($fileLinkTag && $linkOptions['delCheck'] && (is_string($value) ||
                (is_array($value) && empty($value['session_key'])) ||
                (is_object($value) && $value->getError() == UPLOAD_ERR_NO_FILE))) {
            $delCheckTag = $this->Html->tag('span', $this->checkbox($fieldName . '_delete', $deleteCheckboxOptions) . $this->label($fieldName . '_delete', __d('baser_core', '削除する'), $deleteLabelOptions), $deleteSpanOptions);
        }
        $hiddenValue = $this->getSourceValue($fieldName . '_');
        $fileValue = $this->getSourceValue($fieldName);

        $hiddenTag = '';
        if ($fileLinkTag) {
            if (is_object($fileValue) && empty($fileValue->getClientFileName()) && $hiddenValue) {
                $hiddenTag = $this->hidden($fieldName . '_', ['value' => $hiddenValue]);
            } else {
                if (is_array($fileValue) || is_object($fileValue)) {
                    $fileValue = null;
                }
                $hiddenTag = $this->hidden($fieldName . '_', ['value' => $fileValue]);
            }
        }

        $out = $fileTag;

        if ($fileLinkTag) {
            $out .= '&nbsp;' . $delCheckTag . $hiddenTag . '<br />' . $fileLinkTag;
        }

        if (isset($divOptions)) {
            if ($divOptions === false) {
                return $out;
            } elseif (is_array($divOptions)) {
                $tag = 'div';
                if (!empty($divOptions['tag'])) {
                    $tag = $divOptions['tag'];
                }
                if (!empty($divOptions['class'])) {
                    $divOptions['class'] .= ' upload-file';
                } else {
                    $divOptions['class'] = 'upload-file';
                }
                unset($divOptions['tag'], $divOptions['errorClass']);
                return $this->Html->tag($tag, $out, $divOptions);
            } else {
                return $this->Html->div($options['class'], $out);
            }
        } else {
            return $out;
        }
    }

    /**
     * フィールドに紐づくテーブルを取得する
     * @param string $fieldName
     * @return \Cake\ORM\Table|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTable($fieldName)
    {
        $context = $this->context();
        if (!($context instanceof EntityContext)) return false;
        $entity = $context->entity(explode('.', $fieldName));
        if (!$entity) return false;

        $fieldArray = explode('.', $fieldName);

        if (count($fieldArray) === 3) {
            if ($entity && $entity->get($fieldArray[1])) {
                $entity = $entity->get($fieldArray[1]);
            }
        } elseif (!in_array(count($fieldArray), [1, 2])) {
            return false;
        }

        $alias = $entity->getSource();
        $plugin = '';
        if (strpos($alias, '.')) {
            [$plugin, $name] = pluginSplit($alias);
        }
        $name = Inflector::camelize(Inflector::tableize($name));
        if ($plugin) $name = $plugin . '.' . $name;
        return TableRegistry::getTableLocator()->get($name);
    }

    /**
     * フォームコントロールを取得
     *
     * CakePHPの標準仕様をカスタマイズ
     * - labelタグを自動で付けない
     * - legendタグを自動で付けない
     * - errorタグを自動で付けない
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function control(string $fieldName, array $options = []): string
    {
        $options = array_replace_recursive([
            'label' => false,
            'legend' => false,
            'error' => false,
            'counter' => false,
            'templateVars' => ['tag' => 'span', 'groupTag' => 'span']
        ], $options);

        if ($options['counter']) {
            if (!empty($options['class'])) {
                $options['class'] .= ' bca-text-counter';
            } else {
                $options['class'] = 'bca-text-counter';
            }
            unset($options['counter']);
        }

        // EVENT Form.beforeControl
        $event = $this->dispatchLayerEvent('beforeControl', [
            'formId' => $this->__id,
            'data' => $this->getView()->getRequest()->getData(),
            'fieldName' => $fieldName,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        $output = parent::control($fieldName, $options);

        // EVENT Form.afterControl
        $event = $this->dispatchLayerEvent('afterControl', [
            'formId' => $this->__id,
            'data' => $this->getView()->getRequest()->getData(),
            'fieldName' => $fieldName,
            'out' => $output
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $output;
    }
// <<<

}
