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

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\View\Helper;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールヘルパー
 *
 *
 */
class MailHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ヘルパー
     * @var array
     */
    public $helpers = ['BcBaser'];

    /**
     * コンストラクタ
     *
     * @param View $View Viewオブジェクト
     * @return void
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        $this->setMailContent();
    }

    /**
     * メールコンテンツデータをセットする
     *
     * @param int $mailContentId メールコンテンツID
     * @return void
     */
    public function setMailContent($mailContentId = null)
    {
        if (isset($this->mailContent)) {
            return;
        }
        if ($mailContentId) {
            $MailContent = ClassRegistry::init('BcMail.MailContent');
            $MailContent->reduceAssociations([]);
            $this->mailContent = Hash::extract($MailContent->read(null, $mailContentId), 'MailContent');
        } elseif ($this->_View->get('mailContent')) {
            $this->mailContent = $this->_View->get('mailContent');
        }
    }

    /**
     * フォームテンプレートを取得
     *
     * コンボボックスのソースとして利用
     *
     * @return array フォームテンプレート一覧データ
     * @todo 他のヘルパーに移動する
     * @checked
     * @noTodo
     */
    public function getFormTemplates($siteId = 1)
    {
        $templatesPaths = BcUtil::getFrontTemplatePaths($siteId, 'BcMail');
        $templates = [];
        foreach ($templatesPaths as $templatePath) {
            $templatePath .= 'Mail' . DS;
            $folder = new Folder($templatePath);
            $files = $folder->read(true, true);
            if ($files[0]) {
                if ($templates) {
                    $templates = array_merge($templates, $files[0]);
                } else {
                    $templates = $files[0];
                }
            }
        }
        $templates = array_unique($templates);
        return array_combine($templates, $templates);
    }

    /**
     * メールテンプレートを取得
     *
     * コンボボックスのソースとして利用
     *
     * @return array メールテンプレート一覧データ
     * @todo 他のヘルパに移動する
     * @checked
     * @noTodo
     */
    public function getMailTemplates($siteId = 1)
    {
        $templatesPaths = BcUtil::getFrontTemplatePaths($siteId, 'BcMail');
        $templates = [];
        $ext = Configure::read('BcApp.templateExt');
        foreach ($templatesPaths as $templatePath) {
            $templatePath .= 'email' . DS . 'text' . DS;
            $folder = new Folder($templatePath);
            $files = $folder->read(true, true);
            if ($files[1]) {
                foreach($files[1] as $key => $file) {
                    if($file === 'mail_data.php' || !preg_match('/^mail_/', $file)) {
                        unset($files[1][$key]);
                    } else {
                        $files[1][$key] = basename($file, $ext);
                    }
                }
                if ($templates) {
                    $templates = array_merge($templates, $files[1]);
                } else {
                    $templates = $files[1];
                }
            }
        }
        $templates = array_unique($templates);
        return array_combine($templates, $templates);
    }

    /**
     * メールフォームの説明文を取得する
     * @return string メールフォームの説明文
     */
    public function getDescription()
    {
        return $this->mailContent['description'];
    }

    /**
     * メールの説明文を出力する
     *
     * @return void
     */
    public function description()
    {
        echo $this->getDescription();
    }

    /**
     * メールの説明文が設定されているかどうかを判定する
     *
     * @return boolean 設定されている場合 true を返す
     */
    public function descriptionExists()
    {
        if (empty($this->mailContent['description'])) {
            return false;
        }

        return true;
    }

    /**
     * メールフォームへのリンクを生成する
     *
     * @param string $title リンクのタイトル
     * @param string $contentsName メールフォームのコンテンツ名
     * @param array $datas メールフォームに引き継ぐデータ（初期値 : array()）
     * @param array $options a タグの属性（初期値 : array()）
     *    ※ オプションについては、HtmlHelper::link() を参照
     * @return void
     */
    public function link($title, $contentsName, $datas = [], $options = [])
    {
        if ($datas && is_array($datas)) {
            foreach ($datas as $key => $data) {
                $datas[$key] = base64UrlsafeEncode($data);
            }
        }
        $link = array_merge(['plugin' => '', 'controller' => $contentsName, 'action' => 'index'], $datas);
        $this->BcBaser->link($title, $link, $options);
    }

    /**
     * ブラウザの戻るボタン対応コードを作成
     *
     * @return string
     */
    public function getToken()
    {
        return $this->BcBaser->getElement('BcMail.mail_token');
    }

    /**
     * ブラウザの戻るボタン対応コードを出力
     *
     * @return void
     */
    public function token()
    {
        echo $this->getToken();
    }

    /**
     * メールフォームを取得する
     *
     * @param $id
     * @return mixed
     */
    public function getForm($id = null)
    {
        $MailContent = ClassRegistry::init('BcMail.MailContent');
        $conditions = [];
        if ($id) {
            $conditions = [
                'MailContent.id' => $id
            ];
        }
        $mailContent = $MailContent->findPublished('first', ['conditions' => $conditions]);
        if (!$mailContent) {
            return false;
        }
        $url = $mailContent['Content']['url'];
        return $this->requestAction($url, ['return' => true]);
    }

    /**
     * beforeRender
     *
     * @param Event $event
     * @param string $viewFile
     */
    public function beforeRender(Event $event, string $viewFile)
    {
        $request = $this->getView()->getRequest();
        if ($request->getParam('controller') === 'Mail' && in_array($request->getParam('action'), ['index', 'confirm', 'submit'])) {
            // メールフォームをショートコードを利用する際、ショートコードの利用先でキャッシュを利用している場合、
            // セキュリティコンポーネントで発行するトークンが更新されない為、強制的にキャッシュをオフにする
            // TODO ucmitz 未実装
            /*if (!empty($request->getParam('requested'))) {
                Configure::write('Cache.disable', true);
            }*/
            // TODO ucmitz 未検証
            $this->getView()->setRequest($request->withParam('_Token.unlockedFields', $this->getView()->get('unlockedFields')));
        }
    }

    /**
     * 現在のページがメールプラグインかどうかを判定する
     *
     * @return bool
     */
    public function isMail(): bool
    {
        $content = $this->getView()->getRequest()->getAttribute('currentContent');
        if(!$content) return false;
        return ($content->plugin === 'BcMail');
    }

}
