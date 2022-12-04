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

namespace BcMail\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcMail\Service\Admin\MailContentsAdminService;
use BcMail\Service\Admin\MailContentsAdminServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * メールコンテンツコントローラー
 */
class MailContentsController extends MailAdminAppController
{

    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'mailContent',
            'useForm' => true
        ]);
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param MailContentsAdminService $service
     * @param int ブログコンテンツID
     * @checked
     * @noTodo
     */
    public function edit(MailContentsAdminServiceInterface $service, int $id)
    {
        $entity = $service->get($id);
        if ($this->getRequest()->is(['post', 'PUT'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser', 'メールフォーム「{0}」を更新しました。', $entity->content->title));
                if ($this->getRequest()->getData('edit_mail_form') && Plugin::isLoaded('BcThemeFile')) {
                    return $this->redirectEditForm($this->getRequest()->getData('form_template'));
                } elseif ($this->getRequest()->getData('edit_mail') && Plugin::isLoaded('BcThemeFile')) {
                    return $this->redirectEditMail($this->getRequest()->getData('mail_template'));
                } else {
                    return $this->redirect(['action' => 'edit', $id]);
                }
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (BcException $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        $this->set($service->getViewVarsForEdit($entity));
    }

    /**
     * 削除
     *
     * Controller::requestAction() で呼び出される
     *
     * @return bool
     */
    public function delete()
    {
        if (empty($this->request->getData('entityId'))) {
            return false;
        }
        if ($this->MailContent->delete($this->request->getData('entityId'))) {
            $this->MailMessage->dropTable($this->request->getData('entityId'));
            return true;
        }
        return false;
    }

    /**
     * メール編集画面にリダイレクトする
     *
     * @param string $template
     * @return Response
     * @checked
     * @noTodo
     */
    public function redirectEditMail($template)
    {
        $type = 'email';
        $ext = Configure::read('BcApp.templateExt');
        $path = 'text' . DS . $template . $ext;
        $theme = BcUtil::getCurrentTheme();
        $target = Plugin::templatePath($theme) . $type . DS . $path;
        if (!file_exists($target)) {
            $source = Plugin::templatePath(Configure::read('BcApp.defaultFrontTheme')) . $type . DS . $path;
            if (file_exists($source)) {
                $folder = new Folder();
                $folder->create(dirname($target), 0777);
                copy($source, $target);
                chmod($target, 0666);
            }
        }
        $path = str_replace(DS, '/', $path);
        return $this->redirect(array_merge([
            'plugin' => 'BcThemeFile',
            'prefix' => 'Admin',
            'controller' => 'ThemeFiles',
            'action' => 'edit',
            $theme,
            $type
        ], explode('/', $path)));
    }

    /**
     * メールフォーム編集画面にリダイレクトする
     *
     * @param string $template
     * @return Response
     * @checked
     * @noTodo
     */
    public function redirectEditForm($template)
    {
        $path = 'Mail' . DS . $template;
        $theme = BcUtil::getCurrentTheme();
        $target = Plugin::templatePath($theme) . $path;
        $ext = Configure::read('BcApp.templateExt');
        if (!file_exists($target . DS . 'index' . $ext)) {
            $source = Plugin::templatePath(Configure::read('BcApp.defaultFrontTheme')) . DS . $path;
            if (is_dir($source)) {
                $folder = new Folder();
                $folder->create(dirname($target), 0777);
                $folder->copy($target, ['from' => $source, 'chmod' => 0777]);
            }
        }
        $path = str_replace(DS, '/', $path);
        return $this->redirect(array_merge([
            'plugin' => 'BcThemeFile',
            'prefix' => 'Admin',
            'controller' => 'ThemeFiles',
            'action' => 'edit',
            $theme,
            'etc'
        ], explode('/', $path . '/index' . $ext)));
    }

}
