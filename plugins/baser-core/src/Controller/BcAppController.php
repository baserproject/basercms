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
use Cake\Event\EventInterface;
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
     * @checked
     * @note(value="マイルストーン２が終わってから確認する")
     * @todo ucmitz 未確認
     */
    public function beforeFilter(EventInterface $event)
    {
        return parent::beforeFilter($event);

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
        $this->__loadDataToView();
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
