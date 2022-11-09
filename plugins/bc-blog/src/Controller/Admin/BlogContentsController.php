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
use BcBlog\Service\Admin\BlogContentsAdminServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Exception;

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
     * @return void
     * @checked
     */
    public function edit(BlogContentsAdminServiceInterface $service, int $id)
    {
        $blogContent = $service->get($id);
        if ($this->request->is(['post', 'put'])) {
            try {
                $blogContent = $service->update($blogContent, $this->getRequest()->getData());
                $this->BcMessage->setSuccess(sprintf(
                    __d('baser', 'ブログ「%s」を更新しました。'),
                    $blogContent->content->title
                ));
                // TODO ucmitz 未実装
                // BcThemeFileプラグインの利用状況をチェックした上でリダイレクトする
                if ($this->request->getData('BlogContents.edit_blog_template')) {
                    $this->redirectEditBlog(
                        $this->request->getData('BlogContent.template')
                    );
                } else {
                    $this->redirect(['action' => 'edit', $id]);
                }
            } catch (PersistenceFailedException $e) {
                $blogContent = $e->getEntity();
                $this->BcMessage->setError(
                    __d('baser', '入力エラーです。内容を修正してください。')
                );
            } catch (BcException $e) {
                $this->BcMessage->setError(
                    __d('baser', '入力エラーです。内容を修正してください。' . $e->getMessage())
                );
            }
        }
        $this->set($service->getViewVarsForEdit($blogContent));
    }

    /**
     * レイアウト編集画面にリダイレクトする
     *
     * @param string $template
     * @return void
     */
    protected function redirectEditLayout($template)
    {
        $target = sprintf(
            "%stheme/%s/Layouts/%s%s",
            WWW_ROOT,
            $this->siteConfigs['theme'],
            $template,
            $this->ext
        );
        $sorces = [
            sprintf("%sblog/View/Layouts/%s%s", BASER_PLUGINS, $template, $this->ext),
            sprintf("%sLayouts/%s%s", BASER_VIEWS, $template, $this->ext)
        ];
        if (!$this->siteConfigs['theme']) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    '現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。'
                )
            );
            $this->redirect(['action' => 'index']);
            return;
        }

        if (!file_exists($target)) {
            foreach ($sorces as $source) {
                if (file_exists($source)) {
                    copy($source, $target);
                    chmod($target, 0666);
                    break;
                }
            }
        }
        $this->redirect([
            'plugin' => null,
            'controller' => 'theme_files',
            'action' => 'edit',
            $this->siteConfigs['theme'],
            'Layouts',
            $template . $this->ext
        ]);
    }

    /**
     * ブログテンプレート編集画面にリダイレクトする
     *
     * @param string $template
     * @return void
     */
    protected function redirectEditBlog($template)
    {
        $path = str_replace(DS, '/', 'Blog/' . $template);
        $target = sprintf(
            "%stheme/%s/%s",
            WWW_ROOT,
            $this->siteConfigs['theme'],
            $path
        );
        if (!$this->siteConfigs['theme']) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    '現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。'
                )
            );
            $this->redirect(['action' => 'index']);
            return;
        }

        if (!file_exists(sprintf('%s/index%s', $target, $this->ext))) {
            $sources = [
                sprintf('%sBlog/View/%s', BASER_PLUGINS, $path)
            ];
            foreach ($sources as $source) {
                if (!is_dir($source)) {
                    continue;
                }
                $folder = new Folder();
                $folder->create(dirname($target), 0777);
                $folder->copy([
                    'from' => $source,
                    'to' => $target,
                    'chmod' => 0777,
                    'skip' => ['_notes']
                ]);
                break;
            }
        }
        $this->redirect(
            array_merge(
                [
                    'plugin' => null,
                    'controller' => 'theme_files',
                    'action' => 'edit',
                    $this->siteConfigs['theme'],
                    'etc'
                ],
                explode('/', $path . '/index' . $this->ext)
            )
        );
    }

}
