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

namespace BaserCore\Controller;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use Cake\Core\Exception\Exception;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use BaserCore\Utility\BcUtil;
use BaserCore\Service\DblogsServiceInterface;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcAppController
 */
class BcAppController extends AppController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * サブディレクトリ
     *
     * @var        string
     * @access    public
     */
    public $subDir = null;

    /**
     * コンテンツタイトル
     *
     * @var string
     */
    public $contentsTitle = '';

    /**
     * プレビューフラグ
     *
     * @var bool
     */
    public $preview = false;

    /**
     * 管理画面テーマ
     *
     * @var string
     */
    public $adminTheme = null;

    /**
     * サイトデータ
     *
     * @var array
     */
    public $site = [];

    /**
     * コンテンツデータ
     *
     * @var array
     */
    public $content = [];

    /**
     * beforeFilter
     *
     * @return void
     * @checked
     * @note(value="マイルストーン２が終わってから確認する")
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        return;

        // 認証設定
        if (isset($this->BcAuthConfigure)) {
            $authConfig = [];
            if (!empty($this->request->getParam('prefix'))) {
                $currentAuthPrefix = $this->request->getParam('prefix');
            } else {
                $currentAuthPrefix = 'front';
            }
            $authPrefixSettings = Configure::read('BcPrefixAuth');
            foreach($authPrefixSettings as $key => $authPrefixSetting) {
                if (isset($authPrefixSetting['alias']) && $authPrefixSetting['alias'] == $currentAuthPrefix) {
                    $authConfig = $authPrefixSetting;
                    $authConfig['auth_prefix'] = $authPrefixSetting['alias'];
                    break;
                }
                if ($this->request->getParam('action') !== 'back_agent') {
                    if ($key == $currentAuthPrefix) {
                        $authConfig = $authPrefixSetting;
                        $authConfig['auth_prefix'] = $key;
                        break;
                    }
                }
            }
            if ($authConfig) {
                $this->BcAuthConfigure->setting($authConfig);
            } else {
                $this->BcAuth->setSessionKey('Auth.' . Configure::read('BcPrefixAuth.Admin.sessionKey'));
            }

            // =================================================================
            // ユーザーの存在チェック
            // ログイン中のユーザーを管理側で削除した場合、ログイン状態を削除する必要がある為
            // =================================================================
            $user = $this->BcAuth->user();
            if ($user && $authConfig && (empty($authConfig['type']) || $authConfig['type'] === 'Form')) {
                $userModel = $authConfig['userModel'];
                $User = ClassRegistry::init($userModel);
                if (strpos($userModel, '.') !== false) {
                    [$plugin, $userModel] = explode('.', $userModel);
                }
                if ($userModel && !empty($this->{$userModel})) {
                    $nameField = 'name';
                    if (!empty($authConfig['username'])) {
                        $nameField = $authConfig['username'];
                    }
                    $conditions = [
                        $userModel . '.id' => $user['id'],
                        $userModel . '.' . $nameField => $user[$nameField]
                    ];
                    if (isset($User->belongsTo['UserGroup'])) {
                        $UserGroup = ClassRegistry::init('UserGroup');
                        $userGroups = $UserGroup->find('all', ['conditions' => ['UserGroup.auth_prefix LIKE' => '%' . $authConfig['auth_prefix'] . '%'], 'recursive' => -1]);
                        $userGroupIds = Hash::extract($userGroups, '{n}.UserGroup.id');
                        $conditions[$userModel . '.user_group_id'] = $userGroupIds;
                    }
                    if (!$User->find('count', [
                        'conditions' => $conditions,
                        'recursive' => -1])) {
                        $this->Session->delete(BcAuthComponent::$sessionKey);
                    }
                }
            }
        }

    }

    /**
     * beforeRender
     *
     * @return    void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        // TODO ucmitz 未確認
        return;
        if (BcUtil::isAdminSystem()) {
            $this->__updateFirstAccess();
        } else {
            // テーマのヘルパーをセット
            if (BcUtil::isInstalled()) {
                $this->setThemeHelpers();
                // ショートコード
                App::uses('BcShortCodeEventListener', 'Event');
                CakeEventManager::instance()->attach(new BcShortCodeEventListener());
            }
        }

        // テンプレートの拡張子
        // RSSの場合、RequestHandlerのstartupで強制的に拡張子を.ctpに切り替えられてしまう為、
        // beforeRenderでも再設定する仕様にした
        $this->ext = Configure::read('BcApp.templateExt');

        // モバイルでは、mobileHelper::afterLayout をフックしてSJISへの変換が必要だが、
        // エラーが発生した場合には、afterLayoutでは、エラー用のビューを持ったviewクラスを取得できない。
        // 原因は、エラーが発生する前のcontrollerがviewを登録してしまっている為。
        // エラー時のview登録にフックする場所はここしかないのでここでviewの登録を削除する
        if ($this->name === 'CakeError') {
            ClassRegistry::removeObject('view');
            $this->response->disableCache();
        }

        $this->__loadDataToView();
        $this->set('isSSL', $this->request->is('ssl'));
        $this->set('safeModeOn', ini_get('safe_mode'));
        $this->set('baserVersion', BcUtil::getVersion());
        $this->set('widgetArea', BcSiteConfig::get('widget_area'));
    }

    /**
     * 初回アクセスメッセージ用のフラグを更新する
     *
     * @return void
     */
    private function __updateFirstAccess()
    {
        // 初回アクセスメッセージ表示設定
        if ($this->request->getParam('prefix') === "Admin" && !empty(BcSiteConfig::get('first_access'))) {
            $data = ['SiteConfig' => ['first_access' => false]];
            $SiteConfig = ClassRegistry::init('SiteConfig', 'Model');
            $SiteConfig->saveKeyValue($data);
        }
    }

    /**
     * NOT FOUNDページを出力する
     *
     * @return    void
     * @throws    NotFoundException
     */
    public function notFound()
    {
        throw new NotFoundException(__d('baser', '見つかりませんでした。'));
    }

    /**
     * View用のデータを読み込む。
     * beforeRenderで呼び出される
     *
     * @return    void
     */
    private function __loadDataToView()
    {
        $this->set('preview', $this->preview);

        if (!empty($this->request->getParam('prefix'))) {
            $currentPrefix = $this->request->getParam('prefix');
        } else {
            $currentPrefix = 'front';
        }

        $user = BcUtil::loginUser();
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');

        $authPrefix = Configure::read('BcPrefixAuth.' . $currentPrefix);
        if ($authPrefix) {
            $currentPrefixUser = BcUtil::loginUser($currentPrefix);
            if ($currentPrefixUser) {
                $user = $currentPrefixUser;
                $sessionKey = BcUtil::getLoginUserSessionKey();
            }
        }

        /* ログインユーザー */
        if (BcUtil::isInstalled() && $user && $this->name !== 'Installations' && !Configure::read('BcRequest.isUpdater') && !Configure::read('BcRequest.isMaintenance') && $this->name !== 'CakeError') {
            $this->set('user', $user);
        }

        $currentUserAuthPrefixes = [];
        if ($this->Session->check('Auth.' . $sessionKey . '.UserGroup.auth_prefix')) {
            $currentUserAuthPrefixes = explode(',', $this->Session->read('Auth.' . $sessionKey . '.UserGroup.auth_prefix'));
        }
        $this->set('currentUserAuthPrefixes', $currentUserAuthPrefixes);

        /* 携帯用絵文字データの読込 */
        // TODO 実装するかどうか検討する
        /* if (isset($this->request->getParam('prefix')) && $this->request->getParam('prefix') == 'mobile' && !empty($this->EmojiData)) {
          $emojiData = $this->EmojiData->find('all');
          $this->set('emoji',$this->Emoji->EmojiData($emojiData));
          } */
    }

    /**
     * CakePHPのバージョンを取得する
     *
     * @return string Baserバージョン
     */
    protected function getCakeVersion()
    {
        $versionFile = new File(CAKE_CORE_INCLUDE_PATH . DS . CAKE . 'VERSION.txt');
        $versionData = $versionFile->read();
        $lines = explode("\n", $versionData);
        $version = null;
        foreach($lines as $line) {
            if (preg_match('/^([0-9.]+)$/', $line, $matches)) {
                $version = $matches[1];
                break;
            }
        }
        if (!$version) {
            return false;
        }
        return $version;
    }

    /**
     * メールを送信する
     *
     * @param string $to 送信先アドレス
     * @param string $title タイトル
     * @param mixed $body 本文
     * @param array $options オプション
     *    - bool agentTemplate : テンプレートの配置場所についてサイト名をサブフォルダとして利用するかどうか（初期値：true）
     * @return bool 送信結果
     */
    public function sendMail($to, $title = '', $body = '', $options = [])
    {
        $dbg = debug_backtrace();
        if (!empty($dbg[1]['function']) && $dbg[1]['function'] === 'invokeArgs') {
            $this->notFound();
        }
        $options = array_merge([
            'agentTemplate' => true,
            'template' => 'default'
        ], $options);

        /*** Controller.beforeSendEmail ***/
        $event = $this->dispatchLayerEvent('beforeSendMail', [
            'options' => $options
        ]);
        if ($event !== false) {
            $this->request = $this->request->withParsedBody($event->getResult() === true? $event->getData('data') : $event->getResult());
            if (!empty($event->getData('options'))) {
                $options = $event->getData('options');
            }
        }

        if (BcSiteConfig::get('smtp_host')) {
            $transport = 'Smtp';
            $host = BcSiteConfig::get('smtp_host');
            $port = (BcSiteConfig::get('smtp_port'))? BcSiteConfig::get('smtp_port') : 25;
            $username = (BcSiteConfig::get('smtp_user'))? BcSiteConfig::get('smtp_user') : null;
            $password = (BcSiteConfig::get('smtp_password'))? BcSiteConfig::get('smtp_password') : null;
            $tls = BcSiteConfig::get('smtp_tls') && (BcSiteConfig::get('smtp_tls') == 1);
        } else {
            $transport = 'Mail';
            $host = 'localhost';
            $port = 25;
            $username = null;
            $password = null;
            $tls = null;
        }

        $config = [
            'transport' => $transport,
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'tls' => $tls
        ];

        /**
         * CakeEmailでは、return-path の正しい設定のためには additionalParameters を設定する必要がある
         * @url http://norm-nois.com/blog/archives/2865
         */
        if (!BcSiteConfig::get('mail_additional_parameters')) {
            $config = Hash::merge($config, ['additionalParameters' => BcSiteConfig::get('mail_additional_parameters')]);
        }
        if (!empty($options['additionalParameters'])) {
            $config = Hash::merge($config, ['additionalParameters' => $options['additionalParameters']]);
        }
        $cakeEmail = new CakeEmail($config);

        // charset
        if (!empty(BcSiteConfig::get('mail_encode'))) {
            $encode = BcSiteConfig::get('mail_encode');
        } else {
            $encode = 'UTF-8';
        }

        // ISO-2022-JPの場合半角カナが文字化けしてしまうので全角に変換する
        if ($encode === 'ISO-2022-JP') {
            $title = mb_convert_kana($title, 'KV', 'UTF-8');
            if (is_string($body)) {
                $body = mb_convert_kana($body, 'KV', 'UTF-8');
            } elseif (isset($body['message']) && is_array($body['message'])) {
                foreach($body['message'] as $key => $val) {
                    if (is_string($val)) {
                        $body['message'][$key] = mb_convert_kana($val, 'KV', 'UTF-8');
                    }
                }
            }
        }

        //CakeEmailの内部処理のencodeを統一したいので先に値を渡しておく
        $cakeEmail->headerCharset($encode);
        $cakeEmail->charset($encode);

        //$format
        if (!empty($options['format'])) {
            $cakeEmail->emailFormat($options['format']);
        } else {
            $cakeEmail->emailFormat('text');
        }

        //bcc 'mail@example.com,mail2@example.com'
        if (!empty($options['bcc'])) {
            // 文字列の場合
            $bcc = [];
            if (is_string($options['bcc'])) {
                if (strpos($options['bcc'], ',') !== false) {
                    $bcc = explode(',', $options['bcc']);
                } else {
                    $bcc[] = $options['bcc'];
                }
                // 配列の場合
            } elseif (is_array($options['bcc'])) {
                $bcc = $options['bcc'];
            }
            foreach($bcc as $val) {
                if (Validation::email(trim($val))) {
                    $cakeEmail->addBcc(trim($val));
                }
            }
            unset($bcc);
        }

        //cc 'mail@example.com,mail2@example.com'
        if (!empty($options['cc'])) {
            // 文字列の場合
            $cc = [];
            if (is_string($options['cc'])) {
                if (strpos($options['cc'], ',') !== false) {
                    $cc = explode(',', $options['cc']);
                } else {
                    $cc[] = $options['cc'];
                }
                // 配列の場合
            } elseif (is_array($options['cc'])) {
                $cc = $options['cc'];
            }
            foreach($cc as $val) {
                if (Validation::email(trim($val))) {
                    $cakeEmail->addCc($val);
                }
            }
            unset($cc);
        }

        $toAddress = null;
        try {
            // to 送信先アドレス (最初の1人がTOで残りがBCC)
            if (strpos($to, ',') !== false) {
                $_to = explode(',', $to);
                $i = 0;
                if (count($_to) >= 1) {
                    foreach($_to as $val) {
                        if ($i == 0) {
                            $cakeEmail->addTo($val);
                            $toAddress = $val;
                        } else {
                            $cakeEmail->addBcc($val);
                        }
                        ++$i;
                    }
                }
            } else {
                $cakeEmail->addTo($to);
            }
        } catch (Exception $e) {
            $this->BcMessage->setError($e->getMessage() . ' ' . __d('baser', '送信先のメールアドレスが不正です。'));
            return false;
        }

        // 件名
        $cakeEmail->subject($title);

        //From
        $from = '';
        if (!empty($options['from'])) {
            $from = $options['from'];
        } else {
            if (BcSiteConfig::get('email')) {
                $from = BcSiteConfig::get('email');
                if (strpos($from, ',') !== false) {
                    $from = explode(',', $from);
                }
            } else {
                $from = $toAddress;
            }
        }

        if (!empty($options['fromName'])) {
            $fromName = $options['fromName'];
        } else {
            if (!empty(BcSiteConfig::get('formal_name'))) {
                $fromName = BcSiteConfig::get('formal_name');
            } else {
                $fromName = Configure::read('BcApp.title');
            }
        }

        try {
            $cakeEmail->from($from, $fromName);
        } catch (Exception $e) {
            $this->setMessage($e->getMessage() . ' ' . __d('baser', '送信元のメールアドレスが不正です。'), true, false, true);
            return false;
        }

        //Reply-To
        if (!empty($options['replyTo'])) {
            $replyTo = $options['replyTo'];
        } else {
            $replyTo = $from;
        }
        $cakeEmail->replyTo($replyTo);

        //Return-Path
        if (!empty($options['returnPath'])) {
            $returnPath = $options['returnPath'];
            $cakeEmail->returnPath($returnPath);
        }

        //$sender
        if (!empty($options['sender'])) {
            $cakeEmail->sender($options['sender']);
        }

        //$theme
        if ($this->theme) {
            $cakeEmail->theme($this->theme);
        }
        if (!empty($options['theme'])) {
            $cakeEmail->theme($options['theme']);
        }

        //viewRender (利用するviewクラスを設定する)
        $cakeEmail->viewRender('BcApp');

        //template
        if (!empty($options['template'])) {

            $subDir = $plugin = '';
            // インストール時にSiteは参照できない
            if ($options['agentTemplate'] && !empty($this->request->getAttribute('currentSite')->name)) {
                $subDir = $this->request->getAttribute('currentSite')->name;
            }

            [$plugin, $template] = pluginSplit($options['template']);

            if ($subDir) {
                $template = "{$subDir}/{$template}";
            }

            if (!empty($plugin)) {
                $template = "{$plugin}.{$template}";
            }

            if (!empty($options['layout'])) {
                $cakeEmail->template($template, $options['layout']);
            } else {
                $cakeEmail->template($template);
            }
            $content = '';
            if (is_array($body)) {
                $cakeEmail->viewVars($body);
            } else {
                $cakeEmail->viewVars(['body' => $body]);
            }
        } else {
            $content = $body;
        }

        // $attachments tmp file path
        $attachments = [];
        if (!empty($options['attachments'])) {
            if (!is_array($options['attachments'])) {
                $attachments = [$options['attachments']];
            } else {
                $attachments = $options['attachments'];
            }
        }
        $cakeEmail->attachments($attachments);

        try {
            $cakeEmail->send($content);
            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage());
            return false;
        }
    }

    /**
     * Select Text 用の条件を生成する
     *
     * @param string $fieldName フィールド名
     * @param mixed $values 値
     * @param array $options オプション
     * @return    array
     */
    protected function convertSelectTextCondition($fieldName, $values, $options = [])
    {
        $_options = ['type' => 'string', 'conditionType' => 'or'];
        $options = am($_options, $options);
        $conditions = [];

        if ($options['type'] === 'string' && !is_array($values)) {
            $values = explode(',', str_replace('\'', '', $values));
        }
        if (!empty($values) && is_array($values)) {
            foreach($values as $value) {
                $conditions[$options['conditionType']][] = [$fieldName . ' LIKE' => "%'" . $value . "'%"];
            }
        }
        return $conditions;
    }

    /**
     * BETWEEN 条件を生成
     *
     * @param string $fieldName フィールド名
     * @param mixed $value 値
     * @return array
     */
    protected function convertBetweenCondition($fieldName, $value)
    {
        if (strpos($value, '-') === false) {
            return false;
        }
        [$start, $end] = explode('-', $value);
        if (!$start) {
            $conditions[$fieldName . ' <='] = $end;
        } elseif (!$end) {
            $conditions[$fieldName . ' >='] = $start;
        } else {
            $conditions[$fieldName . ' BETWEEN ? AND ?'] = [$start, $end];
        }
        return $conditions;
    }

    /**
     * ランダムなパスワード文字列を生成する
     *
     * @param int $len 文字列の長さ
     * @return string パスワード
     */
    protected function generatePassword($len = 8)
    {
        srand((double)microtime() * 1000000);
        $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $password = "";
        while($len--) {
            $pos = rand(0, 61);
            $password .= $seed[$pos];
        }
        return $password;
    }

    /**
     * 認証完了後処理
     *
     * @param array $user 認証されたユーザー情報
     * @return    bool
     */
    public function isAuthorized($user)
    {

        if (!isset($user['UserGroup']['auth_prefix'])) {
            return true;
        }
        $authPrefix = explode(',', $user['UserGroup']['auth_prefix']);
        if (!empty($this->request->getParam('prefix'))) {
            $currentPrefix = $this->request->getParam('prefix');
        } else {
            $currentPrefix = 'front';
        }
        return (in_array($currentPrefix, $authPrefix));

    }

    /**
     * リクエストされた画面に対しての認証用ユーザーモデルを取得する
     *
     * @return mixed string Or false
     */
    protected function getUserModel()
    {
        if (!isset($this->BcAuth)) {
            return false;
        }
        if (!isset($this->BcAuth->authenticate['Form']['userModel'])) {
            return false;
        }

        return $this->BcAuth->authenticate['Form']['userModel'];
    }

    /**
     * Redirects to given $url, after turning off $this->autoRender.
     * Script execution is halted after the redirect.
     *
     * @param mixed $url A string or array-based URL pointing to another location within the app, or an absolute URL
     * @param int $status Optional HTTP status code (eg: 404)
     * @param bool $exit If true, exit() will be called after the redirect
     * @return void if $exit = false. Terminates script if $exit = true
     */
    public function redirect($url, int $status = 302): ?Response
    {
        // TODO 未確認のため代替措置
        /* >>>
        $url = addSessionId($url, true);
        <<< */

        // 管理システムでのURLの生成が CakePHP の標準仕様と違っていたので調整
        // ※ Routing.admin を変更した場合
        if (is_array($url)) {
            if (!isset($url['admin']) && $this->request->getParam('prefix') === "Admin") {
                $url['admin'] = true;
            } elseif (isset($url['admin']) && !$url['admin']) {
                unset($url['admin']);
            }
        }
        return parent::redirect($url, $status);
    }

    /**
     * Calls a controller's method from any location.
     *
     * @param mixed $url String or array-based url.
     * @param array $extra if array includes the key "return" it sets the AutoRender to true.
     * @return mixed Boolean true or false on success/failure, or contents
     *               of rendered action if 'return' is set in $extra.
     */
    public function requestAction($url, $extra = [])
    {
        // >>> CUSTOMIZE ADD 2011/12/16 ryuring
        // 管理システムやプラグインでのURLの生成が CakePHP の標準仕様と違っていたので調整
        // >>> CUSTOMIZE MODIFY 2012/1/28 ryuring
        // 配列でないURLの場合に、間違った値に書きなおされていたので配列チェックを追加
        if (is_array($url)) {
            if ((!isset($url['admin']) && $this->request->getParam('prefix') === "Admin") || !empty($url['admin'])) {
                $url['prefix'] = 'admin';
            }
            if (!isset($url['plugin']) && !empty($this->request->getParam('plugin'))) {
                $url['plugin'] = $this->request->getParam('plugin');
            }
        }
        // <<<
        return parent::requestAction($url, $extra);
    }

    /**
     * Internally redirects one action to another. Examples:
     *
     * setAction('another_action');
     * setAction('action_with_parameters', $parameter1);
     *
     * @param string $action The new action to be redirected to
     * @return mixed Returns the return value of the called action
     */
    public function setAction(string $action, ...$args)
    {
        // CUSTOMIZE ADD 2012/04/22 ryuring
        // >>>
        $_action = $this->request->getParam('action');
        // <<<

        $this->request->withParam('action', $action);
        $args = func_get_args();
        unset($args[0]);

        // CUSTOMIZE MODIFY 2012/04/22 ryuring
        // >>>
        //return call_user_func_array(array($this, $action), $args);
        // ---
        $return = call_user_func_array([$this, $action], $args);
        $this->request->withParam('action', $_action);
        return $return;
        // <<<
    }

    /**
     * テーマ用のヘルパーをセットする
     * 管理画面では読み込まない
     *
     * @return void
     */
    protected function setThemeHelpers()
    {
        if ($this->request->getParam('prefix') === "Admin") {
            return;
        }

        $themeHelpersPath = WWW_ROOT . 'theme' . DS . Configure::read('BcSite.theme') . DS . 'Helper';
        $Folder = new Folder($themeHelpersPath);
        $files = $Folder->read(true, true);
        if (empty($files[1])) {
            return;
        }

        foreach($files[1] as $file) {
            $file = str_replace('-', '_', $file);
            $this->helpers[] = Inflector::camelize(basename($file, 'Helper.php'));
        }
    }

    /**
     * Ajax用のエラーを出力する
     *
     * @param int $errorNo エラーのステータスコード
     * @param mixed $message エラーメッセージ
     * @return void
     */
    public function ajaxError($errorNo = 500, $message = '')
    {
        $this->response = $this->response->withStatus($errorNo);
        if (!$message) {
            return;
        }

        if (!is_array($message)) {
            return;
        }

        $aryMessage = [];
        foreach($message as $value) {
            if (is_array($value)) {
                $aryMessage[] = implode('<br />', $value);
            } else {
                $aryMessage[] = $value;
            }
        }
        echo implode('<br />', $aryMessage);
        return;
    }

    /**
     * リクエストメソッドとトークンをチェックする
     *
     * - GETでのアクセスの場合 not found
     * - トークンが送信されていない場合 not found
     */
    protected function _checkSubmitToken()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'GET' || empty($_POST['_Token']['key']) && empty($_POST['data']['_Token']['key'])) {
            throw new NotFoundException();
        }
    }

    /**
     * データベースログを記録する
     *
     * @param string $message
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function saveDblog($message)
    {
        $DblogsService = $this->getService(DblogsServiceInterface::class);
        return $DblogsService->create(['message' => $message]);
    }
}
