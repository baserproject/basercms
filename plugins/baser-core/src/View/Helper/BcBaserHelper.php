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

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\Site;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BcBlog\Model\Entity\BlogPost;
use BcCustomContent\Model\Entity\CustomContent;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Entity\CustomLink;
use BcMail\Model\Entity\MailField;
use BcMail\View\Helper\MailformHelper;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingElementException;
use Cake\View\Helper\BreadcrumbsHelper;
use Cake\View\View;
use Cake\View\Helper;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcAgent;
use Cake\View\Helper\UrlHelper;
use Cake\View\Helper\FlashHelper;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Doc;

/**
 * Class BcBaserHelper
 *
 * @property BcHtmlHelper $BcHtml
 * @property UrlHelper $Url
 * @property FlashHelper $Flash
 * @property BcAuthHelper $BcAuth
 * @property BreadcrumbsHelper $Breadcrumbs
 * @property BcContentsHelper $BcContents
 * @property BcGoogleMapsHelper $BcGoogleMaps
 * @property BcXmlHelper $BcXml
 *
 * ### BcContentsHelper
 * @method EntityInterface getParentContent(int $id = null, bool $direct = true)
 * @method Site getCurrentSite()
 * @method Content getCurrentContent()
 *
 * ### BcThemeConfigHelper
 * @method void mainImage(array $options = [])
 * @method void logo(array $options = [])
 *
 * ### BcWidgetAreaHelper
 * @method void widgetArea(int $no = null, array $options = [])
 * @method string getWidgetArea(int $no = null, array $options = [])
 * @method bool isMail() MailHelper
 *
 * ### BlogHelper
 * @method void blogPosts(string $contentsName = [], int $num = 5, array $options = [])
 * @method string getBlogPosts(string $contentsName = [], int $num = 5, array $options = [])
 * @method bool isBlogCategory()
 * @method bool isBlogTag()
 * @method bool isBlogDate()
 * @method bool isBlogMonth()
 * @method bool isBlogYear()
 * @method bool isBlogSingle()
 * @method bool isBlogHome()
 * @method array getBlogs(string $name = '', array $options = [])
 * @method bool isBlog()
 * @method array getBlogCategories(array $options = [])
 * @method bool hasChildBlogCategory(int $id)
 * @method array getBlogTagList(string $name, array $options = [])
 * @method void blogTagList(string $name, array $options = [])
 * @method string getBlogContentsUrl(int $blogContentId, $base = true)
 * @method int getBlogPostCount()
 * @method string getBlogTitle()
 * @method string getBlogPostLinkUrl(BlogPost $post, bool $base = true, bool $full = true)
 * @method void blogPostEyeCatch(BlogPost $post, array $options = [])
 * @method void blogPostDate(BlogPost $post, string $format = 'Y/m/d')
 * @method void blogPostTitle(BlogPost $post, bool $link = true, array $options = [])
 * @method void blogPostCategory(BlogPost $post, array $options = [])
 * @method void blogPostContent(BlogPost $post, bool $moreText = true, bool $moreLink = false, bool $cut = false, bool $lastText = false)
 * @method void blogDescription()
 * @method bool blogDescriptionExists()
 * @method string getBlogPostContent(BlogPost $post, bool $moreText = true, bool $moreLink = false, bool $cut = false, bool $lastText = false)
 * @method void blogPostPrevLink(BlogPost $post, string $title = '', array $htmlAttributes = [])
 * @method void blogPostNextLink(BlogPost $post, string $title = '', array $htmlAttributes = [])
 *
 * ### MailHelper
 * @method bool mailFormDescriptionExists()
 * @method void mailFormDescription()
 *
 * ### MailformHelper
 * @method void freezeMailForm()
 * @method string createMailForm($context = null, $options = [])
 * @method string mailFormHidden($fieldName, $options = [])
 * @method void mailFormAuthCaptcha(string $fieldName, array $options = [])
 * @method string mailFormSubmit(string $caption = null, array $options = [])
 * @method string endMailForm(array $secureAttributes = [])
 * @method MailformHelper unlockMailFormField(string $name)
 * @method mixed getMailFormSourceValue(string $fieldname, array $options = [])
 * @method string mailFormError(string $field, $text = null, array $options = [])
 * @method string mailFormControl(string $fieldName, array $options = [])
 * @method array getMailFormGroupValidErrors(array $mailFields, string $groupValid, array $options = [], bool $distinct = true)
 * @method bool isMailFormGroupLastField(ResultSet $mailFields, MailField $currentMailField)
 * @method string mailFormLabel(string $fieldName, ?string $text = null, array $options = [])
 *
 * ### BcUploadHelper
 * @method void setTableToUpload(string $tableName)
 *
 * ### BcFormHelper
 * @method string createForm($context = null, array $options = [])
 * @method string formControl(string $fieldName, array $options = [])
 * @method string formHidden(string $fieldName, array $options = [])
 * @method string formSubmit(?string $caption = null, array $options = [])
 * @method string formError(string $field, $text = null, array $options = [])
 * @method string endForm(array $secureAttributes = [])
 * @method string formLabel(string $fieldName, ?string $text = null, array $options = [])
 *
 * ### HtmlHelper
 * @method scriptStart(array $options = [])
 * @method string scriptEnd()
 * @method string meta($type, $content = null, array $options = [])
 *
 * ### CustomContentHelper
 * @method bool isDisplayCustomEntrySearch(CustomLink $customLink, string $type = 'front')
 * @method string customSearchControl(CustomLink $customLink, array $options = [])
 * @method void customContentDescription(CustomContent $content)
 * @method void customEntryTitle(CustomEntry $entry, array $options = [])
 * @method string customEntryPublished(CustomEntry $entry)
 * @method ResultSet getCustomLinks(int $tableId, bool $isThreaded = true)
 * @method bool isDisplayCustomField(CustomEntry $entry, string $fieldName)
 * @method string getCustomFieldTitle(mixed $entry, string $fieldName)
 * @method string|array getCustomFieldValue(mixed $entry, string $fieldName, array $options = [])
 *
 * ### TextHelper
 * @method string truncateText(string $text, int $length = 100, array $options = [])
 */
