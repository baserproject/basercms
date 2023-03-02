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

namespace BaserCore\Controller\Api;

use BaserCore\Service\SiteConfigsServiceInterface;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * SiteConfigsController
 */
class SiteConfigsController extends BcApiController
{

    /**
     * システム基本設定を取得
     * @param SiteConfigsServiceInterface $service
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(SiteConfigsServiceInterface $service) {
        $this->set([
            'siteConfig' => $service->get()
        ]);
        $this->viewBuilder()->setOption('serialize', ['siteConfig']);
    }

    /**
     * システム基本設定を編集する
     * @param SiteConfigsServiceInterface $service
     */
    public function edit(SiteConfigsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $siteConfig = $errors = null;
        try {
            $siteConfig = $service->update($this->request->getData());
            if (!$siteConfig->getErrors()) {
                $message = __d('baser', 'システム基本設定を更新しました。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'siteConfig' => $siteConfig,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['siteConfig', 'message', 'errors']);
    }

    /**
     * メールの送信テストを実行する
     * @checked
     * @note(value="メール送信機能を実装してから対応する")
     */
    public function check_sendmail()
    {
        $this->request->allowMethod(['post', 'put']);
        $siteConfigs = $this->request->getData();
        $message = '';
        if(isset($siteConfigs['site_url'])) {
            $siteUrl = $siteConfigs['site_url'];
        } else {
            $siteUrl = Configure::read('BcEnv.siteUrl');
        }
        // TODO ucmitz 未実装ためコメントアウト
        /* >>>
        if (!$this->sendMail(
            $siteConfigs['email'], __d('baser', 'メール送信テスト'),
            sprintf('%s からのメール送信テストです。', $siteConfigs['formal_name']) . "\n" . $siteUrl
        )) {
            $this->setResponse($this->response->withStatus(401));
            $message = __d('baser', 'ログを確認してください。');
        }
        <<< */
        $this->set('message', $message);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }
}
