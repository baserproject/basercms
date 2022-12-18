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

use BaserCore\Error\BcException;
use BcMail\Model\Entity\MailMessage;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;

/**
 * メッセージモデル
 *
 * @package Mail.Model
 *
 */
class MailMessagesTable extends MailAppTable
{

    /**
     * メールフォーム情報
     *
     * @var array
     */
    public $mailFields = [];

    /**
     * メールコンテンツ情報
     *
     * @var array
     */
    public $mailContent = [];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('mail_messages');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BaserCore.BcUpload', [
            'subdirDateFormat' => 'Y/m/'
        ]);
    }

    /**
     * モデルのセットアップを行う
     *
     * MailMessageモデルは利用前にこのメソッドを呼び出しておく必要あり
     *
     * @param int $mailContentId
     * @return boolean
     */
    public function setup($mailContentId, $postData = [])
    {
        // テーブル名の設定
        $this->setUseTable($mailContentId);
        // アップロード設定
        $this->setupUpload($mailContentId);
        // バリデーションの設定
        $this->setupValidate($mailContentId, $postData);
        // スキーマの初期化
        $this->_schema = null;
        return true;
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $this->getValidator('MailMessages');
    }

    /**
     * テーブル名を設定する
     *
     * @param $mailContentId
     * @checked
     * @noTodo
     */
    public function setUseTable($mailContentId)
    {
        $this->setTable($this->createTableName($mailContentId));
    }

    /**
     * テーブル名を生成する
     * int型でなかったら強制終了
     * @param $mailContentId
     * @return string
     * @checked
     * @noTodo
     */
    public function createTableName($mailContentId)
    {
        $mailContentId = (int)$mailContentId;
        if (!is_int($mailContentId)) {
            throw new BcException(__d('baser', 'MailMessageService::createTableName() の引数 $mailContentId は int 型しか受けつけていません。'));
        }
        return 'mail_message_' . $mailContentId;
    }

    /**
     * アップロード設定を行う
     * @checked
     * @noTodo
     */
    public function setupUpload(int $mailContentId)
    {
        $mailFieldsTable = TableRegistry::getTableLocator()->get('BcMail.MailFields');
        $mailFields = $mailFieldsTable->find()->where([
            'MailFields.mail_content_id' => $mailContentId,
            'MailFields.use_field' => true
        ])->all();
        $settings = $this->getFileUploader()->getSettings();
        $settings['fields'] = [];
        foreach($mailFields as $mailField) {
            if ($mailField->type === 'file') {
                $settings['fields'][$mailField->field_name] = [
                    'type' => 'all',
                    'namefield' => 'id',
                    'nameformat' => '%08d'
                ];
            }
        }
        if (empty($settings['saveDir']) || !preg_match('/^' . preg_quote("mail" . DS . $mailContentId, '/') . '\//', $settings['saveDir'])) {
            $settings['saveDir'] = "mail" . DS . "limited" . DS . $mailContentId . DS . "messages";
        }
        $this->getBehavior('BcUpload')->setSettings($settings);
    }

    /**
     * Called after data has been checked for errors
     *
     * @return void
     */
    public function afterValidate()
    {
        $data = $this->data;

        // Eメール確認チェック
        $this->_validEmailCofirm($data);
        // 不完全データチェック
        $this->_validGroupComplate($data);
        // 拡張バリデートチェック
        $this->_validExtends($data);
        // バリデートグループエラーチェック
        $this->_validGroupErrorCheck();
        // 和暦不完全データチェック
        $this->_validWarekiComplate($data);
    }

    /**
     * validate（入力チェック）を個別に設定する
     * VALID_NOT_EMPTY    空不可
     * VALID_EMAIL        メール形式チェック
     *
     * @return void
     */
    protected function setupValidate(int $mailContentId, array $postData)
    {
        $mailFieldsTable = TableRegistry::getTableLocator()->get('BcMail.MailFields');
        $mailFields = $mailFieldsTable->find()->where([
            'MailFields.mail_content_id' => $mailContentId,
            'MailFields.use_field' => true
        ])->all();

        $validator = new $this->_validatorClass();

        foreach($mailFields as $mailField) {
            if ($mailField->valid && !empty($mailField->use_field)) {
                if ($mailField->valid === 'VALID_NOT_EMPTY' || $mailField->valid === 'VALID_EMAIL') {
                    // 必須項目
                    if ($mailField->type === 'file') {
                        if (!isset($postData[$mailField->field_name . '_tmp'])) {
                            $validator->requirePresence($mailField->field_name)
                                ->add($mailField->field_name, [
                                'notFileEmpty' => [
                                    'rule' => 'notFileEmpty',
                                    'message' => __('必須項目です。')
                                ]
                            ]);
                        }
                    } else {
                        $validator->requirePresence($mailField->field_name)
                            ->notEmpty($mailField->field_name, __('必須項目です。'));
                    }
                } elseif ($mailField->valid === '/^(|[0-9]+)$/') {
                    // 半角数字
                    $validator->allowEmpty($mailField->field_nam)
                        ->add($mailField->field_name, [
                            'alphaNumeric' => [
                                'rule' => 'alphaNumeric',
                                'message' => __('半角数字で入力してください。')
                            ]
                    ]);
                } elseif ($mailField->valid === '/^([0-9]+)$/') {
                    // 半角数字（入力必須）
                    $validator->notEmpty($mailField->field_nam)
                        ->add($mailField->field_name, [
                            'alphaNumeric' => [
                                'rule' => 'alphaNumeric',
                                'message' => __('半角数字で入力してください。')
                            ]
                    ]);
                } else {
                    $validator->allowEmpty($mailField->field_nam)
                        ->add($mailField->field_name, [
                            'custom' => [
                                'rule' => $mailField->valid,
                                'message' => __('エラーが発生しました。')
                            ]
                    ]);
                }
                if (!empty($postData[$mailField->field_name]) && $mailField->valid == 'VALID_EMAIL') {
                    $validator->email($mailField->field_nam, __('形式が無効です。'))
                        ->add($mailField->field_name, [
                            'english' => [
                                'rule' => '/^[a-zA-Z0-9!#$%&\’*+-\/=?^_`{|}~@.]*$/',
                                'message' => __('半角で入力してください。')
                            ]
                    ]);
                }
            }
            // ### 拡張バリデーション
            if ($mailField->valid_ex && !empty($mailField->use_field)) {
                $valids = explode(',', $mailField->valid_ex);
                foreach($valids as $valid) {
                    $options = preg_split('/(?<!\\\)\|/', $mailField->options);
                    $options = call_user_func_array('aa', $options);
                    switch($valid) {
                        case 'VALID_MAX_FILE_SIZE':
                            if (
                                !empty($options['maxFileSize']) &&
                                (isset($postData[$mailField->field_name]['error']) &&
                                    $postData[$mailField->field_name]['error'] !== UPLOAD_ERR_NO_FILE)
                            ) {
                                $validator->add($mailField->field_name, [
                                        'fileCheck' => [
                                            'rule' => ['fileCheck', $this->convertSize($options['maxFileSize'], 'B', 'M')],
                                            'message' => __('ファイルのアップロードに失敗しました。')
                                        ]
                                ]);
                                // TODO ucmitz 未検証
                                // 必須入力としている場合、必須エラーが優先され、ファイルサイズオーバーのエラーメッセージとならないため、バリデーションエラーの優先度を入れ替える
                                //$this->validate[$mailField->field_name] = array_reverse($this->validate[$mailField->field_name]);
                            }
                            break;
                        case 'VALID_FILE_EXT':
                            if (!empty($options['fileExt'])) {
                                $validator->add($mailField->field_name, [
                                    'fileExt' => [
                                        'rule' => ['fileExt', $options['fileExt']],
                                        'message' => __('ファイル形式が無効です。')
                                    ]
                                ]);
                            }
                            break;
                        case 'VALID_REGEX':
                            if (!empty($options['regex'])) {
                                $options['regex'] = str_replace('\|', '|', $options['regex']);
                                $options['regex'] = str_replace("\0", '', $options['regex']); // ヌルバイト除去
                                $validator->allowEmpty
                                    ->add($mailField->field_name, [
                                        'fileExt' => [
                                            'rule' => '/\A' . $options['regex'] . '\z/us',
                                            'message' => __('形式が無効です。')
                                        ]
                                ]);
                            }
                            break;
                    }
                }
            }
        }
        $this->setValidator('MailMessages', $validator);
    }

    /**
     * 拡張バリデートチェック
     *
     * @param array $data
     * @return void
     */
    protected function _validExtends($data)
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $row) {
            $mailField = $row['MailField'];
            if (empty($mailField['use_field'])) {
                continue;
            }

            $valids = explode(',', $mailField['valid_ex']);
            $field_name = $mailField['field_name'];
            // マルチチェックボックスのチェックなしチェック
            if (in_array('VALID_NOT_UNCHECKED', $valids)) {
                if (empty($data['MailMessage'][$field_name])) {
                    $this->invalidate($field_name, __('必須項目です。'));
                }
                $dists[$field_name][] = @$data['MailMessage'][$field_name];
                // datetimeの空チェック
                continue;
            }

            if (in_array('VALID_DATETIME', $valids)) {
                if (is_array($data['MailMessage'][$field_name])) {
                    if (
                        empty($data['MailMessage'][$field_name]['year']) ||
                        empty($data['MailMessage'][$field_name]['month']) ||
                        empty($data['MailMessage'][$field_name]['day'])
                    ) {
                        $this->invalidate($field_name, __('日付の形式が無効です。'));
                    }
                }
                if (is_string($data['MailMessage'][$field_name])) {
                    // カレンダー入力利用時は yyyy/mm/dd で入ってくる
                    // yyyy/mm/dd 以外の文字列入力も可能であり、そうした際は日付データとして 1970-01-01 となるため認めない
                    $inputValue = date('Y-m-d', strtotime($data['MailMessage'][$field_name]));
                    if ($inputValue === '1970-01-01') {
                        $this->invalidate($field_name, __('日付の形式が無効です。'));
                    }
                    if (!$this->checkDate([$field_name => $inputValue])) {
                        $this->invalidate($field_name, __('日付の形式が無効です。'));
                    }
                }
                continue;
            }

            if (in_array('VALID_ZENKAKU_KATAKANA', $valids)) {
                if (!preg_match('/^(|[ァ-ヾ 　]+)$/u', $data['MailMessage'][$field_name])) {
                    $this->invalidate($field_name, __('全て全角カタカナで入力してください。'));
                }
                continue;
            }

            if (in_array('VALID_ZENKAKU_HIRAGANA', $valids)) {
                if (!preg_match('/^([　 \t\r\n]|[ぁ-ん]|[ー])+$/u', $data['MailMessage'][$field_name])) {
                    $this->invalidate($field_name, __('全て全角ひらがなで入力してください。'));
                }
            }
        }
    }

    /**
     * バリデートグループエラーチェック
     *
     * @return void
     */
    protected function _validGroupErrorCheck()
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $mailField) {
            $mailField = $mailField['MailField'];
            // 対象フィールドがあれば、バリデートグループごとに配列にフィールド名を格納する
            if (!empty($mailField['use_field']) && $mailField['group_valid']) {
                $dists[$mailField['group_valid']][] = $mailField['field_name'];
            }
        }

        // エラーが発生しているかチェック
        foreach($dists as $key => $dist) {
            foreach($dist as $data) {
                if (isset($this->validationErrors[$data]) && isset($this->validate[$data])) {
                    $this->invalidate($key);
                }
            }
        }
    }

    /**
     * 不完全データチェック
     *
     * @param array $data
     * @return void
     */
    protected function _validGroupComplate($data)
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $mailField) {
            $mailField = $mailField['MailField'];
            // 対象フィールドがあれば、バリデートグループごとに配列に格納する
            $valids = explode(',', $mailField['valid_ex']);
            if (in_array('VALID_GROUP_COMPLATE', $valids) && !empty($mailField['use_field'])) {
                $dists[$mailField['group_valid']][] = [
                    'name' => $mailField['field_name'],
                    'value' => @$data['MailMessage'][$mailField['field_name']]
                ];
            }
        }
        // チェック
        // バリデートグループにおけるデータの埋まり具合をチェックし、全て埋まっていない場合、全て埋まっている場合以外は
        // 不完全データとみなしエラーとする
        foreach($dists as $key => $dist) {
            $i = 0;
            foreach($dist as $data) {
                if (!empty($data['value'])) {
                    $i++;
                }
            }
            $count = count($dist);
            if ($i > 0 && $i < $count) {
                $this->invalidate($key . '_not_complate', __('入力データが不完全です。'));
                foreach($dist as $jValue) {
                    $this->invalidate($jValue['name']);
                }
            }
        }
    }

    /**
     * Eメール確認チェック
     *
     * @param array $data
     * @return void
     */
    protected function _validEmailCofirm($data)
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $mailField) {
            $mailField = $mailField['MailField'];
            if (empty($mailField['use_field'])) {
                continue;
            }
            $valids = explode(',', $mailField['valid_ex']);
            // 対象フィールドがあれば、バリデートグループごとに配列に格納する
            if (in_array('VALID_EMAIL_CONFIRM', $valids)) {
                $dists[$mailField['group_valid']][] = [
                    'name' => $mailField['field_name'],
                    'value' => @$data['MailMessage'][$mailField['field_name']],
                    'isGroupValidComplate' => in_array('VALID_GROUP_COMPLATE', explode(',', $mailField['valid_ex']))
                ];
            }
        }
        // チェック
        // バリデートグループにおけるデータ２つを比較し、違えばエラーとする
        foreach($dists as $key => $dist) {
            if (count($dist) < 2) {
                continue;
            }
            if (count($dist) == 2) {
                if ($dist[0]['value'] !== $dist[1]['value']) {
                    $this->invalidate($key . '_not_same', __('入力データが一致していません。'));
                    if ($dist[0]['isGroupValidComplate']) {
                        $this->invalidate($dist[0]['name']);
                    }
                    if ($dist[1]['isGroupValidComplate']) {
                        $this->invalidate($dist[1]['name']);
                    }
                }
            }
        }
    }

    /**
     * 和暦不完全データチェック
     *
     * @param array $data
     * @return void
     */
    protected function _validWarekiComplate($data)
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $mailField) {
            $mailField = $mailField['MailField'];
            if ($mailField['type'] !== 'date_time_wareki') {
                continue;
            }
            $dists[] = [
                'name' => $mailField['field_name'],
                'value' => $data['MailMessage'][$mailField['field_name']]
            ];
        }

        foreach($dists as $dist) {
            $timeNames = ['year', 'month', 'day'];
            $inputCount = 0;
            foreach($timeNames as $timeName) {
                if (!empty($data['MailMessage'][$dist['name']][$timeName])) {
                    $inputCount++;
                }
            }
            if ($inputCount !== 0 && $inputCount !== count($timeNames)) {
                $this->invalidate($dist['name'] . '', __('入力データが不完全です。'));
            }
        }
    }

    /**
     * データベース用のデータに変換する
     *
     * @param $mailFields
     * @param $mailMessage
     * @return MailMessage
     */
    public function convertToDb($mailFields, $mailMessage)
    {
        foreach($mailFields as $mailField) {
            if (empty($mailMessage->{$mailField->field_name})) continue;
            $value = $mailMessage->{$mailField->field_name};
            // マルチチェックのデータを｜区切りに変換
            if ($mailField->type === 'multi_check' && $mailField->use_field && $value && is_array($value)) {
                $mailMessage->{$mailField->field_name} = implode("|", $value);
            }
            // パスワードのデータをハッシュ化
            // TODO ucmitz 未実装
//            if ($mailField->type === 'password' && !empty($mailMessage->{$mailField->field_name})) {
//                App::uses('AuthComponent', 'Controller/Component');
//                $mailMessage->{$mailField->field_name} = AuthComponent::password($mailMessage->{$mailField->field_name});
//            }
            // 和暦未入力時に配列をnullに変換
            // - 和暦完全入力時は、lib/Baser/Model/BcAppModel->deconstruct にて日時に変換される
            // - 一部のフィールドしか入力されていない場合は $this->_validWarekiComplate にてエラーになる
            if ($mailField->type === 'date_time_wareki' && is_array($value)) {
                $mailMessage->{$mailField->field_name} = null;
            }
            // 機種依存文字を変換
            $mailMessage->{$mailField->field_name} = $this->replaceText($value);
        }
        return $mailMessage;
    }

    /**
     * 機種依存文字の変換処理
     * 内部文字コードがUTF-8である必要がある。
     * 多次元配列には対応していない。
     *
     * @param string $str 変換対象文字列
     * @return string $str 変換後文字列
     * TODO AppExModeに移行すべきかも
     */
    public function replaceText($str)
    {
        $arr = [
            "\xE2\x85\xA0" => "I",
            "\xE2\x85\xA1" => "II",
            "\xE2\x85\xA2" => "III",
            "\xE2\x85\xA3" => "IV",
            "\xE2\x85\xA4" => "V",
            "\xE2\x85\xA5" => "VI",
            "\xE2\x85\xA6" => "VII",
            "\xE2\x85\xA7" => "VIII",
            "\xE2\x85\xA8" => "IX",
            "\xE2\x85\xA9" => "X",
            "\xE2\x85\xB0" => "i",
            "\xE2\x85\xB1" => "ii",
            "\xE2\x85\xB2" => "iii",
            "\xE2\x85\xB3" => "iv",
            "\xE2\x85\xB4" => "v",
            "\xE2\x85\xB5" => "vi",
            "\xE2\x85\xB6" => "vii",
            "\xE2\x85\xB7" => "viii",
            "\xE2\x85\xB8" => "ix",
            "\xE2\x85\xB9" => "x",
            "\xE2\x91\xA0" => "(1)",
            "\xE2\x91\xA1" => "(2)",
            "\xE2\x91\xA2" => "(3)",
            "\xE2\x91\xA3" => "(4)",
            "\xE2\x91\xA4" => "(5)",
            "\xE2\x91\xA5" => "(6)",
            "\xE2\x91\xA6" => "(7)",
            "\xE2\x91\xA7" => "(8)",
            "\xE2\x91\xA8" => "(9)",
            "\xE2\x91\xA9" => "(10)",
            "\xE2\x91\xAA" => "(11)",
            "\xE2\x91\xAB" => "(12)",
            "\xE2\x91\xAC" => "(13)",
            "\xE2\x91\xAD" => "(14)",
            "\xE2\x91\xAE" => "(15)",
            "\xE2\x91\xAF" => "(16)",
            "\xE2\x91\xB0" => "(17)",
            "\xE2\x91\xB1" => "(18)",
            "\xE2\x91\xB2" => "(19)",
            "\xE2\x91\xB3" => "(20)",
            "\xE3\x8A\xA4" => "(上)",
            "\xE3\x8A\xA5" => "(中)",
            "\xE3\x8A\xA6" => "(下)",
            "\xE3\x8A\xA7" => "(左)",
            "\xE3\x8A\xA8" => "(右)",
            "\xE3\x8D\x89" => "ミリ",
            "\xE3\x8D\x8D" => "メートル",
            "\xE3\x8C\x94" => "キロ",
            "\xE3\x8C\x98" => "グラム",
            "\xE3\x8C\xA7" => "トン",
            "\xE3\x8C\xA6" => "ドル",
            "\xE3\x8D\x91" => "リットル",
            "\xE3\x8C\xAB" => "パーセント",
            "\xE3\x8C\xA2" => "センチ",
            "\xE3\x8E\x9D" => "cm",
            "\xE3\x8E\x8F" => "kg",
            "\xE3\x8E\xA1" => "m2",
            "\xE3\x8F\x8D" => "K.K.",
            "\xE2\x84\xA1" => "TEL",
            "\xE2\x84\x96" => "No.",
            "\xE3\x8B\xBF" => "令和",
            "\xE3\x8D\xBB" => "平成",
            "\xE3\x8D\xBC" => "昭和",
            "\xE3\x8D\xBD" => "大正",
            "\xE3\x8D\xBE" => "明治",
            "\xE3\x88\xB1" => "(株)",
            "\xE3\x88\xB2" => "(有)",
            "\xE3\x88\xB9" => "(代)",
        ];

        return str_replace(array_keys($arr), array_values($arr), $str);
    }

    /**
     * メール用に変換する
     *
     * @param array $dbDatas
     * @return array $dbDatas
     * TODO ヘルパー化すべきかも
     */
    public function convertDatasToMail($data, $options)
    {
        $mailFields = $data['mailFields'];
        $message = $data['message'];
        $mailContent = $data['mailContent'];

        foreach($mailFields as $key => $value) {
            $fieldName = $value->field_name;
            $value->before_attachment = strip_tags($value->before_attachment);
            $value->after_attachment = str_replace(["<br />", "<br>"], "\n", strip_tags($value->after_attachment, "<br>"));
            $value->head = str_replace(["<br />", "<br>"], "", strip_tags($value->head, "<br>"));
            if ($value->no_send) {
                unset($message->{$fieldName});
            }
            if ($value->type === 'multi_check') {
                if (!empty($message->{$fieldName}) && !is_array($message->{$fieldName})) {
                    $message->{$fieldName} = explode("|", $message->{$fieldName});
                }
            }
            if (!is_array($message->{$fieldName})) {
                $mailContent->subject_user = str_replace('{$' . $fieldName . '}', $message->{$fieldName}, $mailContent->subject_user);
                $mailContent->subject_admin = str_replace('{$' . $fieldName . '}', $message->{$fieldName}, $mailContent->subject_admin);
            }
            // パスワードは入力値をマスクした値を表示
            if ($value->type === 'password' && $message->{$fieldName} && !empty($options['maskedPasswords'][$fieldName])) {
                $message->{$fieldName} = $options['maskedPasswords'][$fieldName];
            }
        }
        $data['message'] = $message;
        $data['mailContent'] = $mailContent;
        $data['mailFields'] = $mailFields;
        return $data;
    }

    /**
     * フルテーブル名を生成する
     *
     * @param $mailContentId
     * @return string
     */
    public function createFullTableName($mailContentId)
    {
        return $this->tablePrefix . $this->createTableName($mailContentId);
    }

    /**
     * 受信メッセージの内容を表示状態に変換する
     *
     * @param int $id
     * @param array $messages
     * @return array
     */
    public function convertMessageToCsv($id, $messages)
    {
        App::uses('MailField', 'BcMail.Model');
        $mailFieldClass = new MailField();

        // フィールドの一覧を取得する
        $mailFields = $mailFieldClass->find('all', ['conditions' => ['MailField.mail_content_id' => $id], 'order' => 'sort']);

        // フィールド名とデータの変換に必要なヘルパーを読み込む
        App::uses('MaildataHelper', 'BcMail.View/Helper');
        App::uses('MailfieldHelper', 'BcMail.View/Helper');
        $Maildata = new MaildataHelper(new View());
        $Mailfield = new MailfieldHelper(new View());

        foreach($messages as $key => $message) {
            $inData = [];
            $inData['NO'] = $message[$this->alias]['id'];
            foreach($mailFields as $mailField) {
                if ($mailField['MailField']['type'] === 'file') {
                    $inData[$mailField['MailField']['field_name'] . ' (' . $mailField['MailField']['name'] . ')'] = $message[$this->alias][$mailField['MailField']['field_name']];
                } else {
                    $inData[$mailField['MailField']['field_name'] . ' (' . $mailField['MailField']['name'] . ')'] = $Maildata->toDisplayString(
                        $mailField['MailField']['type'],
                        $message[$this->alias][$mailField['MailField']['field_name']],
                        $Mailfield->getOptions($mailField['MailField'])
                    );
                }
            }
            $inData['作成日'] = $message[$this->alias]['created'];
            $inData['更新日'] = $message[$this->alias]['modified'];
            $messages[$key][$this->alias] = $inData;
        }

        return $messages;
    }

    /**
     * メール受信テーブルを全て再構築
     *
     * @return boolean
     */
    public function reconstructionAll()
    {

        // メール受信テーブルの作成
        $MailContent = ClassRegistry::init('BcMail.MailContent');
        $contents = $MailContent->find('all', ['recursive' => -1]);

        $result = true;
        foreach($contents as $content) {
            if ($this->createTable($content['MailContent']['id'])) {
                if (!$this->construction($content['MailContent']['id'])) {
                    $result = false;
                }
            } else {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * find
     *
     * @param String $type
     * @param mixed $query
     * @return Array
     */
    public function find(string $type = 'all', array $options = []): Query
    {
        return parent::find($type, $options);
        // TODO ucmitz 以下、未検証
        // テーブルを共用しているため、環境によってはデータ取得に失敗する。
        // その原因のキャッシュメソッドをfalseに設定。
        $db = ConnectionManager::get('default');
        $db->cacheMethods = false;
        $result = parent::find($type, $options);
        $db->cacheMethods = true;
        return $result;
    }
}
