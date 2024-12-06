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

use Authentication\PasswordHasher\DefaultPasswordHasher;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BcMail\View\Helper\MaildataHelper;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Validation\Validator;
use Cake\View\View;

/**
 * メッセージモデル
 *
 *
 */
class MailMessagesTable extends MailAppTable
{

    /**
     * メールフォーム情報
     *
     * @var ResultSet
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
     * @unitTest
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
     * @param array $postData
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setup(int $mailContentId, array $postData = [])
    {
        // テーブル名の設定
        $this->setUseTable($mailContentId);
        // メールフィールドの設定
        $this->setMailFields($mailContentId);
        // アップロード設定
        $this->setupUpload($mailContentId);
        // バリデーションの設定
        $this->setupValidate($postData);
        // スキーマの初期化
        $this->_schema = null;
        return true;
    }

    /**
     * メールフィールドを取得してセットする。
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setMailFields(int $mailContentId)
    {
        $mailFieldsTable = TableRegistry::getTableLocator()->get('BcMail.MailFields');
        $this->mailFields = $mailFieldsTable->find()->where([
            'MailFields.mail_content_id' => $mailContentId,
            'MailFields.use_field' => true
        ])->all();
    }

    /**
     * デフォルトのバリデーションを設定する
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $this->hasValidator('MailMessages')? $this->getValidator('MailMessages') : $validator;
    }

    /**
     * テーブル名を設定する
     *
     * @param $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUseTable($mailContentId)
    {
        $this->setTable($this->createTableName($mailContentId));
    }

    /**
     * テーブル名を生成する
     * int型でなかったら強制終了
     * @param int $mailContentId
     * @return string The table name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createTableName(int $mailContentId): string
    {
        return $this->addPrefix("mail_message_{$mailContentId}");
    }

    /**
     * アップロード設定を行う
     * @checked
     * @noTodo
     * @unitTest
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
     * After Marshal
     *
     * @param Event $event
     * @return void
     * @checked
     * @noTodo
     */
    public function afterMarshal(Event $event)
    {
        $entity = $event->getData('entity');
        // 不完全データチェック
        $this->_validGroupComplete($entity);
        // バリデートグループエラーチェック
        $this->_validGroupErrorCheck($entity);
    }

