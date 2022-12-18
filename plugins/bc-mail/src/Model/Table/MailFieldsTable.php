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

namespace BcMail\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use Cake\Datasource\EntityInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドモデル
 *
 * @package Mail.Model
 *
 */
class MailFieldsTable extends MailAppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mail_fields');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('MailContents', [
            'className' => 'BcMail.MailContents',
            'foreignKey' => 'mail_content_id',
        ]);
    }

    /**
     * MailField constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->notEmptyString('name', __d('baser', '項目名を入力してください。'))
            ->maxLength('name', 255, __d('baser', '項目名は255文字以内で入力してください。'));
        $validator
            ->scalar('field_name')
            ->notEmptyString('field_name', __d('baser', 'フィールド名を入力してください。'))
            ->maxLength('field_name', 255, __d('baser', 'フィールド名は255文字以内で入力してください。'));
        // TODO ucmitz 未実装
//            ->add('field_name', [
//                'halfTextMailField' => [
//                    'rule' => 'halfTextMailField',
//                    'provider' => 'table',
//                    'message' => __d('baser', 'フィールド名は半角英数字のみで入力してください。')
//                ]])
//            ->add('field_name', [
//                'duplicateMailField' => [
//                    'rule' => 'duplicateMailField',
//                    'provider' => 'table',
//                    'message' => __d('baser', '既に登録のあるフィールド名です。')
//                ]]);
        $validator
            ->scalar('type')
            ->notEmptyString('type', __d('baser', 'タイプを入力してください。'));
        $validator
            ->scalar('head')
            ->maxLength('head', 255, __d('baser', '項目見出しは255文字以内で入力してください。'));
        $validator
            ->scalar('attention')
            ->maxLength('attention', 255, __d('baser', '注意書きは255文字以内で入力してください。'));
        $validator
            ->scalar('before_attachment')
            ->maxLength('before_attachment', 255, __d('baser', '前見出しは255文字以内で入力してください。'));
        $validator
            ->scalar('after_attachment')
            ->maxLength('after_attachment', 255, __d('baser', '後見出しは255文字以内で入力してください。'));
        // TODO ucmitz 未実装
//        $validator
//            ->scalar('source')
//            ->add('source', [
//                'sourceMailField' => [
//                    'rule' => 'sourceMailField',
//                    'provider' => 'table',
//                    'message' => __d('baser', '選択リストを入力してください。')
//                ]]);
        $validator
            ->scalar('options')
            ->maxLength('options', 255, __d('baser', 'オプションは255文字以内で入力してください。'));
        $validator
            ->scalar('class')
            ->maxLength('class', 255, __d('baser', 'クラス名255文字以内で入力してください。'));
        $validator
            ->scalar('default_value')
            ->maxLength('default_value', 255, __d('baser', '初期値は255文字以内で入力してください。'));
        $validator
            ->scalar('description')
            ->maxLength('options', 255, __d('baser', '説明文は255文字以内で入力してください。'));
        $validator
            ->scalar('group_field')
            ->maxLength('group_field', 255, __d('baser', 'グループフィールドは255文字以内で入力してください。'));
        $validator
            ->scalar('group_valid')
            ->maxLength('group_valid', 255, __d('baser', 'グループ入力チェックは255文字以内で入力してください。'));
        return $validator;
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @return array source
     */
    public function getControlSource($field = null)
    {
        $source['type'] = [
            'text' => __d('baser', 'テキスト'),
            'textarea' => __d('baser', 'テキストエリア'),
            'radio' => __d('baser', 'ラジオボタン'),
            'select' => __d('baser', 'セレクトボックス'),
            'email' => __d('baser', 'Eメール'),
            'multi_check' => __d('baser', 'マルチチェックボックス'),
            'file' => __d('baser', 'ファイル'),
            'autozip' => __d('baser', '自動補完郵便番号'),
            'pref' => __d('baser', '都道府県リスト'),
            'date_time_wareki' => __d('baser', '和暦日付'),
            'date_time_calender' => __d('baser', 'カレンダー'),
            'tel' => __d('baser', '電話番号'),
            'password' => __d('baser', 'パスワード'),
            'hidden' => __d('baser', '隠しフィールド')
        ];
        $source['valid'] = [
            'VALID_NOT_EMPTY' => __d('baser', '入力必須'),
            'VALID_EMAIL' => __d('baser', 'Eメールチェック（入力必須）'),
            '/^(|[0-9]+)$/' => __d('baser', '数値チェック'),
            '/^([0-9]+)$/' => __d('baser', '数値チェック（入力必須）')
        ];
        $source['valid_ex'] = [
            'VALID_EMAIL_CONFIRM' => __d('baser', 'Eメール比較チェック'),
            'VALID_GROUP_COMPLATE' => __d('baser', 'グループチェック'),
            'VALID_NOT_UNCHECKED' => __d('baser', 'チェックボックス未入力チェック'),
            'VALID_DATETIME' => __d('baser', '日付チェック'),
            'VALID_MAX_FILE_SIZE' => __d('baser', 'ファイルアップロードサイズ制限'),
            'VALID_FILE_EXT' => __d('baser', 'ファイル拡張子チェック'),
            'VALID_ZENKAKU_KATAKANA' => __d('baser', '全角カタカナチェック'),
            'VALID_ZENKAKU_HIRAGANA' => __d('baser', '全角ひらがなチェック'),
            'VALID_REGEX' => __d('baser', '正規表現チェック'),
        ];
        $source['auto_convert'] = ['CONVERT_HANKAKU' => __d('baser', '半角変換')];
        if (!$field) {
            return $source;
        }

        return $source[$field];
    }

    /**
     * 同じ名称のフィールド名がないかチェックする
     * 同じメールコンテンツが条件
     *
     * @param array $check
     * @return boolean
     */
    public function duplicateMailField($check)
    {
        $conditions = [
            'MailField.' . key($check) => $check[key($check)],
            'MailField.mail_content_id' => $this->data['MailField']['mail_content_id']
        ];
        if ($this->exists()) {
            $conditions['NOT'] = ['MailField.id' => $this->id];
        }
        $ret = $this->find('first', ['conditions' => $conditions]);
        if ($ret) {
            return false;
        }

        return true;
    }

    /**
     * メールフィールドの値として正しい文字列か検証する
     * 半角英数-_
     *
     * @param array $check
     * @return boolean
     */
    public function halfTextMailField($check)
    {
        $subject = $check[key($check)];
        $pattern = "/^[a-zA-Z0-9-_]*$/";
        return !!(preg_match($pattern, $subject) === 1);
    }

    /**
     * 選択リストの入力チェック
     *
     * @param integer $check
     */
    public function sourceMailField($check)
    {
        switch($this->data['MailField']['type']) {
            case 'radio':        // ラジオボタン
            case 'select':        // セレクトボックス
            case 'multi_check':    // マルチチェックボックス
            case 'autozip':        // 自動保管郵便番号
                // 選択リストのチェックを行う
                return (!empty($check[key($check)]));
        }
        // 選択リストが不要のタイプの時はチェックしない
        return true;
    }

    /**
     * フィールドデータをコピーする
     *
     * @param int $id
     * @param array $data
     * @param array $options
     * @return EntityInterface|false
     * @checked
     * @noTodo
     */
    public function copy($id, $data = null, $options = [])
    {
        $options = array_merge([
            'sortUpdateOff' => false,
        ], $options);

        if ($id) $data = $this->get($id);
        $oldData = clone $data;

        if($this->find()->where(['MailFields.mail_content_id' => $data->mail_content_id, 'MailFields.field_name' => $data->field_name])->count()) {
            $data->name .= '_copy';
            if (strlen($data->name) >= 64) return false;
            $data->field_name .= '_copy';
            return $this->copy(null, $data, $options); // 再帰処理
        }

        if (!$options['sortUpdateOff']) {
            // EVENT MailFields.beforeCopy
            $event = $this->dispatchLayerEvent('beforeCopy', [
                'data' => $data,
                'id' => $id,
            ]);
            if ($event !== false) {
                $data = $event->getResult() === true? $event->getData('data') : $event->getResult();
            }
        }

        $data->no = $this->getMax('no', ['MailFields.mail_content_id' => $data->mail_content_id]) + 1;
        if (!$options['sortUpdateOff']) {
            $data->sort = $this->getMax('sort') + 1;
        }
        $data->use_field = false;
        $data->id = null;
        $data->modified = null;
        $data->created = null;

        $result = $this->save($this->patchEntity($this->newEmptyEntity(), $data->toArray()));
        if (!$result) return false;

        // EVENT MailFields.afterCopy
        if (!$options['sortUpdateOff']) {
            $this->dispatchLayerEvent('afterCopy', [
                'id' => $data->id,
                'data' => $result,
                'oldId' => $id,
                'oldData' => $oldData,
            ]);
        }
        return $result;
    }

    /**
     * 選択リストのソースを整形する
     * 空白と \r を除外し、改行で結合する
     * | の対応は後方互換として残しておく
     * @param string $source 選択リストソース
     * @return string 整形後選択リストソース
     */
    public function formatSource($source)
    {
        $source = str_replace('|', "\n", $source);
        $values = explode("\n", $source);
        $sourceList = [];
        foreach($values as $value) {
            $sourceList[] = preg_replace("/(^\s+|\r|\n\s+$)/u", '', $value);
        }
        return implode("\n", $sourceList);
    }

    /**
     * After Delete
     */
    public function afterDelete()
    {
        // TODO ucmitz 未実装
//        parent::afterDelete();
//        // フロントエンドでは、MailContentのキャッシュを利用する為削除しておく
//        $MailContent = ClassRegistry::init('BcMail.MailContent');
//        $MailContent->delCache();
    }

    /**
     * After Save
     *
     * @param bool $created
     * @param array $options
     */
    public function afterSave($created, $options = [])
    {
        // TODO ucmitz 未実装
//        parent::afterSave($created, $options);
//        // フロントエンドでは、MailContentのキャッシュを利用する為削除しておく
//        $MailContent = ClassRegistry::init('BcMail.MailContent');
//        $MailContent->delCache();
    }
}
