<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;
use Cake\ORM\Entity;
use \Cake\View\Helper;
use Cake\View\Helper\FlashHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\UrlHelper;

/**
 * Class BcBaserHelper
 * @package BaserCore\View\Helper
 * @property HtmlHelper $Html
 * @property UrlHelper $Url
 * @property FlashHelper $Flash
 */
class BcBaserHelper extends Helper
{
	public $helpers = ['Html', 'Url', 'Flash'];
	// TODO 取り急ぎ
	public $siteConfig = [
		'formal_name' => 'baserCMS',
		'admin_side_banner' => true
	];
	public function js($url, $inline = true, $options = []) {
		$options = array_merge(['block' => !$inline], $options);
		$result = $this->Html->script($url, $options);
		if ($inline) {
			echo $result;
		}
	}

	public function element($name, $data = [], $options = []) {
		echo $this->getElement($name, $data, $options);
	}

	public function getElement($name, $data = [], $options = []) {
		return $this->_View->element($name, $data, $options);
	}

	public function getImg($path, $options = []) {
		return $this->Html->image($path, $options);
	}

	public function link($title, $url = null, $htmlAttributes = [], $confirmMessage = false) {
		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage);
	}

	public function getLink($title, $url = null, $options = [], $confirmMessage = false) {
	    if($confirmMessage) {
	        $options['confirm'] = $confirmMessage;
	    }
		return $this->Html->link($title, $url, $options);
	}

	public function isAdminUser ($userGroupId = null) {

	}
	public function existsEditLink() {

	}

	public function existsPublishLink() {

	}

	public function url($url = null, $full = false, $sessionId = true) {

	}

    /**
     * ユーザー名を整形して取得する
     *
     * 姓と名を結合して取得
     * ニックネームがある場合にはニックネームを優先する
     *
     * @param Entity $user ユーザーデータ
     * @return string $userName ユーザー名
     */
	public function getUserName($user)
	{
		if (!empty($user->nickname)) {
			return $user->nickname;
		}
		$userName = [];
		if (!empty($user->real_name_1)) {
			$userName[] = $user->real_name_1;
		}
		if (!empty($user->real_name_2)) {
			$userName[] = $user->real_name_2;
		}
		$userName = implode(' ', $userName);
		return $userName;
	}

    public function i18nScript($data, $options = []) {

    }

    /**
     * セッションに保存したメッセージを出力する
     *
     * メールフォームのエラーメッセージ等を出力します。
     *
     * @param string $key 出力するメッセージのキー（初期状態では省略可）
     */
    public function flash($key = 'flash'): void
    {
        $session = $this->_View->getRequest()->getSession();
		$sessionMessageList = $session->read('Flash');
		if ($sessionMessageList) {
		    echo '<div id="MessageBox" class="message-box">';
			foreach ($sessionMessageList as $messageKey => $sessionMessage) {
				if ($key === $messageKey && $session->check('Flash.' . $messageKey)) {
					echo $this->Flash->render($messageKey, ['escape' => false]);
				}
			}
			echo '</div>';
		}
    }

    public function getContentsTitle() {

    }

    /**
     * コンテンツを特定する文字列を出力する
     *
     * URL を元に、第一階層までの文字列をキャメルケースで取得する
     * ※ 利用例、出力例については BcBaserHelper::getContentsName() を参照
     *
     * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで出力する（初期値 : false）
     * @param array $options オプション（初期値 : array()）
     *	※ オプションの詳細については、BcBaserHelper::getContentsName() を参照
     * @return void
     */
	public function contentsName($detail = false, $options = []) {
		echo $this->getContentsName($detail, $options);
	}


    /**
     * コンテンツを特定する文字列を取得する
     *
     * URL を元に、第一階層までの文字列をキャメルケースで取得する
     *
     * 《利用例》
     * $this->BcBaser->contentsName()
     *
     * 《出力例》
     * - トップページの場合 : Home
     * - about ページの場合 : About
     *
     * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで取得する（初期値 : false）
     * @param array $options オプション（初期値 : array()）
     *	- `home` : トップページの場合に出力する文字列（初期値 : Home）
     *	- `default` : ルート直下の下層ページの場合に出力する文字列（初期値 : Default）
     *	- `error` : エラーページの場合に出力する文字列（初期値 : Error）
     *  - `underscore` : キャメルケースではなく、アンダースコア区切りで出力する（初期値 : false）
     * @return string
     */
	public function getContentsName($detail = false, $options = []) {
	    // TODO : 一時対応。実装要
	    $request = $this->_View->getRequest();
	    if($request->getParam('action') === 'login') {
            return 'AdminUsersLogin';
        } else {
            return 'Admin';
        }
	}

    /**
     * 編集画面へのリンクを出力する
     *
     * @return void
     */
	public function editLink() {
        // TODO: 未実装
	}

    /**
     * 公開ページへのリンクを出力する
     *
     * 管理システムで利用する
     *
     * @return void
     */
	public function publishLink() {
		// TODO: 未実装
	}

    /**
     * baserCMSの設置フォルダを考慮したURLを取得する
     *
     * 《利用例》
     * <a href="<?php echo $this->BcBaser->getUrl('/about') ?>">会社概要</a>
     *
     * @param mixed $url baserCMS設置フォルダからの絶対URL、もしくは配列形式のURL情報
     *		省略した場合には、PC用のトップページのURLを取得する
     * @param bool $full httpから始まるURLを取得するかどうか
     * @param bool $sessionId セションIDを付加するかどうか
     * @return string URL
     */
	public function getUrl($url = null, $full = false, $sessionId = true) {
	    // TODO $sessionId について実装要
	    return $this->Url->build($url, ['fullBase' => $full]);
	}

}
