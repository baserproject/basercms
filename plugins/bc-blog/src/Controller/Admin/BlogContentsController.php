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

namespace BcBlog\Controller\Admin;

use BaserCore\Controller\Component\BcAdminContentsComponent;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BcBlog\Service\Admin\BlogContentsAdminServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * ブログコンテンツコントローラー
 *
 * @property BcAdminContentsComponent $BcContents
 * @uses BlogContentsController
 */
class BlogContentsController extends BlogAdminAppController
{

    /**
     * initialize
     * @return void
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'blogContent',
            'useForm' => true
        ]);
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param int $id
     * @return void|ResponseInterface
     * @checked
     */
    public function edit(BlogContentsAdminServiceInterface $service, int $id)
    {
        $blogContent = $service->get($id);
        if ($this->request->is(['post', 'put'])) {
            // EVENT BlogContents.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'data' => $this->getRequest()->getData()
            ]);
            if ($event !== false) {
                $data = ($event->getResult() === null || $event->getResult() === true) ? $event->getData('data') : $event->getResult();
                $this->setRequest($this->getRequest()->withParsedBody($data));
            }
            try {
                $oldContent = clone $blogContent->content;
                $blogContent = $service->update($blogContent, $this->getRequest()->getData());
                // EVENT BlogContents.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $blogContent
                ]);
                $message = __d('baser_core', 'ブログ「{0}」を更新しました。', $blogContent->content->title);
                if ($service->checkRequireSearchIndexReconstruction($oldContent, $blogContent->content)) {
                    $message .= "\n\n" . __d('baser_core',
                            'URL、または、公開状態を変更したので、検索インデックスの再構築が必要です。
                        設定 > ユーティリティ > 検索インデックス より、検索インデックスの再構築を行ってください。'
                        );
                }
                $this->BcMessage->setSuccess($message);
                // BcThemeFileプラグインの利用状況をチェックした上でリダイレクトする
                if ($this->request->getData('edit_blog')) {
                    return $this->redirectEditBlog(
                        $this->request->getData('template')
                    );
                } else {
                    return $this->redirect(['action' => 'edit', $id]);
                }
            } catch (PersistenceFailedException $e) {
                $blogContent = $e->getEntity();
                $this->BcMessage->setError(
                    __d('baser_core', '入力エラーです。内容を修正してください。')
                );
            } catch (BcException $e) {
                $this->BcMessage->setError(
                    __d('baser_core', '入力エラーです。内容を修正してください。' . $e->getMessage())
                );
            }
        }
        $this->set($service->getViewVarsForEdit($blogContent));
    }

    /**
     * ブログテンプレート編集画面にリダイレクトする
     *
     * @param string $template
     */
    protected function redirectEditBlog($template)
    {
        $path = 'Blog' . DS . $template;
        $theme = BcUtil::getCurrentTheme();
        $target = Plugin::templatePath($theme) . 'plugin' . DS . 'BcBlog' . DS . $path;
        $ext = Configure::read('BcApp.templateExt');
        if (!file_exists($target . DS . 'index' . $ext)) {
            $source = Plugin::templatePath(Configure::read('BcApp.coreFrontTheme')) . DS . 'plugin' . DS . 'BcBlog' . DS . $path;
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
            'BcBlog',
            'etc'
        ], explode('/', $path . '/index' . $ext)));
    }

}