    /**
     * バリデーションをを個別に設定する
     *
     * @return void
     * @checked
     */
    protected function setupValidate(array $postData)
    {
        $validator = new $this->_validatorClass();
        $validator->setProvider('mailMessage', 'BcMail\Model\Validation\MailMessageValidation');
        $validator->setProvider('bc', 'BaserCore\Model\Validation\BcValidation');

        foreach($this->mailFields as $mailField) {
            if ($mailField->valid) {
                // 必須項目
                if ($mailField->type === 'file') {
                    if (!isset($postData[$mailField->field_name . '_tmp'])) {
                        $validator->requirePresence($mailField->field_name)
                            ->add($mailField->field_name, [
                                'notFileEmpty' => [
                                    'provider' => 'bc',
                                    'rule' => 'notFileEmpty',
                                    'message' => __d('baser_core', '必須項目です。')
                                ]
                            ]);
                    }
                } else {
                    $validator->requirePresence($mailField->field_name)
                        ->notEmptyString($mailField->field_name, __d('baser_core', '必須項目です。'));
                }
            } else {
                $validator->allowEmptyString($mailField->field_name);
            }

            // ファイル拡張子チェックデフォルト設定
            if ($mailField->type === 'file') {
                $validator->add($mailField->field_name, [
                    'fileExt' => [
                        'provider' => 'bc',
                        'rule' => ['fileExt', 'gif,jpg,jpeg,png,pdf'],
                        'message' => __d('baser_core', 'ファイル形式が無効です。')
                    ]
                ]);
            }

            // ### 拡張バリデーション
            if ($mailField->valid_ex && !empty($mailField->use_field)) {
                $valids = explode(',', $mailField->valid_ex);
                foreach($valids as $valid) {
                    $options = preg_split('/(?<!\\\)\|/', $mailField->options);
                    /**
                     * 引数のペアから連想配列を構築する
                     *
                     * Example:
                     * `aa('a','b')`
                     *
                     * Would return:
                     * `array('a'=>'b')`
                     *
                     * @return array Associative array
                     */
                    $options = call_user_func_array(function() {
                        $args = func_get_args();
                        $argc = count($args);
                        for($i = 0; $i < $argc; $i++) {
                            if ($i + 1 < $argc) {
                                $a[$args[$i]] = $args[$i + 1];
                            } else {
                                $a[$args[$i]] = null;
                            }
                            $i++;
                        }
                        return $a;
                    }, $options);

                    switch($valid) {
                        case 'VALID_MAX_FILE_SIZE':
                            if (
                                !empty($options['maxFileSize']) &&
                                (isset($postData[$mailField->field_name]['error']) &&
                                    $postData[$mailField->field_name]['error'] !== UPLOAD_ERR_NO_FILE)
                            ) {
                                $validator->add($mailField->field_name, [
                                    'fileCheck' => [
                                        'provider' => 'bc',
                                        'rule' => ['fileCheck', BcUtil::convertSize($options['maxFileSize'], 'B', 'M')],
                                        'message' => __d('baser_core', 'ファイルのアップロードに失敗しました。')
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
                                        'provider' => 'bc',
                                        'rule' => ['fileExt', $options['fileExt']],
                                        'message' => __d('baser_core', 'ファイル形式が無効です。')
                                    ]
                                ]);
                            }
                            break;

                        case 'VALID_REGEX':
                            if (!empty($options['regex'])) {
                                $options['regex'] = str_replace('\|', '|', $options['regex']);
                                $options['regex'] = str_replace("\0", '', $options['regex']); // ヌルバイト除去
                                $validator->regex(
                                    $mailField->field_name,
                                    '/\A' . $options['regex'] . '\z/us',
                                    __d('baser_core', '形式が無効です。')
                                );
                            }
                            break;

                        case 'VALID_EMAIL':
                            $validator->email($mailField->field_name, false, __d('baser_core', 'Eメール形式で入力してください。'))
                                ->regex(
                                    $mailField->field_name,
                                    '/^[a-zA-Z0-9!#$%&\’*+-\/=?^_`{|}~@.]*$/',
                                    __d('baser_core', '半角で入力してください。')
                                );
                            break;

                        case 'VALID_NUMBER':
                            $validator->regex(
                                $mailField->field_name,
                                '/^[0-9]+$/u',
                                __d('baser_core', '数値形式で入力してください。')
                            );
                            break;

                        case 'VALID_DATETIME':
                            if (is_array($postData[$mailField->field_name])) {
                                $validator->add($mailField->field_name, [
                                    'dataArray' => [
                                        'provider' => 'mailMessage',
                                        'rule' => 'dataArray',
                                        'message' => __d('baser_core', '日付の形式が無効です。')
                                    ]
                                ]);
                            } else {
                                $validator->add($mailField->field_name, [
                                    'dateString' => [
                                        'provider' => 'mailMessage',
                                        'rule' => 'dateString',
                                        'message' => __d('baser_core', '日付の形式が無効です。')
                                    ]
                                ]);
                            }
                            break;

                        case 'VALID_ZENKAKU_KATAKANA':
                            $validator->regex(
                                $mailField->field_name,
                                '/^(|[ァ-ヾ 　]+)$/u',
                                __d('baser_core', '全て全角カタカナで入力してください。')
                            );
                            break;

                        case 'VALID_ZENKAKU_HIRAGANA':
                            $validator->regex(
                                $mailField->field_name,
                                '/^([　 \t\r\n]|[ぁ-ん]|[ー])+$/u',
                                __d('baser_core', '全て全角ひらがなで入力してください。')
                            );
                            break;

                        case 'VALID_EMAIL_CONFIRM':
                            $target = '';
                            foreach(clone $this->mailFields as $value) {
                                if ($value->group_valid === $mailField->group_valid &&
                                    $value->field_name !== $mailField->field_name) {
                                    $target = $value->field_name;
                                    break;
                                }
                            }
                            if ($target) {
                                $validator->add($mailField->field_name, [
                                    'checkSame' => [
                                        'provider' => 'mailMessage',
                                        'rule' => ['checkSame', $target],
                                        'message' => __d('baser_core', '入力データが一致していません。')
                                    ]
                                ]);
                            }
                    }
                }
            }
        }

        $this->setValidator('MailMessages', $validator);
    }