#[\AllowDynamicProperties]
class BcBaserHelper extends Helper
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * ヘルパー
     *
     * @var array
     */
    public array $helpers = [
        'Url', 'Js', 'Session', 'Flash',
        'BaserCore.BcHtml',
        'BaserCore.BcXml',
        'BaserCore.BcArray',
        'BaserCore.BcPage',
        'BaserCore.BcContents',
        'BaserCore.BcAuth',
        'Breadcrumbs',
        'BaserCore.BcGoogleMaps'
    ];

    /**
     * カテゴリタイトル設定
     *
     * タイトルタグとパンくず用の配列を取得する際にカテゴリのタイトルを取得するかどうかの判定を保持
     * getCrumbs() と、setTitle() で設定を変更できる
     * @var mixed boolean or null
     */
    protected $_categoryTitleOn = true;

    /**
     * タイトルタグとパンくず用の配列を取得する際に取得するカテゴリタイトル
     * $_categoryTitleOn が true の場合に取得するが、
     * $_categoryTitle が、true の場合は、パンくず用配列に格納された値を利用する
     * setCategoryTitle() で設定を変更できる
     *
     * @var mixed boolean or string
     */
    protected $_categoryTitle = true;

    /**
     * BcBaserHelper を拡張するプラグインのヘルパ
     *
     * BcBaserHelper::_initPluginBasers() で自動的に初期化される。
     *
     * @var array
     */
    protected $_pluginBasers = [];

    /**
     * コンストラクタ
     *
     * @param View $View ビュークラス
     * @param array $settings ヘルパ設定値
     * @checked
     * @noTodo
     */
    public function __construct(View $View, $settings = [])
    {
        parent::__construct($View, $settings);
        $request = $this->_View->getRequest();
        // プラグインのBaserヘルパを初期化
        if (BcUtil::isInstalled() && !$request->is('maintenance')) {
            $this->_initPluginBasers();
        }
    }

    /**
     * initialize
     *
     * @param  array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize($config): void
    {
        parent::initialize($config);
        if(!BcUtil::isInstalled()) return;
        $this->PermissionsService = $this->getService(PermissionsServiceInterface::class);
    }

    /**
     * Javascript タグを出力する
     *
     * @param string|array $path Javascriptのパス（js フォルダからの相対パス）拡張子は省略可
     * @param bool $inline コンテンツ内に Javascript を出力するかどうか（初期値 : true）
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function js($path, $inline = true, $options = [])
    {
        if (!isset($options['block'])) {
            $options['block'] = $inline ? null : true;
        }
        echo $this->BcHtml->script($path, $options);
    }

    /**
     * エレメントテンプレートを出力する
     *
     * @param string $name エレメント名
     * @param array $data エレメントで参照するデータ
     * @param array $options オプションのパラメータ
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     * ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function element($name, $data = [], $options = [])
    {
        $options = array_merge([
            'subDir' => true
        ], $options);
        echo $this->getElement($name, $data, $options);
    }

    /**
     * エレメントテンプレートのレンダリング結果を取得する
     *
     * @param string $name エレメント名
     * @param array $data エレメントで参照するデータ
     * @param array $options オプションのパラメータ
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     * ※ その他のパラメータについては、View::element() を参照
     * @return string エレメントのレンダリング結果
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     */
    public function getElement(string $name, array $data = [], array $options = [])
    {
        // EVENT beforeElement
        $event = $this->dispatchLayerEvent('beforeElement', [
            'name' => $name,
            'data' => $data,
            'options' => $options
        ], ['layer' => 'View', 'class' => '', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        // EVENT PluginName.ControllerName.beforeElement
        $event = $this->dispatchLayerEvent('beforeElement', [
            'name' => $name,
            'data' => $data,
            'options' => $options
        ], ['layer' => 'View', 'class' => $this->_View->getName()]);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        $out = '';
        try {
            $out = $this->_View->element($name, $data, $options);
        } catch (MissingElementException $e) {
            echo __d('baser_core', 'エレメントテンプレート「{0}」が見つかりませんでした。', $name);
            $this->log($e->getMessage());
        }

        // EVENT afterElement
        $event = $this->dispatchLayerEvent('afterElement', [
            'name' => $name,
            'out' => $out
        ], ['layer' => 'View', 'class' => '', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        // EVENT PluginName.ControllerName.afterElement
        $event = $this->dispatchLayerEvent('afterElement', [
            'name' => $name,
            'out' => $out
        ], ['layer' => 'View', 'class' => $this->_View->getName()]);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
    }

    /**
     * 画像タグを出力する
     *
     * @param string|array $path 画像のパス（img フォルダからの相対パス）
     * @param array $options オプション（主にHTML属性）
     *    ※ パラメータについては、HtmlHelper::image() を参照。
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function img($path, $options = [])
    {
        echo $this->getImg($path, $options);
    }

    /**
     * 画像タグを取得する
     *
     * @param mixed $path 画像のパス（img フォルダからの相対パス）
     * @param array $options オプション（主にHTML属性）
     * ※ パラメータについては、HtmlHelper::image() を参照。
     * @return string 画像タグ
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getImg($path, $options = [])
    {
        return $this->BcHtml->image($path, $options);
    }

    /**
     * アンカータグを出力する
     *
     * @param string $title タイトル
     * @param mixed $url オプション（初期値 : null）
     * @param array $htmlAttributes オプション（初期値 : array()）
     *    - `escape` : タイトルとHTML属性をエスケープするかどうか（初期値 : true）
     *    - `escapeTitle` : タイトルをエスケープするかどうか（初期値 : true）
     *    - `prefix` : URLにプレフィックスをつけるかどうか（初期値 : false）
     *    - `forceTitle` : 許可されていないURLの際にタイトルを強制的に出力するかどうか（初期値 : false）
     *    - `ssl` : SSL用のURLをして出力するかどうか（初期値 : false）
     *     ※ その他のパラメータについては、HtmlHelper::link() を参照。
     * @param string $confirmMessage 確認メッセージ（初期値 : false）
     *    リンクをクリックした際に確認メッセージが表示され、はいをクリックした場合のみ遷移する
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function link($title, $url = null, $htmlAttributes = [], $confirmMessage = false)
    {
        echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage);
    }

    /**
     * アンカータグを取得する
     *
     * @param string $title タイトル
     * @param mixed $url オプション（初期値 : null）
     * @param array $options オプション（初期値 : array()）
     *    - `escape` : タイトルとHTML属性をエスケープするかどうか（初期値 : true）
     *    - `escapeTitle` : タイトルをエスケープするかどうか（初期値 : true）
     *    - `prefix` : URLにプレフィックスをつけるかどうか（初期値 : false）
     *    - `forceTitle` : 許可されていないURLの際にタイトルを強制的に出力するかどうか（初期値 : false）
     *    - `ssl` : SSL用のURLをして出力するかどうか（初期値 : false）
     *    - `enabled` : リンクが有効かどうか（初期値 : true）
     *     ※ その他のパラメータについては、HtmlHelper::image() を参照。
     * @param bool $confirmMessage 確認メッセージ（初期値 : false）
     *    リンクをクリックした際に確認メッセージが表示され、はいをクリックした場合のみ遷移する
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     */
    public function getLink($title, $url = null, $options = [], $confirmMessage = false)
    {
        if ($confirmMessage) $options['confirm'] = $confirmMessage;
        if (!is_array($options)) $options = [$options];
        $options = array_merge([
            'escape' => true,
            'prefix' => false,
            'forceTitle' => false,
            'ssl' => $this->isSSL(),
            'enabled' => true
        ], $options);

        // EVENT Html.beforeGetLink
        $event = $this->dispatchLayerEvent('beforeGetLink', [
            'title' => $title,
            'url' => $url,
            'options' => $options,
            'confirmMessage' => $confirmMessage
        ], ['class' => 'Html', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        $request = $this->getView()->getRequest();
        $prefix = $options['prefix'];
        $forceTitle = $options['forceTitle'];
        $ssl = $options['ssl'];
        $enabled = $options['enabled'];
        unset($options['prefix'], $options['forceTitle'], $options['ssl'], $options['enabled']);

        if ($prefix && is_array($url) && !empty($request->getParam('prefix'))) {
            $url[$request->getParam('prefix')] = true;
        }

        $srcUrl = $this->getUrl($url, false, ['escape' => false]);
        $srcUrl = preg_replace('/^' . preg_quote($request->getAttribute('base'), '/') . '\//', '/', $srcUrl);

        if (!$enabled || !$this->isLinkEnabled($srcUrl)) {
            if ($forceTitle) {
                return "<span>$title</span>";
            } else {
                return '';
            }
        }

        // 現在SSLの場合、特定の条件でフルパスとする
        // - //(スラッシュスラッシュ)から始まるURL
        // - http / https 以外のプロトコル
        // - ハッシュタグから始まるURL
        $full = false;
        if (BcUtil::isInstalled()
            && ($this->isSSL() || $ssl)
            && !(preg_match('/^(javascript|https?|ftp|tel|mailto):/', $srcUrl))
            && !(strpos($srcUrl, '//') === 0)
            && !preg_match('/^#/', $srcUrl)) {
            $full = true;
        }

        if (preg_match('{^' . BcUtil::getPrefix(true) . '\/}', $srcUrl) || !isset($this->BcContents)) {
            $url = $this->getUrl($srcUrl, $full, ['escape' => false]);
        } else {
            $site = $this->getCurrentSite();
            $useSubdomain = ($site)? $site->use_subdomain : false;
            $url = $this->BcContents->getUrl($srcUrl, $full, $useSubdomain, (bool) $request->getAttribute('base'));
        }

        if (!$full) {
            $url = preg_replace('/^' . preg_quote($request->getAttribute('base'), '/') . '\//', '/', $url);
        }

        $out = $this->BcHtml->link($title?? '', $url, $options);

        // EVENT Html.afterGetLink
        $event = $this->dispatchLayerEvent('afterGetLink', [
            'url' => $url,
            'out' => $out
        ], ['class' => 'Html', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
    }

    /**
     * リンクが有効化どうか
     *
     * ログインしているユーザーの権限によって判定する
     * ログインしていない場合、ユーザーグループがユーザーに関連付けられていない場合は、常に有効とする
     * @param string $link
     * @return true
     * @noTodo
     * @checked
     * @unitTest
     */
    public function isLinkEnabled(string $link): bool
    {
        if (!BcUtil::isInstalled()) return true;
        $user = Bcutil::loginUser();
        if(!$user) return true;
        if(!$user->user_groups) return true;
        $userGroups = array_column($user->user_groups, 'id');
        return $this->PermissionsService->check($link, $userGroups);
    }


    /**
     * 管理者グループかどうかチェックする
     *
     * @param array|\BaserCore\Model\Entity\User $user ユーザー（初期値 : null）※ 指定しない場合は、現在のログインユーザーについてチェックする
     * @return bool 管理者グループの場合は true を返す
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function isAdminUser($user = null): bool
    {
        return BcUtil::isAdminUser($user);
    }

    /**
     * baserCMSの設置フォルダを考慮したURLを出力する
     *
     * 《利用例》
     * <a href="<?php $this->BcBaser->getUrl('/about') ?>">会社概要</a>
     *
     * @param mixed $url baserCMS設置フォルダからの絶対URL、もしくは配列形式のURL情報
     *        省略した場合には、PC用のトップページのURLを出力する
     * @param bool $full httpから始まるURLを取得するかどうか
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     */
    public function url($url = null, $full = false)
    {
        echo $this->getUrl($url, $full);
    }

    /**
     * ユーザー名を整形して取得する
     *
     * 姓と名を結合して取得
     * ニックネームがある場合にはニックネームを優先する
     *
     * @param EntityInterface $user ユーザーデータ
     * @return string $userName ユーザー名
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getUserName($user)
    {
        if(!$user) return '';
        return $user->getDisplayName();
    }

    /**
     * JavaScript に、翻訳データを引き渡す
     * `bcI18n.キー名` で参照可能
     * （例）bcI18n.alertMessage
     *
     * @param array $value 値（連想配列）
     * @checked
     * @unitTest
     * @noTodo
     */
    public function i18nScript($data, $options = [])
    {
        return $this->BcHtml->i18nScript($data, $options);
    }

    /**
     * セッションに保存したメッセージを出力する
     *
     * メールフォームのエラーメッセージ等を出力します。
     *
     * @param string $key 出力するメッセージのキー（初期状態では省略可）
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function flash($key = 'flash'): void
    {
        $session = $this->_View->getRequest()->getSession();
        $sessionMessageList = $session->read('Flash');
        if ($sessionMessageList) {
            $this->element('wrap_flash', [
                'key' => $key,
                'sessionMessageList' => $sessionMessageList
            ]);
        }
    }

    /**
     * 表示しているページのコンテンツタイトルを取得する
     *
     * コンテンツタイトルは、BcBaserHelper->setTitle() でセットする
     *
     * @return string コンテンツタイトル
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsTitle()
    {
        return $this->_View->fetch('title');
    }

    /**
     * コンテンツを特定する文字列を出力する
     *
     * URL を元に、第一階層までの文字列をキャメルケースで取得する
     * ※ 利用例、出力例については BcBaserHelper::getContentsName() を参照
     *
     * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで出力する（初期値 : false）
     * @param array $options オプション（初期値 : array()）
     *    ※ オプションの詳細については、BcBaserHelper::getContentsName() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function contentsName($detail = false, $options = [])
    {
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
     *    - `home` : トップページの場合に出力する文字列（初期値 : Home）
     *    - `default` : ルート直下の下層ページの場合に出力する文字列（初期値 : Default）
     *    - `error` : エラーページの場合に出力する文字列（初期値 : Error）
     *  - `underscore` : キャメルケースではなく、アンダースコア区切りで出力する（初期値 : false）
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     */
    public function getContentsName($detail = false, $options = [])
    {
        $options = array_merge([
            'home' => 'Home',
            'default' => 'Default',
            'error' => 'Error',
            'underscore' => false
        ], $options);

        $home = $options['home'];
        $default = $options['default'];
        $error = $options['error'];
        $underscore = $options['underscore'];
        $prefix = $plugin = $url0 = $url1 = $url2 = '';
        $pass = $aryUrl = [];

        $request = $this->getView()->getRequest();
        if (!empty($request->getParam('prefix'))) $prefix = h($request->getParam('prefix'));
        if (!empty($request->getParam('plugin'))) $plugin = h($request->getParam('plugin'));
        $controller = h($request->getParam('controller'));
        if ($prefix) {
            $action = str_replace($prefix . '_', '', h($request->getParam('action')));
        } else {
            $action = h($request->getParam('action'));
        }
        if (!empty($request->getParam('pass'))) {
            foreach($request->getParam('pass') as $key => $value) {
                if($key !== '?') $pass[$key] = h($value);
            }
        }

        $url = explode('/', h($request->getPath()));

        // $url[0]がnullの場合はずらす
        if(empty($url[0])) array_shift($url);

        if (!empty($request->getAttribute('currentSite')->alias)) array_shift($url);
        if (isset($url[0])) $url0 = $url[0];
        if (isset($url[1])) $url1 = $url[1];
        if (isset($url[2])) $url2 = $url[2];

        // 固定ページの場合
        if (!BcUtil::isAdminSystem()) {
            $pageUrl = h($request->getPath());
            if ($pageUrl === false) $pageUrl = '/';

            $sitePrefix = $this->getSitePrefix();
            if ($sitePrefix) {
                $pageUrl = preg_replace('/^\/' . preg_quote($sitePrefix, '/') . '\//', '/', $pageUrl);
            }
            if (preg_match('/\/$/', $pageUrl)) $pageUrl .= 'index';
            $pageUrl = preg_replace('/\.html$/', '', $pageUrl);
            $pageUrl = preg_replace('/^\//', '', $pageUrl);
            $aryUrl = explode('/', $pageUrl);
        } else {
            // プラグインルーティングの場合
            if ((($url1 == '' && in_array($action, ['index', 'mobile_index', 'smartphone_index'])) || ($url1 == $action)) && $url2 != $action && $plugin) {
                $prefix = $plugin = '';
                $controller = $url0;
            }
            if ($plugin) $controller = $plugin . '_' . $controller;
            if ($prefix) $controller = $prefix . '_' . $controller;
            if ($controller) $aryUrl[] = $controller;
            if ($action) $aryUrl[] = $action;
            if ($pass) $aryUrl = array_merge($aryUrl, $pass);
        }

        if ($this->getView()->getName() == 'CakeError') {
            $contentsName = $error;
        } elseif (count($aryUrl) >= 2) {
            if (!$detail) {
                $contentsName = $aryUrl[0];
            } else {
                $contentsName = implode('_', $aryUrl);
            }
        } elseif (count($aryUrl) == 1 && $aryUrl[0] == 'index') {
            $contentsName = $home;
        } else {
            if (!$detail) {
                $contentsName = $default;
            } else {
                $contentsName = $aryUrl[0];
            }
        }

        if ($underscore) {
            $contentsName = Inflector::underscore($contentsName);
        } else {
            $contentsName = str_replace('-', '_', $contentsName);
            $contentsName = Inflector::camelize($contentsName);
        }

        return $contentsName;
    }

    /**
     * baserCMSの設置フォルダを考慮したURLを取得する
     *
     * 《利用例》
     * <a href="<?php echo $this->BcBaser->getUrl('/about') ?>">会社概要</a>
     *
     * @param mixed $url baserCMS設置フォルダからの絶対URL、もしくは配列形式のURL情報
     *        省略した場合には、PC用のトップページのURLを取得する
     * @param bool $full httpから始まるURLを取得するかどうか
     * @return string URL
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getUrl($url = null, $full = false, $options = [])
    {
        $options = array_merge([
            'fullBase' => $full
        ], $options);
        return $this->Url->build($url, $options);
    }

    /**
     * タイトルを設定する
     *
     * @param string $title タイトル
     * @param mixed $categoryTitleOn カテゴリのタイトルを含むかどうか
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTitle($title, $categoryTitleOn = null)
    {
        if (!is_null($categoryTitleOn)) {
            $this->_categoryTitleOn = $categoryTitleOn;
        }
        $this->getView()->assign('title', $title);
    }

    /**
     * meta タグのキーワードを設定する
     *
     * @param string $keywords キーワード（複数の場合はカンマで区切る）
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function setKeywords($keywords)
    {
        $this->_View->set('keywords', $keywords);
    }

    /**
     * meta タグの説明文を設定する
     *
     * @param string $description 説明文
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function setDescription($description)
    {
        $this->_View->set('description', $description);
    }

    /**
     * レイアウトで利用する為の変数を設定する
     *
     * View::set() のラッパー
     *
     * @param string $key 変数名
     * @param mixed $value 値
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function set($key, $value)
    {
        $this->_View->set($key, $value);
    }

    /**
     * タイトルとパンくずへのカテゴリタイトルの出力有無を設定する
     *
     * コンテンツごとに個別設定をする為に利用する。
     * パンくずにも影響する。
     *
     * @param bool|string|array $on true を指定した場合は、コントローラーで指定した crumbs を参照し、
     *        文字列を指定した場合には、その文字列をカテゴリとして利用する。
     *        パンくずにリンクをつける場合には、配列で指定する。
     *        （例） array('name' => '会社案内', 'url' => '/company/index')
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCategoryTitle($on = true)
    {
        $this->_categoryTitle = $on;
    }

    /**
     * meta タグ用のキーワードを取得する
     *
     * @return string meta タグ用のキーワード
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getKeywords()
    {
        $keywords = $this->getView()->get('keywords');
        if (!empty($keywords)) {
            return $keywords;
        }
        $currentSite = $this->getView()->getRequest()->getAttribute('currentSite');
        return $currentSite->keyword ?? '';
    }

    /**
     * meta タグ用のページ説明文を取得する
     *
     * @return string meta タグ用の説明文
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDescription()
    {
        $description = $this->getView()->get('description');
        if (!empty($description)) {
            return $description;
        }
        if ($this->isHome()) {
            $currentSite = $this->getView()->getRequest()->getAttribute('currentSite');
            return $currentSite->description ?? '';
        }
        return '';
    }

    /**
     * タイトルタグを取得する
     *
     * ページタイトルと直属のカテゴリ名が同じ場合は、ページ名を省略する
     * version 3.0.10 より第2引数 $categoryTitleOn は、 $options にまとめられました。
     * 後方互換のために第2引数に配列型以外を指定された場合は、 $categoryTitleOn として取り扱います。
     *
     * @param string $separator 区切り文字
     * @param array $options
     *  `categoryTitleOn` カテゴリタイトルを表示するかどうか boolean で指定 (初期値 : null)
     *  `tag` (boolean) false でタグを削除するかどうか (初期値 : true)
     *  `allowableTags` tagが falseの場合、削除しないタグを指定できる。詳しくは、php strip_tags のドキュメントを参考してください。 (初期値 : '')
     * @return string メタタグ用のタイトルを返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitle($separator = '｜', $options = [])
    {
        if (!is_array($options)) {
            $categoryTitleOn = $options;
            unset($options);
            $options['categoryTitleOn'] = $categoryTitleOn;
        }

        $options = array_merge([
            'categoryTitleOn' => null,
            'tag' => true,
            'allowableTags' => ''
        ], $options);

        $title = [];

        if ($this->isHome()) {
            $homeTitle = $this->_View->get('homeTitle');
            if ($homeTitle) {
                if (!$options['tag']) {
                    $title[] = strip_tags($homeTitle, $options['allowableTags']);
                } else {
                    $title[] = $homeTitle;
                }
            }
        } else {
            $crumbs = $this->getCrumbs($options['categoryTitleOn']);
            if ($crumbs) {
                $crumbs = array_reverse($crumbs);
                foreach($crumbs as $key => $crumb) {
                    if ($this->BcArray->first($crumbs, $key) && isset($crumbs[$key + 1])) {
                        if ($crumbs[$key + 1]['name'] == $crumb['name']) {
                            continue;
                        }
                    }
                    if (!$options['tag']) {
                        $title[] = strip_tags($crumb['name'], $options['allowableTags']);
                    } else {
                        $title[] = $crumb['name'];
                    }
                }
            }
        }

        // サイトタイトルを追加
        $currentSite = $this->getView()->getRequest()->getAttribute('currentSite');
        $siteName = $currentSite->title ?? '';
        if ($siteName) {
            if (!$options['tag']) {
                $title[] = strip_tags($siteName, $options['allowableTags']);
            } else {
                $title[] = $siteName;
            }
        }

        return implode($separator, $title);
    }

    /**
     * パンくず用の配列を取得する
     *
     * 基本的には、コントローラーの crumbs プロパティで設定した値を取得する仕様だが
     * 事前に setCategoryTitle メソッドで出力内容をカスタマイズする事ができる
     *
     * @param mixed $categoryTitleOn 親カテゴリの階層を表示するかどうか
     * @return array パンくず用の配列
     * @todo
     * HTMLレンダリングも含めた状態で取得できる、HtmlHelper::getCrumbs() とメソッド名が
     * 同じで、 処理内容がわかりにくいので変数名のリファクタリング要。
     * ただし、BcBaserHelper::getCrumbs() は、テーマで利用されている可能性が高いので、
     * 後方互換を考慮する必要がある。
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getCrumbs($categoryTitleOn = null)
    {
        // ページカテゴリを追加
        if (!is_null($categoryTitleOn)) {
            $this->_categoryTitleOn = $categoryTitleOn;
        }

        // 親となるパンくずを取得
        $crumbs = [];
        if ($this->_categoryTitleOn) {
            // true の場合は、コントローラーで設定された crumbs を取得
            if ($this->_categoryTitle === true) {
                $crumbs = $this->_View->get('crumbs', []);

            } elseif (is_array($this->_categoryTitle)) {
                $crumbs[] = $this->_categoryTitle;

            } elseif ($this->_categoryTitle) {
                $crumbs[] = ['name' => $this->_categoryTitle, 'url' => ''];
            }
        }

        // カレントのページを追加
        $contentsTitle = $this->getContentsTitle();
        $useCurrentTitle = true;
        // インデックスページで親カテゴリとタイトルが被る場合は重複しないようにする
        if (!empty($this->_View->getRequest()->getAttribute('currentContent')) &&
            $this->_View->getRequest()->getAttribute('currentContent')->type !== 'ContentFolder' &&
            $this->_View->getRequest()->getAttribute('currentContent')->name === 'index' &&
            $this->_categoryTitleOn) {
            $parentTitle = '';
            if ($this->_categoryTitle === true && $crumbs) {
                $parentTitle = $crumbs[count($crumbs) - 1]['name'];
            } elseif ($this->_categoryTitle) {
                $parentTitle = $this->_categoryTitle;
            }
            if ($parentTitle === $contentsTitle) {
                $useCurrentTitle = false;
            }
        }
        if ($contentsTitle && $useCurrentTitle) {
            $crumbs[] = ['name' => $contentsTitle, 'url' => ''];
        }
        return $crumbs;
    }

    /**
     * コンテンツのタイトルを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function contentsTitle()
    {
        echo h($this->getContentsTitle());
    }

    /**
     * タイトルタグを出力する
     *
     * @param string $separator 区切り文字
     * @param string $categoryTitleOn カテゴリを表示するかどうか boolean で指定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function title($separator = '｜', $categoryTitleOn = null)
    {
        echo '<title>' . h($this->getTitle($separator, $categoryTitleOn)) . "</title>\n";
    }

    /**
     * キーワード用のメタタグを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function metaKeywords()
    {
        echo $this->BcHtml->meta('keywords', $this->getkeywords()) . "\n";
    }

    /**
     * ページ説明文用のメタタグを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function metaDescription()
    {
        echo $this->BcHtml->meta('description', strip_tags($this->getDescription())) . "\n";
    }

    /**
     * RSSフィードのリンクタグを出力する
     *
     * @param string $title RSSのタイトル
     * @param string $link RSSのURL
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rss($title, $link)
    {
        echo $this->BcHtml->meta($title, $link, ['type' => 'rss']) . "\n";
    }

    /**
     * 現在のページがトップページかどうかを判定する
     *
     * MEMO: BcRequest.(agent).aliasは廃止
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function isHome()
    {
        $request = $this->_View->getRequest();
        if (empty($request->getAttribute('currentSite'))) {
            return false;
        } else {
            $site = $request->getAttribute('currentSite');
            $path = $request->getUri()->getPath();
        }
        if (empty($site->alias) || $site->same_main_url || $site->use_subdomain) {
            // メインサイトの場合
            return $path === "/" || $path === "/index";
        } else {
            // サブサイトの場合
            return $path === "/$site->alias/" || $path === "/$site->alias/index";
        }
    }

    /**
     * ヘッダーテンプレートを出力する
     *
     * @param array $data エレメントで参照するデータ
     * @param array $options オプションのパラメータ
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     * ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function header($data = [], $options = [])
    {
        $options = array_merge([
            'subDir' => true
        ], $options);

        $out = $this->getElement('header', $data, $options);

        // EVENT header
        $event = $this->dispatchLayerEvent('header', [
            'out' => $out
        ], ['layer' => 'View', 'class' => '', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        // EVENT ControllerName.header
        $event = $this->dispatchLayerEvent('header', [
            'out' => $out
        ], ['layer' => 'View', 'class' => $this->getView()->getName()]);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        echo $out;
    }

    /**
     * フッターテンプレートを出力する
     *
     * @param array $data エレメントで参照するデータ
     * @param array $options オプションのパラメータ
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     * ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function footer($data = [], $options = [])
    {
        $options = array_merge([
            'subDir' => true
        ], $options);

        $out = $this->getElement('footer', $data, $options);

        /*** footer ***/
        $event = $this->dispatchLayerEvent('footer', [
            'out' => $out
        ], ['layer' => 'View', 'class' => '', 'plugin' => '']);
        if ($event) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        /*** Controller.footer ***/
        $event = $this->dispatchLayerEvent('footer', [
            'out' => $out
        ], ['layer' => 'View', 'class' => $this->getView()->getName()]);
        if ($event) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        echo $out;
    }

    /**
     * ページネーションを出力する
     *
     * @param string $name
     * @param array $data ページネーションで参照するデータ
     * @param array $options オプションのパラメータ
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     * ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     */
    public function pagination($name = 'default', $data = [], $options = [])
    {
        $options = array_merge([
            'subDir' => true
        ], $options);
        if (!$name) $name = 'default';
        echo $this->getElement('paginations' . DS . $name, $data, $options);
    }

    /**
     * コンテンツ本体を出力する
     *
     * レイアウトテンプレートで利用する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function content()
    {
        /*** contentHeader ***/
        $this->dispatchLayerEvent('contentHeader', null, ['layer' => 'View', 'class' => '', 'plugin' => '']);

        /*** Controller.contentHeader ***/
        $this->dispatchLayerEvent('contentHeader', null, ['layer' => 'View', 'class' => $this->getView()->getName()]);

        echo $this->getView()->fetch('content');

        /*** contentFooter ***/
        $this->dispatchLayerEvent('contentFooter', null, ['layer' => 'View', 'class' => '', 'plugin' => '']);

        /*** Controller.contentFooter ***/
        $this->dispatchLayerEvent('contentFooter', null, ['layer' => 'View', 'class' => $this->getView()->getName()]);
    }

    /**
     * コンテンツ内で設定した CSS や javascript をレイアウトテンプレートに出力し、ログイン中の場合、ツールバー用のCSSも出力する
     * また、テーマ用のCSSが存在する場合には出力する
     *
     * 利用する際は、</head>タグの直前あたりに記述する。
     * コンテンツ内で、レイアウトテンプレートへの出力を設定する場合には、inline オプションを false にする
     *
     * 《利用例》
     * $this->BcBaser->css('admin/layout', false);
     * $this->BcBaser->js('admin/startup', false);
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function scripts()
    {
        if (BcUtil::isInstalled() && !BcUtil::isAdminSystem() && $this->getView()->getName() !== 'Error') {
            echo BcSiteConfig::get('outer_service_output_header');
        }

        $currentPrefix = $this->BcAuth->getCurrentPrefix();
        $authPrefix = Configure::read('BcPrefixAuth.' . $currentPrefix);
        $toolbar = true;
        if (isset($authPrefix['toolbar'])) {
            $toolbar = $authPrefix['toolbar'];
        }

        // ### ツールバー用CSS出力
        // 《表示条件》
        // - プレビューでない
        // - auth prefix の設定で、利用するように定義されている
        // - Query String で、toolbar=false に定義されていない
        // - 管理画面でない
        // - ログインしている
        if (empty($this->getView()->get('preview')) && $toolbar) {
            if ($this->getView()->getRequest()->getQuery('toolbar') !== false && $this->getView()->getRequest()->getQuery('toolbar') !== 'false') {
                if ($currentPrefix !== 'Admin' && BcUtil::loginUser()) {
                    $this->css('admin/toolbar');
                }
            }
        }

        if (empty($this->getView()->get('preview')) && Configure::read('BcWidget.editLinkAtFront')) {
            if ($currentPrefix !== 'Admin' && BcUtil::loginUser()) {
                $this->css('admin/widget_link');
            }
        }

        if (BcUtil::isAdminSystem()) {
            $plugins = Plugin::loaded();
            if ($plugins) {
                foreach($plugins as $plugin) {
                    $cssName = 'admin/' . Inflector::underscore($plugin) . '_admin';
                    $path = Plugin::path($plugin) . 'webroot' . DS . 'css' . DS . $cssName . '.css';
                    if (file_exists($path)) {
                        $this->css($plugin . '.' . $cssName);
                    }
                }
            }
        }

        // ### テーマ用CSS出力
        // 《表示条件》
        // - インストーラーではない
        // - /files/theme_configs/config.css が存在する
        if (!BcUtil::isAdminSystem() && $this->getView()->getRequest()->getParam('controller') != 'installations' && file_exists(WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css')) {
            $this->css('/files/theme_configs/config');
        }

        $scripts = $this->_View->fetch('meta') . "\n" .
            $this->_View->fetch('css') . "\n" .
            $this->_View->fetch('script');

        if (Configure::read('BcApp.outputMetaGenerator')) {
            $scripts = "\n<meta name=\"generator\" content=\"basercms\"/>" . $scripts;
        }

        echo $scripts;
    }

    /**
     * ツールバーエレメントや CakePHP のデバッグ出力を表示
     *
     * 利用する際は、</body> タグの直前あたりに記述する。
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function func()
    {
        if (BcUtil::isInstalled() && !BcUtil::isAdminSystem()) {
            echo BcSiteConfig::get('outer_service_output_footer');
        }

        $currentPrefix = $this->BcAuth->getCurrentPrefix();
        $authPrefix = Configure::read('BcPrefixAuth.' . $currentPrefix);
        $toolbar = true;
        if ($authPrefix && isset($authPrefix['toolbar'])) $toolbar = $authPrefix['toolbar'];

        // ### ツールバーエレメント出力
        // 《表示条件》
        // - プレビューでない
        // - auth prefix の設定で、利用するように定義されている
        // - Query String で、toolbar=false に定義されていない
        // - 管理画面でない
        // - ログインしている
        if (empty($this->_View->get('preview')) && $toolbar) {
            if ($this->_View->getRequest()->getQuery('toolbar') !== false && $this->_View->getRequest()->getQuery('toolbar') !== 'false') {
                if ($currentPrefix !== 'Admin' && BcUtil::loginUser()) {
                    $adminTheme = Inflector::camelize(Configure::read('BcApp.coreAdminTheme'), '-');
                    $this->element($adminTheme . '.toolbar');
                }
            }
        }
    }

    /**
     * XMLヘッダタグを出力する
     *
     * @param array $attrib 属性
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function xmlHeader($attrib = [])
    {
        if (empty($attrib['encoding']) && !empty($this->getView()->getRequest()->getAttribute('currentSite')->device) && $this->_View->getRequest()->getAttribute('currentSite')->device == 'mobile') {
            $attrib['encoding'] = 'Shift-JIS';
        }
        echo $this->BcXml->header($attrib) . "\n";
    }

    /**
     * アイコン（favicon）タグを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function icon()
    {
        echo $this->BcHtml->meta('icon') . "\n";
    }

    /**
     * CSS タグを出力する
     *
     * 《利用例》
     * $this->BcBaser->css('admin/import')
     *
     * @param mixed $path CSSファイルのパス（css フォルダからの相対パス）拡張子は省略可
     * @param bool $inline コンテンツ内に css を出力するかどうか（初期値 : true）
     * @param mixed $options オプション
     * ※💣inline=false→block=trueに変更になったため注意
     * @return string|void
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     * @see https://book.cakephp.org/4/ja/views/helpers/html.html#css
     * ※ その他のパラメータについては、HtmlHelper::css() を参照。
     *
     * 下記のbasercms4系引数は残したまま
     * - 'inline'=trueを指定する (代替:$options['block']にnullが入る)
     * - 'inline'=falseを指定する (代替:$options['block']にtrueが入る)
     */
    public function css($path, $inline = true, $options = [])
    {
        if (!isset($options['block'])) {
            // @deprecated 6.0.0 since 5.1.0 後方互換用
            // >>>
            if(!$options && is_array($inline) && isset($inline['inline'])) {
                echo __d('baser_core', 'BcBaserHelper::css() にて、第２引数に配列でオプションを指定することは非推奨です。引数の仕様を見直してください。');
                $inline = $inline['inline'];
            }
            // <<<
            $options['block'] = $inline ? null : true;
        }
        echo $this->BcHtml->css($path, $options);
    }

    /**
     * SSL通信かどうか判定する
     *
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     * @doc
     */
    public function isSSL()
    {
        return $this->_View->getRequest()->is('https');
    }

    /**
     * charset メタタグを出力する
     *
     * モバイルの場合は、強制的に文字コードを Shift-JIS に設定
     *
     * @param string $charset 文字コード（初期値 : null）
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function charset($charset = null)
    {
        if (!$charset && !empty($this->_View->getRequest()->getAttribute('currentSite')->device) && $this->_View->getRequest()->getAttribute('currentSite')->device === 'mobile') {
            $charset = 'Shift-JIS';
        }
        echo $this->BcHtml->charset($charset);
    }

    /**
     * コピーライト用の年を出力する
     *
     * 《利用例》
     * $this->BcBaser->copyYear(2012)
     *
     * 《出力例》
     * 2012 - 2014
     *
     * @param int $begin 開始年
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copyYear($begin)
    {
        $year = date('Y');
        if ($begin == $year || !is_numeric($begin)) {
            echo $year;
            return;
        }
        echo $begin . ' - ' . $year;
    }

    /**
     * 現在のサイトのプレフィックスを取得する
     *
     * @return string|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSitePrefix(): string|false
    {
        if (!BcUtil::isInstalled()) return '';
        $site = $this->getView()->getRequest()->getAttribute('currentSite') ?? null;
        if(!$site) return '';
        /** @var SitesTable $sites */
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        return $sites->getPrefix($site->id);
    }

    /**
     * パンくずリストを出力する
     *
     * 事前に BcBaserHelper::addCrumb() にて、パンくず情報を追加しておく必要がある。
     * また、アクセス制限がかかっているリンクはテキストのみ表示する
     *
     * @param string $separator パンくずの区切り文字（初期値 : &raquo;）
     * @param string|bool $startText トップページを先頭に追加する場合にはトップページのテキストを指定する（初期値 : false）
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function crumbs($separator = '&raquo;', $startText = false, $onSchema = false)
    {
        $crumbs = $this->Breadcrumbs->getCrumbs();
        if (empty($crumbs)) {
            return;
        }
        if ($startText) {
            $homeUrl = '/';
            if (!empty($this->_View->getRequest()->getAttribute('currentSite')->alias)) {
                $homeUrl = '/' . $this->_View->getRequest()->getAttribute('currentSite')->alias . '/';
            } elseif (!empty($this->_View->getRequest()->getAttribute('currentSite')->name)) {
                $homeUrl = '/' . $this->_View->getRequest()->getAttribute('currentSite')->name . '/';
            }
            array_unshift($crumbs, [
                'title' => $startText,
                'url' => $homeUrl
            ]);
        }

        $out = [];
        if (!$onSchema) {
            foreach($crumbs as $crumb) {
                $options = ['escape' => false];
                if (!empty($crumb['options'])) {
                    $options = array_merge($options, $crumb['options']);
                }
                if (!empty($crumb['url'])) {
                    $out[] = $this->getLink($crumb['title'], $crumb['url'], $options);
                } else {
                    $out[] = $crumb['title'];
                }
            }
            $out = implode($separator, $out);
        } else {
            $out = $this->getElement('schema_crumbs', [
                'crumbs' => $crumbs,
                'separator' => $separator
            ]);
        }
        echo $out;
    }

    /**
     * パンくずリストの要素を追加する
     *
     * デフォルトでアクセス制限がかかっているリンクの場合でもタイトルを表示する
     * $options の forceTitle キー に false を指定する事で表示しない設定も可能
     *
     * @param string $name パンくず用のテキスト
     * @param mixed $link パンくず用のリンク（初期値 : null）※ 指定しない場合はリンクは設定しない
     * @param mixed $options リンクタグ用の属性（初期値 : array()）
     * ※ パラメータについては、HtmlHelper::link() を参照。
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function addCrumb($name, $link = null, $options = [])
    {
        $options = array_merge([
            'forceTitle' => true
        ], $options);
        $this->Breadcrumbs->add($name, $link, $options);
    }

    /**
     * ブラウザにキャッシュさせる為のヘッダーを出力する
     *
     * @param string|int|float $expire キャッシュの有効期間（初期値 : null） ※ 指定しない場合は、baserCMSコアのキャッシュ設定値
     * @param string $type どのタイプ(拡張子)に対してのキャッシュか（初期値 : 'html'）
     * @return void
     */
    public function cacheHeader($expire = null, $type = 'html')
    {
        $contentType = [
            'html' => 'text/html',
            'js' => 'text/javascript', 'css' => 'text/css',
            'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'
        ];
        $fileModified = filemtime(WWW_ROOT . 'index.php');

        if (!$expire) {
            $expire = Configure::read('BcCache.duration');
        }
        if (!is_numeric($expire)) {
            $expire = strtotime($expire);
        }
        header("Date: " . date("D, j M Y G:i:s ", $fileModified) . 'GMT');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $fileModified) . " GMT");
        header('Content-type: ' . $contentType[$type]);
        header("Expires: " . gmdate("D, j M Y H:i:s", time() + $expire) . " GMT");
        header('Cache-Control: max-age=' . $expire);
        // Firefoxの場合は不要↓
        //header("Cache-Control: cache");
        header("Pragma: cache");
    }

    /**
     * プロトコルから始まるURLを取得する
     *
     * 《利用例》
     * $this->BcBaser->getUri('/about')
     *
     * 《出力例》
     * http://localhost/about
     *
     * @param mixed $url 文字列のURL、または、配列形式のURL
     * @return string プロトコルから始まるURL
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUri($url)
    {
        if (is_string($url) && preg_match('/^http/is', $url)) {
            return $url;
        }
        if (empty($_SERVER['HTTPS'])) {
            $protocol = 'http';
        } else {
            $protocol = 'https';
        }
        return $protocol . '://' . Configure::read('BcEnv.host') . $this->getUrl($url, false);
    }

    /**
     * PluginBaserHelper を初期化する
     *
     * BcBaserHelperに定義されていないメソッドをプラグイン内のヘルパに定義する事で
     * BcBaserHelperから呼び出せるようになる仕組みを提供する。
     * プラグインのヘルパメソッドを BcBaserHelper 経由で直接呼び出せる為、
     * コア側のコントローラーでいちいちヘルパの定義をしなくてよくなり、
     * プラグインを導入するだけでテンプレート上でプラグインのメソッドが呼び出せるようになる。
     * 例えば固定ページ機能のWYSIWYG内にプラグインのメソッドを書き込む事ができる。
     *
     * 《PluginBaserHelper の命名規則》
     * {プラグイン名}BaserHelper
     *
     * 《利用例》
     * - BcBlogプラグインに BcBlogBaserHelper::blogPosts() が定義されている場合
     *        $this->BcBaser->blogPosts('news');
     *
     * @return void
     * @checked
     * @noTodo
     */
    protected function _initPluginBasers()
    {
        $plugins = BcUtil::getEnablePlugins();
        if($plugins) $plugins = Hash::extract(BcUtil::getEnablePlugins(), '{n}.name');
        if (!$plugins) return;
        foreach($plugins as $plugin) {
            $pluginName = Inflector::camelize($plugin);
            $className = $pluginName . '\\View\\Helper\\' . $pluginName . 'BaserHelper';
            if (class_exists($className) && is_a($className, BcPluginBaserHelperInterface::class, true)) {
                $this->_pluginBasers[$pluginName] = new $className($this->getView());
            }
        }
        $this->_pluginBasers['BaserCore'] = new BaserCoreBaserHelper($this->getView());
    }

    /**
     * PluginBaserHelper 用マジックメソッド
     *
     * BcBaserHelper に存在しないメソッドが呼ばれた際、プラグインで定義された PluginBaserHelper のメソッドを呼び出す
     * call__ から __call へメソット名を変更、Helper の __call をオーバーライド
     *
     * @param string $method メソッド名
     * @param array $params 引数
     * @return mixed|void PluginBaserHelper の戻り値
     * @checked
     * @noTodo
     */
    public function __call($method, $params)
    {
        foreach($this->_pluginBasers as $pluginBaser) {
            $methods = $pluginBaser->methods();
            if(!empty($methods[$method])) {
                if(!isset($methods[$method][0])) continue;
                if(!isset($methods[$method][1])) continue;
                $helper = $methods[$method][0];
                $target = $methods[$method][1];
                if(method_exists($pluginBaser->{$helper}, $target)) {
                    return call_user_func_array([$pluginBaser->{$helper}, $target], $params);
                }
            }
        }
    }

    /**
     * 文字列を検索しマークとしてタグをつける
     *
     * 《利用例》
     * $this->BcBaser->mark('強調', '強調します強調します強調します')
     *
     * 《取得例》
     * <strong>強調</strong>します<strong>強調</strong>します<strong>強調</strong>します
     *
     * @param string|array $search 検索文字列
     * @param string $text 検索対象文字列
     * @param string $name マーク用タグ（初期値 : strong）
     * @param array $attributes タグの属性（初期値 : array()）
     * @param bool $escape エスケープ有無（初期値 : false）
     * @return string $text 変換後文字列
     * @todo TextHelperに移行を検討
     * @checked
     * @noTodo
     * @unitTest
     */
    public function mark($search, $text, $name = 'strong', $attributes = [], $escape = false)
    {
        if (!is_array($search)) {
            $search = [$search];
        }
        $options = [
            'escape' => $escape
        ];
        if (!empty($attributes)) {
            $options = array_merge($options, $attributes);
        }
        foreach($search as $value) {
            $text = str_replace($value, $this->BcHtml->tag($name, $value, $options), $text);
        }
        return $text;
    }

    /**
     * コンテンツメニューを出力する
     *
     * ログインしていない場合はキャッシュする
     * contents_menu エレメントで、HTMLカスタマイズ可能
     *
     * @param mixed $id コンテンツID（初期値：null）
     * @param int $level 階層（初期値：null）※ null の場合は階層指定なし
     * @param string $currentId 現在のページのコンテンツID（初期値：null）
     * @return void
     * @doc
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function contentsMenu($id = null, $level = null, $currentId = null)
    {
        echo $this->getContentsMenu($id, $level, $currentId);
    }

    /**
     * メニューを出力する
     *
     * ログインしていない場合はキャッシュする
     * contents_menu エレメントで、HTMLカスタマイズ可能
     *
     * @param mixed $id コンテンツID（初期値：null）
     * @param int $level 階層（初期値：null）※ null の場合は階層指定なし
     * @param string $currentId 現在のページのコンテンツID（初期値：null）
     * @param array $options オプション（初期値 : []）
     *    - `tree` : ツリーデータを指定する
     *  - `currentId` : 現在表示しているページのID
     *    - `excludeIndex` : インデックスページを除外しない場合に false を指定
     *    - `cache` : キャッシュを有効にする場合に true を指定
     *    ※ その他のパラメータについては、View::element() を参照
     * @return string コンテンツメニュー
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getContentsMenu($id = null, $level = null, $currentId = null, $options = [])
    {
        if (!$id) {
            $siteRoot = $this->BcContents->getSiteRoot($this->_View->getRequest()->getAttribute('currentContent')->site_id);
            $id = $siteRoot->id;
        }
        $options = array_merge([
            'tree' => $this->BcContents->getTree($id, $level),
            'currentId' => $currentId,
            'excludeIndex' => true,
            'cache' => false,
            'element' => 'contents_menu',
        ], $options);
        if ($options['excludeIndex']) {
            $options['tree'] = $this->_unsetIndexInContentsMenu($options['tree']->toArray());
        }

        if (BcUtil::loginUser()) {
            unset($options['cache']);
        } else {
            if ($options['cache'] === false) {
                unset($options['cache']);
            } else {
                $options = array_merge($options, [
                        'cache' => [
                            'time' => Configure::read('BcCache.duration'),
                            'key' => $id]]
                );
            }
        }

        return $this->getElement($options['element'], $options);
    }

    /**
     * コンテンツメニューにおいてフォルダ内の index ページを除外する
     *
     * @param array $contents コンテンツデータ
     * @param bool $children 子かどうか
     * @return mixed コンテンツデータ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function _unsetIndexInContentsMenu($contents, $children = false)
    {
        if ($contents) {
            foreach($contents as $key => $content) {
                if ($children && $content->type !== 'ContentFolder' && $content->name === 'index') {
                    unset($contents[$key]);
                }
                if ($content['children']) {
                    $contents[$key]['children'] = $this->_unsetIndexInContentsMenu($content['children'], true);
                }
            }
        }
        return $contents;
    }

    /**
     * グローバルメニューを出力する
     *
     * @param array $level 取得する階層（初期値 : 1）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function globalMenu($level = 1, $options = [])
    {
        echo $this->getGlobalMenu($level, $options);
    }

    /**
     * グローバルメニューを取得する
     *
     * @param array $level 取得する階層（初期値 : 1）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return string
     * @checked
     * @noTodo
     */
    public function getGlobalMenu($level = 5, $options = [])
    {
        $siteId = 1;
        if (!empty($this->_View->getRequest()->getAttribute('currentContent')->site_id)) {
            $siteId = $this->_View->getRequest()->getAttribute('currentContent')->site_id;
        }
        $siteRoot = $this->BcContents->getSiteRoot($siteId);
        $id = ($siteRoot) ? $siteRoot->id : 1;
        $currentId = 1;
        if (!empty($this->_View->getRequest()->getAttribute('currentContent')->id)) {
            $currentId = $this->_View->getRequest()->getAttribute('currentContent')->id;
        }
        $options = array_merge([
            'tree' => $this->BcContents->getTree($id, $level, ['siteId' => $siteId]),
            'currentId' => $currentId,
            'data' => [],
            'cache' => false
        ], $options);

        if (BcUtil::loginUser()) {
            unset($options['cache']);
        } else {
            if ($options['cache'] === false) {
                unset($options['cache']);
            } else {
                $options = array_merge($options, [
                        'cache' => [
                            'time' => Configure::read('BcCache.duration'),
                            'key' => $id]]
                );
            }
        }

        $data = array_merge([
            'tree' => $options['tree'],
            'currentId' => $options['currentId']
        ], $options['data']);
        unset($options['tree'], $options['currentId'], $options['data']);
        return $this->getElement('global_menu', $data, $options);
    }

    /**
     * サイトマップを出力する
     *
     * ログインしていない場合はキャッシュする
     *
     * @param int $siteId サイトID
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップする
     */
    public function sitemap($siteId = 0)
    {
        echo $this->getSitemap($siteId);
    }

    /**
     * サイトマップを取得する
     *
     * ログインしていない場合はキャッシュする
     *
     * @param int $siteId サイトID
     * @return string サイトマップ
     * @checked
     * @noTodo
     */
    public function getSitemap($siteId = 0)
    {
        /** @var SitesTable $sites */
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $contentId = $sites->getRootContentId($siteId);
        return $this->getContentsMenu($contentId);
    }

    /**
     * 現在のページが固定ページかどうかを判定する
     *
     * @return bool 固定ページの場合は true を返す
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function isPage()
    {
        $request = $this->_View->getRequest();
        return ($request->getParam('controller') === 'Pages' && $request->getParam('action') == 'view');
    }

    /**
     * 現在のページの純粋なURLを取得する
     *
     * スマートURL、サブフォルダかどうかに依存しない、スラッシュから始まるURLを取得
     *
     * @return string URL
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getHere()
    {
        return '/' . preg_replace('/^\//', '', $this->_View->getRequest()->getPath());
    }

    /**
     * 現在のページがページカテゴリのトップかどうかを判定する
     * 判定は、URLからのみで行う
     *
     * @return bool カテゴリトップの場合は、 true を返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isCategoryTop()
    {
        $url = $this->getHere();
        $url = preg_replace('/^\//', '', $url);
        if (preg_match('/\/$/', $url)) {
            $url .= 'index';
        }
        if (preg_match('/\/index$/', $url)) {
            $param = explode('/', $url);
            if (count($param) >= 2) {
                return true;
            }
        }
        return false;
    }

    /**
     * 固定ページをエレメントとして読み込む
     *
     * ※ レイアウトは読み込まずコンテンツ本体のみを読み込む
     *
     * @param string $url 固定ページのURL
     * @param array $params 固定ページに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    - `subDir` : テンプレートの配置場所についてプレフィックスに応じたサブフォルダを利用するかどうか（初期値 : true）
     *    - `recursive` : 固定ページ読み込みを再帰的に読み込むかどうか（初期値 : true）
     *    - `checkExists` : 固定ページの存在判定をするかどうか（初期値 : true）
     * @return void
     * @checked
     * @noTodo
     */
    public function page($url, $params = [], $options = [])
    {
        if(
            in_array('pageRecursive', $this->getView()->getVars())
            && !$this->getView()->get('pageRecursive')
        ) {
            return;
        }

        $options = array_merge([
            'subDir' => true,
            'recursive' => true,
            'checkExists' => true
        ], $options);

        $this->getView()->set('pageRecursive', $options['recursive']);

        $content = $this->BcContents->getContentByUrl($url, 'Page');
        // 該当URLページの存在確認
        if (!$content) {
            if($options['checkExists']) {
                throw new NotFoundException('ページ「' . $url . '」が存在しません。');
            } else {
                return;
            }
        }

        $pagesService = $this->getService(PagesServiceInterface::class);
        try {
            $page = $pagesService->get($content->entity_id);
        } catch (RecordNotFoundException) {
            return;
        }

        // urlを取得
        if (empty($this->getView()->subDir)) {
            $url = '/../Pages/' . $page->page_template;
        } else {
            $url = '../Pages/' . $page->page_template;
        }
        $page->content = $content;
        $params['page'] = $page;
        $this->element($url, $params, ['subDir' => $options['subDir']]);
    }

    /**
     * 指定したURLが現在のURLと同じかどうか判定する
     *
     * 《比較例》
     * /news/ | /news/ ・・・○
     * /news | /news/ ・・・×
     * /news/ | /news/index ・・・○
     *
     * @param string $url 比較対象URL
     * @return bool 同じ場合には true を返す
     * @checked
     * @noTodo
     */
    public function isCurrentUrl($url)
    {
        $pattern = '/\/$/';
        $shortenedUrl = preg_replace($pattern, '/index', $this->getUrl($url));
        $shortenedHere = preg_replace($pattern, '/index', $this->_View->getRequest()->getAttribute('here'));
        return ($shortenedUrl === $shortenedHere);
    }

    /**
     * 現在のテーマのURLを取得する
     *
     * @return string テーマのURL
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getThemeUrl()
    {
        return $this->_View->getRequest()->getAttribute('base') . '/' . Inflector::underscore($this->getView()->getTheme()) . '/';
    }

    /**
     * 現在のテーマのURLを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function themeUrl()
    {
        echo $this->getThemeUrl();
    }

    /**
     * ベースとなるURLを取得する
     *
     * サブフォルダやスマートURLについて考慮されている事が前提
     *
     * @return string ベースURL
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getBaseUrl()
    {
        return $this->_View->getRequest()->getAttribute('base') . '/';
    }

    /**
     * ベースとなるURLを出力する
     *
     * サブフォルダやスマートURLについて考慮されている事が前提
     *
     * @return void
     * @checked
     * @noTodo
     * @UnitTest ラッパーメソッドに付きテスト不要
     */
    public function baseUrl()
    {
        echo $this->getBaseUrl();
    }

    /**
     * コンテンツナビを出力する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function contentsNavi($data = [], $options = [])
    {
        $this->element('contents_navi', $data, $options);
    }

    /**
     * パンくずリストを出力する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function crumbsList($data = [], $options = [])
    {
        $data = array_merge([
            'onSchema' => false
        ], $data);
        $this->element('crumbs', $data, $options);
    }

    /**
     * Google Analytics のトラッキングコードを出力する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     */
    public function googleAnalytics($data = [], $options = [])
    {
        $data = array_merge([
            'googleAnalyticsId' => (string) BcSiteConfig::get('google_analytics_id')
        ], $data);
        $this->element('google_analytics', $data, $options);
    }

    /**
     * Google Maps を出力する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @return void
     * @noTodo
     * @checked
     * @unitTest ラッパーメソッドに付きテスト不要
     */
    public function googleMaps($data = [])
    {
        echo $this->getGoogleMaps($data);
    }

    /**
     * Google Maps を取得する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @return string
     * @noTodo
     * @checked
     * @unitTest ラッパーメソッドに付きテスト不要
     */
    public function getGoogleMaps($data = [])
    {
        try {
            return $this->BcGoogleMaps->load($data);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * 表示件数設定機能を出力する
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function listNum($data = [], $options = [])
    {
        $this->element('list_num', $data, $options);
    }

    /**
     * サイト内検索フォームを出力
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return void
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップする
     */
    public function siteSearchForm($data = [], $options = [])
    {
        echo $this->getSiteSearchForm($data, $options);
    }

    /**
     * サイト内検索フォームを取得
     *
     * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
     * @param array $options オプション（初期値 : array()）
     *    ※ その他のパラメータについては、View::element() を参照
     * @return string
     * @checked
     * @noTodo
     */
    public function getSiteSearchForm($data = [], $options = [])
    {
        return $this->getElement('site_search_form', $data, $options);
    }

    /**
     * Webサイト名を出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function siteName()
    {
        echo $this->getSiteName();
    }

    /**
     * Webサイト名を取得する
     *
     * @return string サイト基本設定のWebサイト名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteName()
    {
        /** @var Site $site */
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        if(!$site) return '';
        return $site->display_name;
    }

    /**
     * WebサイトURLを出力する
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function siteUrl()
    {
        echo $this->getSiteUrl();
    }

    /**
     * WebサイトURLを取得する
     *
     * @return string サイト基本設定のWebサイト名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteUrl()
    {
        return Configure::read('BcEnv.siteUrl');
    }

    /**
     * パラメータ情報を取得する
     *
     * @return array パラメータ情報の配列
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getParams()
    {
        $attributes = $this->_View->getRequest()->getAttributes();
        return $attributes['params'];
    }

    /**
     * URL情報を取得する
     *
     * @return array URL情報の配列
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUrlParams()
    {
        $attributes = $this->_View->getRequest()->getAttributes();
        return [
            'url' => $this->getUrl(null, true),
            'here' => $attributes['here'],
            'path' => $this->_View->getRequest()->getPath(),
            'webroot' => $attributes['webroot'],
            'base' => $attributes['base'],
            'query' => $this->_View->getRequest()->getQueryParams(),
        ];
    }

    /**
     * 現在のサイトプレフィックスを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentPrefix()
    {
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        if (!$site) {
            return '';
        }
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        return $sites->getPrefix($site->id);
    }

    /**
     * コンテンツ作成日を取得
     * @return null|string
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getContentCreatedDate($format = 'Y/m/d H:i')
    {
        $content = $this->getCurrentContent();
        if (!empty($content['created_date'])) {
            return date($format, strtotime($content['created_date']));
        } else {
            return '';
        }
    }

    /**
     * コンテンツ更新日を取得
     *
     * @param string $format
     * @return null|string
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getContentModifiedDate($format = 'Y/m/d H:i')
    {
        $content = $this->getCurrentContent();
        if (!empty($content['modified_date'])) {
            return date($format, strtotime($content['modified_date']));
        } else {
            return '';
        }
    }

    /**
     * 更新情報を出力する
     * @checked
     * @noTodo
     */
    public function updateInfo(): void
    {
        echo $this->getUpdateInfo();
    }

    /**
     * 更新情報を取得する
     * @checked
     * @noTodo
     * @unitTest
     * @doc
     */
    public function getUpdateInfo()
    {
        return $this->getElement('update_info', [
            'createdDate' => $this->getContentCreatedDate(),
            'modifiedDate' => $this->getContentModifiedDate()
        ]);
    }

    /**
     * 関連サイトのリンク一覧を取得
     *
     * @param int $id コンテンツID
     * @checked
     * @noTodo
     */
    public function getRelatedSiteLinks($id = null, $excludeIds = [])
    {
        $options = [];
        if ($excludeIds) {
            $options['excludeIds'] = $excludeIds;
        }
        $links = $this->BcContents->getRelatedSiteLinks($id, $options);
        return $this->getElement('related_site_links', ['links' => $links]);
    }

    /**
     * 関連サイトのリンク一覧を表示
     *
     * @param int $id コンテンツID
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストはスキップする
     */
    public function relatedSiteLinks($id = null, $excludeIds = [])
    {
        echo $this->getRelatedSiteLinks($id, $excludeIds);
    }

    /**
     * After Render
     *
     * @param string $viewFile
     * @checked
     */
    public function afterRender(Event $event)
    {
        if (BcUtil::isAdminSystem()) {
            return;
        }
        if (empty($this->getView()->getRequest()->getAttribute('currentSite'))) {
            return;
        }
        // TODO ucmitz 未実装
        // >>>
        return;
        // <<<
        $this->setCanonicalUrl();
        $this->setAlternateUrl();
    }


    /**
     * setCanonicalUrl
     * PCサイトの場合
     *    - .html付：.htmlなしのカノニカルを出力
     *    - .html無：自身のカノニカルを出力
     * スマホサイトの場合
     *        - PCサイトが存在する場合、canonicalを出力
     * @checked
     */
    public function setCanonicalUrl()
    {
        $currentSite = $this->_View->getRequest()->getAttribute('currentSite');
        if (!$currentSite) return;

        $view = $this->getView();
        $request = $view->getRequest();
        if ($currentSite->device === 'smartphone') {
            $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
            /** @var Site $mainSite */
            $mainSite = $sites->getMainByUrl($request->getPath());
            $url = $mainSite->makeUrl(new ServerRequest(['url' => $this->BcContents->getPureUrl(
                $request->getPath(),
                $request->getAttribute('currentSite')->id
            )]));

        } else {
            $url = $request->getPath();
        }
        $url = preg_replace('/\.html$/', '', $url);
        $url = preg_replace('/\/page:1$/', '', $url);
        $url = preg_replace('/\\/index$/', '/', $url);
        $view->assign('meta',
            $this->BcHtml->meta('canonical',
                $this->getUrl($url, true),
                [
                    'rel' => 'canonical',
                    'type' => null,
                    'title' => null,
                    'inline' => false
                ]
            )
        );
    }

    /**
     * alternate タグ出力
     * スマホサイトが存在し、別URLの場合に出力する
     * @checked
     */
    public function setAlternateUrl()
    {

        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $subSite = $sites->getSubByUrl($this->_View->getRequest()->getPath(), false, BcAgent::find('smartphone'));
        if (!$subSite || $subSite->same_main_url) {
            return;
        }
        $url = $subSite->makeUrl(new CakeRequest($this->BcContents->getPureUrl(
            $this->_View->getRequest()->getPath(),
            $this->_View->getRequest()->getAttribute('currentSite')->id
        )));
        $this->_View->set('meta',
            $this->BcHtml->meta('alternate',
                $this->BcHtml->url($url, true),
                [
                    'rel' => 'alternate',
                    'media' => 'only screen and (max-width: 640px)',
                    'type' => null,
                    'title' => null,
                    'inline' => false
                ]
            )
        );
    }

    /**
     * トップページのタイトルをセットする
     *
     * @param string $title
     * @checked
     * @noTodo
     */
    public function setHomeTitle($title = null)
    {
        if (!$title) {
            $crumbs = $this->getCrumbs();
            if ($crumbs) {
                $crumbs = array_reverse($crumbs);
                $title = $crumbs[0]['name'];
            }
        }
        $this->_View->set('homeTitle', $title);
    }

    /**
     * スマートフォン用のウェブクリップアイコン用のタグを出力する
     *
     * @param string $fileName ファイル名（webroot に配置する事が前提）
     * @param bool $useGloss 光沢有無
     * @checked
     * @noTodo
     * @unitTest
     */
    public function webClipIcon($fileName = 'apple-touch-icon-precomposed.png', $useGloss = false)
    {
        if ($useGloss) {
            $rel = 'apple-touch-icon';
        } else {
            $rel = 'apple-touch-icon-precomposed';
        }
        echo '<link rel="' . $rel . '" href="' . Router::url('/' . $fileName, true) . '" />';
    }

    /**
     * コンテンツ管理用のURLより、正式なURLを取得する
     *
     * @param string $url コンテンツ管理用URLの元データ
     *    省略時は request より現在のデータを取得
     *    request が取得できない場合は、トップページのURLを設定
     * @param bool $full http からのフルのURLかどうか
     * @param bool $useSubDomain サブドメインを利用しているかどうか
     *    省略時は現在のサイト情報から取得する
     * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getContentsUrl($url = null, $full = false, $useSubDomain = null, $base = true)
    {
        if (!$url) {
            $url = $this->_View->getRequest()->getAttribute('currentContent')->url ?? '/';
        }
        if (is_null($useSubDomain)) {
            $site = $this->_View->getRequest()->getAttribute('currentSite');
            if($site) $useSubDomain = $site->use_subdomain;
        }
        return $this->BcContents->getUrl($url, $full, $useSubDomain, $base);
    }

    /**
     * Plugin 内の Baserヘルパを取得する
     *
     * @param string $name
     * @return bool|mixed Plugin 内の Baserヘルパ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPluginBaser($name)
    {
        if (!empty($this->_pluginBasers[$name])) {
            return $this->_pluginBasers[$name];
        }
        return false;
    }

    /**
     * 親フォルダを取得する
     *
     * - 引数なしで現在のコンテンツの親情報を取得
     * - $id を指定して取得する事ができる
     * - $direct を false に設定する事で、最上位までの親情報を取得
     *
     * @param int $id
     * @param bool $direct
     * @return mixed false|array
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function getParentFolder($id, $direct = true)
    {
        return $this->BcContents->getParent($id, $direct);
    }

    /**
     * エンティティIDからコンテンツの情報を取得
     *
     * @param string $contentType コンテンツタイプ
     * ('Page','MailContent','BlogContent','ContentFolder')
     * @param int $id エンティティID
     * @param string $field 取得したい値
     *  'name','url','title'など　初期値：Null
     *  省略した場合配列を取得
     * @return array or bool
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function getContentByEntityId($id, $contentType, $field = null)
    {
        return $this->BcContents->getContentByEntityId($id, $contentType, $field);
    }

    /**
     * IDがコンテンツ自身の親のIDかを判定する
     *
     * @param int|null $id コンテンツ自身のID
     * @param int $parentId 親として判定するID
     * @return bool
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function isContentsParentId($id, $parentId)
    {
        return $this->BcContents->isParentId($id, $parentId);
    }

    /**
     * JavaScript に変数を引き渡す
     *
     * @param string $variable 変数名（グローバル変数）
     * @param array $value 値（連想配列）
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function setScript($variable, $value)
    {
        return $this->BcHtml->scriptBlock($variable, $value);
    }

    /**
     * i18n 用の変数を宣言する
     * @return string
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function declarationI18n()
    {
        return $this->BcHtml->declarationI18n();
    }

    /**
     * 現在のページがコンテンツフォルダかどうか確認する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function isContentFolder()
    {
        return $this->BcContents->isFolder();
    }

    /**
     * プラグインがロードされているか判定する
     *
     * @param string $plugin
     * @return bool
     * @checked
     * @noTodo
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function isPluginLoaded(string $plugin): bool
    {
        return Plugin::isLoaded($plugin);
    }

    /**
     * デバッグモードかどうか
     *
     * @return bool
     * @checked
     * @noTodo
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function isDebug(): bool
    {
        return BcUtil::isDebug();
    }

    /**
     * フルURLに変換する
     *
     * @param string $url
     * @return string
     * @checked
     * @noTodo
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテスト不要
     */
    public function getFullUrl(string $url): string
    {
        return BcUtil::fullUrl($url);
    }

}
