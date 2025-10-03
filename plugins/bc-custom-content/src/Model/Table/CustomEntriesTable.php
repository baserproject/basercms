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

namespace BcCustomContent\Model\Table;

use ArrayObject;
use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\AppTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\Model\Entity\CustomTable;
use BcCustomContent\Utility\CustomContentUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Laminas\Diactoros\UploadedFile;

/**
 * CustomEntriesTable
 *
 * @property CustomTablesTable $CustomTables
 */
class CustomEntriesTable extends AppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Custom Table Id
     * @var null|integer
     */
    public $tableId = null;

    /**
     * 関連フィールド一覧
     *
     * @var null|ResultSet
     */
    public $links = null;

    /**
     * Initialize
     *
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
        $this->belongsTo('CustomTables', ['className' => 'BcCustomContent.CustomTables'])
            ->setForeignKey('custom_table_id');
        if (Plugin::isLoaded('BcSearchIndex')) {
            $this->addBehavior('BcSearchIndex.BcSearchIndexManager');
        }
    }

    /**
     * 検索インデックスを生成する
     *
     * @param CustomEntry $entry
     * @return array|false
     * @noTodo
     * @checked
     * @unitTest
     */
    public function createSearchIndex(CustomEntry $entry)
    {
        $customContent = $this->CustomTables->CustomContents->find()
            ->where(['CustomContents.custom_table_id' => $entry->custom_table_id])
            ->contain(['Contents'])
            ->first();
        /** @var Content $content */
        if (!$customContent) return false;
        $content = $customContent->content;

        $status = $entry->status;
        $publishBegin = $entry->publish_begin;
        $publishEnd = $entry->publish_end;
        // コンテンツのステータスを優先する
        if (!$content->status) {
            $status = false;
        }

        if ($publishBegin) {
            if ((!empty($content->publish_begin) && $content->publish_begin > $publishBegin)) {
                // コンテンツの公開開始の方が遅い場合
                $publishBegin = $content->publish_begin;
            } elseif (!empty($content->publish_end) && $content->publish_end < $publishBegin) {
                // 記事の公開開始より、コンテンツの公開終了が早い場合
                $publishBegin = $content->publish_end;
            }
        } else {
            if (!empty($content->publish_begin)) {
                // 記事の公開開始が定められていない
                $publishBegin = $content->publish_begin;
            }
        }
        if ($publishEnd) {
            if (!empty($content->publish_end) && $content->publish_end < $publishEnd) {
                // コンテンツの公開終了の方が早い場合
                $publishEnd = $content->publish_end;
            } elseif (!empty($content->publish_begin) && $content->publish_begin < $publishEnd) {
                // 記事の公開終了より、コンテンツの公開開始が早い場合
                $publishEnd = $content->publish_begin;
            }
        } else {
            if (!empty($content->publish_end)) {
                // 記事の公開終了が定められていない
                $publishEnd = $content->publish_end;
            }
        }

        return [
            'type' => __d('baser_core', 'カスタムコンテンツ'),
            'model_id' => $entry->id,
            'content_id' => $content->id,
            'site_id' => $content->site_id,
            'title' => $entry->title,
            'detail' => $this->createSearchDetail($entry),
            'url' => $content->url . 'view/' . ($entry->name?: $entry->id),
            'status' => $status,
            'publish_begin' => $publishBegin,
            'publish_end' => $publishEnd
        ];
    }

    /**
     * カスタムコンテンツの検索インデックスの詳細を生成する
     *
     * @param CustomEntry $entity
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createSearchDetail(CustomEntry $entity): string
    {
        $detail = $entity->name?: '';
        if (!$this->links) return $detail;
        foreach($this->links as $link) {
            /** @var CustomLink $link */
            if (!$link->status) continue;
            $controlType = Configure::read('BcCustomContent.fieldTypes.' . $link->custom_field->type . '.controlType');
            if (!in_array($controlType, ['text', 'textarea'])) continue;
            if ($detail) $detail .= ',';
            $detail .= $entity->{$link->name};
        }
        return $detail;
    }

    /**
     * カスタムエントリーテーブルのセットアップを行う
     *
     * @param int $tableId
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUp(int $tableId, array $postData = [])
    {
        /** @var CustomTable $table */
        $table = $this->CustomTables->get($tableId);
        if ($table->has_child) {
            $this->addBehavior('Tree', ['level' => 'level']);
        }

        // テーブル名の設定
        $this->setUseTable($tableId);
        $this->setLinks($tableId);
        $this->setupValidate($postData);
        // スキーマの初期化
        $this->_schema = null;
        $this->tableId = $tableId;
        return true;
    }

    /**
     * テーブル名を設定する
     *
     * @param int $tableId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUseTable(int $tableId)
    {
        $this->setTable($this->getTableName($tableId));
    }

    /**
     * カスタムエントリーのテーブル名を取得する
     *
     * @param int $tableId
     * @return string
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getTableName(int $tableId, string $name = ''): string
    {
        if (!$name) {
            $customTablesTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
            $name = $customTablesTable->get($tableId)->name;
        }
        $prefix = BcUtil::getCurrentDbConfig()['prefix'];
        return $prefix . 'custom_entry_' . $tableId . '_' . $name;
    }

    /**
     * メールフィールドを取得してセットする。
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setLinks(int $tableId)
    {
        $customLinksTable = TableRegistry::getTableLocator()->get('BcCustomContent.CustomLinks');
        // 配列化しておかないと動作が不安定
        // （複数回、foreach する際に、最初のレコードに戻らない）
        $this->links = $customLinksTable->find()
            ->contain(['CustomFields'])
            ->where([
                'CustomLinks.custom_table_id' => $tableId,
                'CustomFields.status' => true
            ])->all()->toArray();
    }

    /**
     * バリデーションを設定する
     *
     * @var array $postData
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupValidate(array $postData = [])
    {
        if (!$this->links) return;

        $validator = new $this->_validatorClass();
        $validator->setProvider('bc', 'BaserCore\Model\Validation\BcValidation');

        foreach($this->links as $link) {
            /** @var CustomLink $link */
            if ($link->required) {
                $validator->requirePresence($link->name)
                    ->notEmptyString($link->name, __d('baser_core', '{0} は必須項目です。', $link->title));
            } else {
                $validator->allowEmptyString($link->name);
            }
            $validator->requirePresence('creator_id', true, __d('baser_core', '作成者は必須項目です。'))
                ->notEmptyString('creator_id', __d('baser_core', '作成者は必須項目です。'));
            $validator = $this->setValidateRegex($validator, $link);
            $validator = $this->setValidateEmail($validator, $link);
            $validator = $this->setValidateNumber($validator, $link);
            $validator = $this->setValidateHankaku($validator, $link);
            $validator = $this->setValidateZenkakuKatakana($validator, $link);
            $validator = $this->setValidateZenkakuHiragana($validator, $link);
            $validator = $this->setValidateDatetime($validator, $link, $postData);
            $validator = $this->setValidateEmailConfirm($validator, $link);
            $validator = $this->setValidateMaxFileSize($validator, $link, $postData);
            $validator = $this->setValidateFileExt($validator, $link);
        }

        $this->setValidator('customEntry', $validator);
    }

    /**
     * ファイルアップロード上限バリデーションをセットアップする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @param array $postData
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateMaxFileSize(Validator $validator, CustomLink $link, array $postData)
    {
        if (empty($link->custom_field->meta['BcCustomContent']['max_file_size'])) return $validator;
        $maxFileSize = $link->custom_field->meta['BcCustomContent']['max_file_size'];

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $postData[$link->name]?? null;
        if(!$uploadedFile) return $validator;

        if ($uploadedFile->getError() !== UPLOAD_ERR_NO_FILE) {
            $validator->add($link->name, [
                'fileCheck' => [
                    'provider' => 'bc',
                    'rule' => ['fileCheck', BcUtil::convertSize($maxFileSize, 'B', 'M')],
                    'message' => __d('baser_core', 'ファイルサイズがオーバーしています。 {0} MB以内のファイルをご利用ください。', $maxFileSize)
                ]
            ]);
        }
        return $validator;
    }

    /**
     * ファイル拡張子バリデーションをセットアップする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @param array $postData
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateFileExt(Validator $validator, CustomLink $link)
    {
        if ($link->custom_field->type !== 'BcCcFile') {
            return $validator;
        }

        $fileExt = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];
        if (!empty($link->custom_field->meta['BcCustomContent']['file_ext'])) {
            $fileExt = explode(',', $link->custom_field->meta['BcCustomContent']['file_ext']);
        }

        $validator->add($link->name, [
            'fileExt' => [
                'provider' => 'bc',
                'rule' => ['fileExt', $fileExt],
                'message' => __d('baser_core', 'ファイル形式が無効です。拡張子 {0} のファイルをご利用ください。', implode(', ', $fileExt))
            ]
        ]);
        return $validator;
    }

    /**
     * Eメール比較バリデーションをセットアップする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateEmailConfirm(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('EMAIL_CONFIRM', $field->validate)) {
            if (empty($field->meta['BcCustomContent']['email_confirm'])) {
                return $validator;
            }
            $validator->add($link->name, [
                'checkSame' => [
                    'provider' => 'mailMessage',
                    'rule' => ['checkSame', $field->meta['BcCustomContent']['email_confirm']],
                    'message' => __d('baser_core', '入力データが一致していません。')
                ]
            ]);
        }
        return $validator;
    }

    /**
     * 正規表現のバリデーションをセットアップする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateRegex(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->regex) {
            $regex = str_replace('\|', '|', $field->regex);
            $regex = str_replace("\0", '', $regex); // ヌルバイト除去
            $validator->regex(
                $link->name,
                '/\A' . $regex . '\z/us',
                $field->regex_error_message?: __d('baser_core', '形式が無効です。')
            );
        }
        return $validator;
    }

    /**
     * Eメールチェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateEmail(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('EMAIL', $field->validate)) {
            $validator->email($link->name, false, __d('baser_core', 'Eメール形式で入力してください。'))
                ->regex(
                    $link->name,
                    '/^[a-zA-Z0-9!#$%&\’*+-\/=?^_`{|}~@.]*$/',
                    __d('baser_core', '半角で入力してください。')
                );
        }
        return $validator;
    }

    /**
     * 数値チェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateNumber(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('NUMBER', $field->validate)) {
            $validator->add($link->name, [
                'numeric' => [
                    'rule' => 'numeric',
                    'message' => __d('baser_core', '数値形式で入力してください。')
                ]
            ]);
        }
        return $validator;
    }

    /**
     * 半角チェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateHankaku(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('HANKAKU', $field->validate)) {
            $validator->add($link->name, [
                'asciiAlphaNumeric' => [
                    'rule' => 'asciiAlphaNumeric',
                    'message' => __d('baser_core', '半角英数で入力してください。')
                ]
            ]);
        }
        return $validator;
    }

    /**
     * 全角カタカナチェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateZenkakuKatakana(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('ZENKAKU_KATAKANA', $field->validate)) {
            $validator->add($link->name, [
                'checkKatakana' => [
                    'provider' => 'bc',
                    'rule' => 'checkKatakana',
                    'message' => __d('baser_core', '全角カタカナで入力してください。')
                ]
            ]);
        }
        return $validator;
    }

    /**
     * 全角ひらがなチェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateZenkakuHiragana(Validator $validator, CustomLink $link): Validator
    {
        $field = $link->custom_field;
        if ($field->validate && is_array($field->validate) && in_array('ZENKAKU_HIRAGANA', $field->validate)) {
            $validator->add($link->name, [
                'checkHiragana' => [
                    'provider' => 'bc',
                    'rule' => 'checkHiragana',
                    'message' => __d('baser_core', '全角ひらがなで入力してください。')
                ]
            ]);
        }
        return $validator;
    }

    /**
     * 日付チェックをセットする
     *
     * @param Validator $validator
     * @param CustomLink $link
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setValidateDatetime(Validator $validator, CustomLink $link, array $postData): Validator
    {

        $validator->setProvider('mailMessage', 'BcMail\Model\Validation\MailMessageValidation');
        $field = $link->custom_field;
        if ($field->type == "BcCcDate" || $field->type == "BcCcDateTime"
            || ($field->validate && is_array($field->validate) && in_array('DATETIME', $field->validate))) {
            if (isset($postData[$link->name]) && is_array($postData[$link->name])) {
                $validator->add($link->name, [
                    'dataArray' => [
                        'provider' => 'mailMessage',
                        'rule' => 'dataArray',
                        'message' => __d('baser_core', '日付の形式が無効です。')
                    ]
                ]);
            } else {
                $validator->add($link->name, [
                    'dateString' => [
                        'provider' => 'mailMessage',
                        'rule' => 'dateString',
                        'message' => __d('baser_core', '日付の形式が無効です。')
                    ]
                ]);
            }
        }
        return $validator;
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
        if ($this->hasValidator('customEntry')) $validator = $this->getValidator('customEntry');
        $validator->requirePresence('title')
            ->notEmptyString('title', __d('baser_core', 'タイトルは必須項目です。'));
        $validator
            ->allowEmptyString('name')
            ->regex('name', '/\D/', __d('baser_core', '数値だけのスラッグを登録することはできません。'));
        $validator
            ->add('published', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '公開日付に不正な文字列が入っています。')
                ]
            ])
            ->allowEmptyDateTime('published_date');

        $validator
            ->add('publish_begin', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '公開開始日に不正な文字列が入っています。')
                ]
            ])
            ->allowEmptyDateTime('publish_begin');

        $validator
            ->add('publish_end', [
                'dateTime' => [
                    'rule' => ['dateTime'],
                    'message' => __d('baser_core', '公開終了日に不正な文字列が入っています。')
                ]
            ])
            ->allowEmptyDateTime('publish_end')
            ->add('publish_end', [
                'checkDateAfterThan' => [
                    'rule' => ['checkDateAfterThan', 'publish_begin'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', '公開終了日は、公開開始日より新しい日付で入力してください。')
                ]
            ]);

        return $validator;
    }

    /**
     * Before Marshal
     *
     * @param EventInterface $event
     * @param ArrayObject $content
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $content, ArrayObject $options)
    {
        // beforeMarshal のタイミングで変換しないと配列が null になってしまう
        $this->autoConvert($content);
    }

    /**
     * エンティティの配列データの自動変換処理
     *
     * @param ArrayObject $content
     * @return ArrayObject
     * @checked
     * @noTodo
     * @unitTest
     */
    public function autoConvert(ArrayObject $content)
    {
        if(empty($this->links)) return $content;
        foreach($content as $key => $value) {
            $fieldLink = null;
            foreach($this->links as $link) {
                if ($link->name === $key) {
                    $fieldLink = $link;
                    break;
                }
            }
            if (empty($fieldLink)) continue;
            $controlType = CustomContentUtil::getPluginSetting($fieldLink->custom_field->type, 'controlType');
            if ($controlType === 'file') continue;
            if (is_array($value)) {
                unset($value['__loop-src__']);
                $content[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }
        return $content;
    }

    /**
     * Find all
     *
     * JSON データをデコードする
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findAll(Query $query, array $options = []): Query
    {
        return $query->formatResults(function(\Cake\Collection\CollectionInterface $results) {
            return $results->map(function($row) {
                if (!is_object($row) || !method_exists($row, 'toArray')) return $row;
                return $this->decodeRow($row);
            });
        });
    }

    /**
     * エンティティのJSON化したフィールドをデコードして返す
     *
     * @param EntityInterface $row
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function decodeRow(EntityInterface $row)
    {
        $rowArray = $row->toArray();
        foreach($rowArray as $key => $value) {
            if (is_string($value) && $this->isJson($value)) {
                $row->{$key} = json_decode($value, true);
            }
        }
        return $row;
    }

    /**
     * JSON データかどうかを判定する
     *
     * @param string $string
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    function isJson(string $string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE)? true : false;
    }

}