    /**
     * バリデートグループエラーチェック
     *
     * @param EntityInterface $entity
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _validGroupErrorCheck(EntityInterface $entity)
    {
        $dists = [];

        // 対象フィールドを取得
        foreach($this->mailFields as $mailField) {
            // 対象フィールドがあれば、バリデートグループごとに配列にフィールド名を格納する
            if (!empty($mailField->use_field) && $mailField->group_valid) {
                $dists[$mailField->group_valid][] = $mailField->field_name;
            }
        }

        // エラーが発生しているかチェック
        foreach($dists as $key => $dist) {
            foreach($dist as $data) {
                if ($entity->getError($data)) {
                    $entity->setError($key, []);
                }
            }
        }
    }

    /**
     * 不完全データチェック
     *
     * @param EntityInterface $entity
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _validGroupComplete(EntityInterface $entity)
    {
        // 対象フィールドを取得
        $dists = [];
        foreach($this->mailFields as $mailField) {
            // 対象フィールドがあれば、バリデートグループごとに配列に格納する
            $valids = explode(',', $mailField->valid_ex);
            if (in_array('VALID_GROUP_COMPLATE', $valids)) {
                $dists[$mailField->group_valid][] = [
                    'name' => $mailField->field_name,
                    'value' => $entity->{$mailField->field_name}
                ];
            }
        }
        // チェック
        // バリデートグループにおけるデータの埋まり具合をチェックし、全て埋まっていない場合、
        // 全て埋まっている場合以外は不完全データとみなしエラーとする
        foreach($dists as $key => $dist) {
            $i = 0;
            foreach($dist as $value) {
                if (!empty($value['value'])) $i++;
            }
            $count = count($dist);
            if ($i > 0 && $i < $count) {
                $entity->setError($key . '_not_complate', [__d('baser_core', '入力データが不完全です。')]);
                foreach($dist as $jValue) {
                    $entity->setError($jValue['name'], []);
                }
            }
        }
    }

    /**
     * データベース用のデータに変換する
     *
     * @param ResultSetInterface $mailFields
     * @param EntityInterface $mailMessage
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function convertToDb(ResultSetInterface $mailFields, EntityInterface $mailMessage)
    {
        foreach($mailFields as $mailField) {
            if (empty($mailMessage->{$mailField->field_name})) continue;
            $value = $mailMessage->{$mailField->field_name};
            // マルチチェックのデータを｜区切りに変換
            if ($mailField->type === 'multi_check' && $mailField->use_field && $value && is_array($value)) {
                $value = implode("|", $value);
            }
            // パスワードのデータをハッシュ化
            if ($mailField->type === 'password' && !empty($value)) {
                $value = (new DefaultPasswordHasher())->hash($value);
            }
            // 機種依存文字を変換
            $mailMessage->{$mailField->field_name} = $this->replaceText($value);
        }
        return $mailMessage;
    }

    /**
     * メール用に変換する
     *
     * @param array $dbDatas
     * @return array $dbDatas
     * @checked
     * @noTodo
     * @TODO ヘルパー化すべきかも
     * @unitTest
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
                $mailContent->subject_user = str_replace('{$' . $fieldName . '}', $message->{$fieldName}?? '', $mailContent->subject_user);
                $mailContent->subject_admin = str_replace('{$' . $fieldName . '}', $message->{$fieldName}?? '', $mailContent->subject_admin);
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createFullTableName($mailContentId)
    {
        return $this->tablePrefix . $this->createTableName($mailContentId);
    }

    /**
     * 受信メッセージの内容を表示状態に変換する
     *
     * @param array $messages
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function convertMessageToCsv(array $messages)
    {
        // フィールド名とデータの変換に必要なヘルパーを読み込む
        $maildataHelper = new MaildataHelper(new View());
        $csv = [];
        foreach($messages as $key => $message) {
            $inData = [];
            $inData['NO'] = $message->id;
            foreach($this->mailFields as $mailField) {
                if ($mailField->type === 'file') {
                    $inData[$mailField->field_name . ' (' . $mailField->name . ')'] = $message->{$mailField->field_name};
                } else {
                    $inData[$mailField->field_name . ' (' . $mailField->name . ')'] = $maildataHelper->toDisplayString(
                        $mailField->type,
                        $message->{$mailField->field_name}
                    );
                }
            }
            $inData['作成日'] = $message->created;
            $inData['更新日'] = $message->modified;
            $csv[$key]['MailMessage'] = $inData;
        }
        return $csv;
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
        $contents = $MailContent->find('all', ...['recursive' => -1]);

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
     * @param mixed $args
     * @return SelectQuery
     */
    public function find(string $type = 'all', mixed ...$args): SelectQuery
    {
        return parent::find($type, ...$args);
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
