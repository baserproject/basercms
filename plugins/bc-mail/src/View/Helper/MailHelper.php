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
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsServiceInterface;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\View\Helper;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールヘルパー
 */
#[\AllowDynamicProperties]
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
    public array $helpers = [
        'BaserCore.BcBaser',
        'BaserCore.BcContents'
    ];

    /**
     * 現在のメールコンテンツ
     * @var MailContent
     */
    public $currentMailContent;

    /**
     * コンストラクタ
     *
     * @param View $View Viewオブジェクト
     * @return void
     * @checked
     * @noTodo
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
        if (isset($this->currentMailContent)) {
            return;
        }
        if ($mailContentId) {
            $MailContent = ClassRegistry::init('BcMail.MailContent');
            $MailContent->reduceAssociations([]);
            $this->currentMailContent = Hash::extract($MailContent->read(null, $mailContentId), 'MailContent');
        } elseif ($this->_View->get('mailContent')) {
            $this->currentMailContent = $this->_View->get('mailContent');
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
            $folder = new BcFolder($templatePath);
            $files = $folder->getFolders();
            if ($files) {
                if ($templates) {
                    $templates = array_merge($templates, $files);
                } else {
                    $templates = $files;
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
            $folder = new BcFolder($templatePath);
            $files = $folder->getFiles();
            if ($files) {
                foreach($files as $key => $file) {
                    if($file === 'mail_data.php' || !preg_match('/^mail_/', $file)) {
                        unset($files[$key]);
                    } else {
                        $files[$key] = basename($file, $ext);
                    }
                }
                if ($templates) {
                    $templates = array_merge($templates, $files);
                } else {
                    $templates = $files;
                }
            }
        }
        $templates = array_unique($templates);
        return array_combine($templates, $templates);
    }

    /**
     * メールフォームの説明文を取得する
     * @return string メールフォームの説明文
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDescription()
    {
        return $this->currentMailContent->description;
    }

    /**
     * メールの説明文を出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest ラッパーのためテスト不要
     */
    public function description()
    {
        echo $this->getDescription();
    }

    /**
     * メールの説明文が設定されているかどうかを判定する
     *
     * @return boolean 設定されている場合 true を返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function descriptionExists()
    {
        if (empty($this->currentMailContent->description)) {
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
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
     */
    public function getToken()
    {
        return $this->BcBaser->getElement('BcMail.mail_token');
    }

    /**
     * ブラウザの戻るボタン対応コードを出力
     *
     * @return void
     * @checked
     * @noTodo
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
        $mailContentsTable = TableRegistry::getTableLocator()->get('BcMail.MailContents');
        $conditions = ($id)? ['MailContents.id' => $id] : [];
        $mailContent = $mailContentsTable->find('accepting')
            ->where($conditions)
            ->contain(['Contents'])
            ->first();
        if (!$mailContent) return false;
        $url = $this->BcContents->getUrl($mailContent->content->url);
        $currentRequest = $this->getView()->getRequest();

        // メールフォーム用のリクエストを作成
        $request = BcUtil::createRequest($url, [], 'GET', ['session' => $currentRequest->getSession()]);
        $request = $request->withAttribute('formTokenData', $currentRequest->getAttribute('formTokenData'));
        $request = $request->withAttribute('csrfToken', $currentRequest->getAttribute('csrfToken'));
        $this->getView()->setRequest($request);

        $result = $this->getView()->cell('BcMail.Mail', [$request]);

        // リクエストを元に戻す
        $this->getView()->setRequest($currentRequest);
        Router::setRequest($currentRequest);
        return $result;
    }

    /**
     * beforeRender
     *
     * @param Event $event
     * @param string $viewFile
     * @checked
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isMail(): bool
    {
        $content = $this->getView()->getRequest()->getAttribute('currentContent');
        if(!$content) return false;
        return ($content->plugin === 'BcMail');
    }

    /**
     * 公開状態のメールコンテンツを取得する
     *
     * @param int $siteId
     * @return mixed
     * @checked
     * @noTodo
     */
    public function getPublishedMailContents(int $siteId)
    {
        $service = $this->getService(MailContentsServiceInterface::class);
        return $service->getPublishedAll($siteId);
    }

}
