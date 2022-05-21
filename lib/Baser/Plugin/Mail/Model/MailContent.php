<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * メールコンテンツモデル
 *
 * @package Mail.Model
 *
 */
class MailContent extends MailAppModel
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'MailContent';

	/**
	 * behaviors
	 *
	 * @var array
	 */
	public $actsAs = ['BcSearchIndexManager', 'BcContents'];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = ['MailField' =>
		['className' => 'Mail.MailField',
			'order' => 'sort',
			'foreignKey' => 'mail_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => '']];

	/**
	 * MailContent constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'id' => [
				[
					'rule' => 'numeric',
					'on' => 'update',
					'message' => __d('baser', 'IDに不正な値が利用されています。')
				]
			],
			'sender_name' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '送信先名は255文字以内で入力してください。')
				]
			],
			'subject_user' => [
				[
					'rule' => ['notBlank'],
					'message' => __d('baser', '自動返信メール件名[ユーザー宛]を入力してください。')
				],
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '自動返信メール件名[ユーザー宛]は255文字以内で入力してください。')
				]
			],
			'subject_admin' => [
				[
					'rule' => ['notBlank'],
					'message' => __d('baser', '自動送信メール件名[管理者宛]を入力してください。')
				],
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '自動返信メール件名[管理者宛]は255文字以内で入力してください。')
				]
			],
			'form_template' => [
				[
					'rule' => ['halfText'],
					'message' => __d('baser', 'メールフォームテンプレート名は半角のみで入力してください。'),
					'allowEmpty' => false
				],
				[
					'rule' => ['maxLength', 20],
					'message' => __d('baser', 'フォームテンプレート名は20文字以内で入力してください。')
				]
			],
			'mail_template' => [
				[
					'rule' => ['halfText'],
					'message' => __d('baser', '送信メールテンプレートは半角のみで入力してください。'),
					'allowEmpty' => false
				],
				[
					'rule' => ['maxLength', 20],
					'message' => __d('baser', 'メールテンプレート名は20文字以内で入力してください。')
				]
			],
			'redirect_url' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', 'リダイレクトURLは255文字以内で入力してください。')
				]
			],
			'sender_1' => [
				[
					'rule' => ['emails'],
					'allowEmpty' => true,
					'message' => __d('baser', '送信先メールアドレスの形式が不正です。')
				]
			],
			'sender_2' => [
				[
					'rule' => ['emails'],
					'allowEmpty' => true,
					'message' => __d('baser', '送信先メールアドレスの形式が不正です。')
				]
			],
			'ssl_on' => [
				[
					'rule' => 'checkSslUrl',
					"message" => __d('baser', 'SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。')
				]
			]
		];
	}

	/**
	 * SSL用のURLが設定されているかチェックする
	 *
	 * @param array $check チェック対象文字列
	 * @return boolean
	 */
	public function checkSslUrl($check)
	{
		if ($check[key($check)] && !Configure::read('BcEnv.sslUrl')) {
			return false;
		}

		return true;
	}

	/**
	 * 英数チェック
	 *
	 * @param array $check チェック対象文字列
	 * @return boolean
	 */
	public function alphaNumeric($check)
	{
		if (!preg_match("/^[a-z0-9]+$/", $check[key($check)])) {
			return false;
		}
		return true;
	}

	/**
	 * フォームの初期値を取得する
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		return [
			'MailContent' => [
				'subject_user' => __d('baser', 'お問い合わせ頂きありがとうございます'),
				'subject_admin' => __d('baser', 'お問い合わせを頂きました'),
				'layout_template' => 'default',
				'form_template' => 'default',
				'mail_template' => 'mail_default',
				'use_description' => true,
				'auth_captcha' => false,
				'ssl_on' => false,
				'save_info' => true
			]
		];
	}

	/**
	 * afterSave
	 *
	 * @return void
	 */
	public function afterSave($created, $options = [])
	{
		// 検索用テーブルへの登録・削除
		if (!$this->data['Content']['exclude_search'] && $this->data['Content']['status']) {
			$this->saveSearchIndex($this->createSearchIndex($this->data));
		} else {
			$this->deleteSearchIndex($this->data['MailContent']['id']);
		}
	}

	/**
	 * beforeDelete
	 *
	 * @return    boolean
	 * @access    public
	 */
	public function beforeDelete($cascade = true)
	{
		return $this->deleteSearchIndex($this->id);
	}

	/**
	 * 検索用データを生成する
	 *
	 * @param array $data
	 * @return array|false
	 */
	public function createSearchIndex($data)
	{
		if (!isset($data['MailContent']) || !isset($data['Content'])) {
			return false;
		}
		$mailContent = $data['MailContent'];
		$content = $data['Content'];
		return [
			'SearchIndex' =>
				[
					'type' => __d('baser', 'メール'),
					'model_id' => (!empty($mailContent['id']))? $mailContent['id'] : $this->id,
					'content_id' => $content['id'],
					'site_id' => $content['site_id'],
					'title' => $content['title'],
					'detail' => $mailContent['description'],
					'url' => $content['url'],
					'status' => $content['status'],
					'publish_begin' => $content['publish_begin'],
					'publish_end' => $content['publish_end']
				]
		];
	}

	/**
	 * メールコンテンツデータをコピーする
	 *
	 * @param int $id ページID
	 * @param int $newParentId 新しい親コンテンツID
	 * @param string $newTitle 新しいタイトル
	 * @param int $newAuthorId 新しいユーザーID
	 * @param int $newSiteId 新しいサイトID
	 * @return mixed mailContent|false
	 */
	public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null)
	{

		$data = $this->find('first', ['conditions' => ['MailContent.id' => $id], 'recursive' => 0]);
		$oldData = $data;

		// EVENT MailContent.beforeCopy
		$event = $this->dispatchEvent('beforeCopy', [
			'data' => $data,
			'id' => $id,
		]);
		if ($event !== false) {
			$data = $event->result === true? $event->data['data'] : $event->result;
		}

		$url = $data['Content']['url'];
		$siteId = $data['Content']['site_id'];
		$name = $data['Content']['name'];
		$eyeCatch = $data['Content']['eyecatch'];
		unset($data['MailContent']['id']);
		unset($data['MailContent']['created']);
		unset($data['MailContent']['modified']);
		unset($data['Content']);
		$data['Content'] = [
			'name' => $name,
			'parent_id' => $newParentId,
			'title' => $newTitle,
			'author_id' => $newAuthorId,
			'site_id' => $newSiteId
		];
		if (!is_null($newSiteId) && $siteId != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
		}
		$this->getDataSource()->begin();
		if ($result = $this->save($data)) {
			$result['MailContent']['id'] = $this->id;
			$data = $result;
			$mailFields = $this->MailField->find(
				'all',
				[
					'conditions' => ['MailField.mail_content_id' => $id],
					'order' => 'MailField.sort',
					'recursive' => -1
				]
			);
			foreach($mailFields as $mailField) {
				$mailField['MailField']['mail_content_id'] = $result['MailContent']['id'];
				$this->MailField->copy(null, $mailField, ['sortUpdateOff' => true]);
			}
			App::uses('MailMessage', 'Mail.Model');
			$MailMessage = ClassRegistry::init('Mail.MailMessage');
			$MailMessage->setup($result['MailContent']['id']);
			$MailMessage->_sourceConfigured = true; // 設定しておかないと、下記の処理にて内部的にgetDataSouceが走る際にエラーとなってしまう。
			$MailMessage->construction($result['MailContent']['id']);
			if ($eyeCatch) {
				$result['Content']['id'] = $this->Content->getLastInsertID();
				$result['Content']['eyecatch'] = $eyeCatch;
				$this->Content->set(['Content' => $result['Content']]);
				$result = $this->Content->renameToBasenameFields(true);
				$this->Content->set($result);
				$result = $this->Content->save();
				$data['Content'] = $result['Content'];
			}

			// EVENT MailContent.afterCopy
			$event = $this->dispatchEvent('afterCopy', [
				'id' => $data['MailContent']['id'],
				'data' => $data,
				'oldId' => $id,
				'oldData' => $oldData,
			]);

			$this->getDataSource()->commit();
			return $result;
		}
		$this->getDataSource()->rollback();
		return false;
	}

	/**
	 * フォームが公開中かどうかチェックする
	 *
	 * @param string $publishBegin 公開開始日時
	 * @param string $publishEnd 公開終了日時
	 * @return    bool
	 */
	public function isAccepting($publishBegin, $publishEnd)
	{
		if ($publishBegin && $publishBegin !== '0000-00-00 00:00:00') {
			if ($publishBegin > date('Y-m-d H:i:s')) {
				return false;
			}
		}
		if ($publishEnd && $publishEnd !== '0000-00-00 00:00:00') {
			if ($publishEnd < date('Y-m-d H:i:s')) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 公開済の conditions を取得
	 *
	 * @return array 公開条件（conditions 形式）
	 */
	public function getConditionAllowAccepting()
	{
		$conditions[] = ['or' => [[$this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')],
			[$this->alias . '.publish_begin' => null]]];
		$conditions[] = ['or' => [[$this->alias . '.publish_end >=' => date('Y-m-d H:i:s')],
			[$this->alias . '.publish_end' => null]]];
		return $conditions;
	}

	/**
	 * 公開されたコンテンツを取得する
	 *
	 * @param Model $model
	 * @param string $type
	 * @param array $query
	 * @return array|null
	 */
	public function findAccepting($type = 'first', $query = [])
	{
		$getConditionAllowAccepting = $this->getConditionAllowAccepting();
		if (!empty($query['conditions'])) {
			$query['conditions'] = array_merge(
				$getConditionAllowAccepting,
				$query['conditions']
			);
		} else {
			$query['conditions'] = $getConditionAllowAccepting;
		}
		return $this->find($type, $query);
	}

}
