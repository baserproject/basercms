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

namespace BcThemeFile\Controller\Admin;

use BaserCore\Error\BcFormFailedException;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Controller\ThemeFileAppController;
use BcThemeFile\Service\Admin\ThemeFilesAdminService;
use BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface;
use BcThemeFile\Service\Admin\ThemeFoldersAdminService;
use BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface;
use BcThemeFile\Service\ThemeFoldersService;
use BcThemeFile\Service\ThemeFoldersServiceInterface;
use BcThemeFile\Utility\BcThemeFileUtil;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemeFilesController
 *
 * テーマファイルコントローラー
 */
class ThemeFilesController extends ThemeFileAppController
{

    /**
     * ThemeFilesController constructor.
     * @param ServerRequest|null $request
     * @param Response|null $response
     * @param string|null $name
     * @param EventManagerInterface|null $eventManager
     * @param ComponentRegistry|null $components
     */
    public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?string $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry $components = null
    )
    {
        parent::__construct($request, $response, $name, $eventManager, $components);
        $this->_tempalteTypes = [
            'Layouts' => __d('baser', 'レイアウトテンプレート'),
            'Elements' => __d('baser', 'エレメントテンプレート'),
            'Emails' => __d('baser', 'Eメールテンプレート'),
            'etc' => __d('baser', 'コンテンツテンプレート'),
            'css' => __d('baser', 'スタイルシート'),
            'js' => 'Javascript',
            'img' => __d('baser', 'イメージ')
        ];

        // テーマ編集機能が制限されている場合はアクセス禁止
        if (Configure::read('BcThemeEdit.allowedThemeEdit') === false) {
            $denyList = [
                'index',
                'add',
                'edit',
                'add_folder',
                'edit_folder',
            ];
            // デフォルトテーマのindexはアクセス可能
            if ($this->isDefaultTheme()) {
                unset($denyList[array_search('index', $denyList)]);
            }
            if (in_array($this->getRequest()->getParam('action'), $denyList)) {
                $this->notfound();
            }
        }
    }

    /**
     * 現在の画面のテーマがデフォルトテーマかどうか
     *
     * @return bool
     */
    protected function isDefaultTheme()
    {
        return (Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-') === $this->getRequest()->getParam('pass.0'));
    }

    /**
     * Before Render
     *
     * @param EventInterface $event
     * @checked
     * @noTodo
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->set([
            'isDefaultTheme' => $this->isDefaultTheme()
        ]);
        if($this->isDefaultTheme()) {
            $this->BcMessage->setWarning(__d('baser', 'デフォルトテーマのため編集できません。編集する場合は、テーマをコピーしてご利用ください。'));
        }
    }

    /**
     * テーマファイル一覧
     *
     * @param ThemeFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function index(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!$args['theme']) $this->notFound();
        $this->set($service->getViewVarsForIndex($args));
    }

    /**
     * テーマファイル作成
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function add(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $entity = $service->getNew($args['fullpath'], $args['type']);
        $form = $service->getForm($entity->toArray());

        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $form = $service->create($this->getRequest()->getData());
                $entity = $service->get($form->getData('fullpath'));
                $this->BcMessage->setInfo(sprintf(__d('baser', 'ファイル %s を作成しました。'), $entity->name));
                $this->redirect(array_merge(
                    ['action' => 'edit', $args['theme'], $args['type']],
                    explode('/', $args['path']),
                    [$entity->name]
                ));
            } catch (BcFormFailedException $e) {
                $form = $e->getForm();
                $this->BcMessage->setError(__d('baser', 'ファイル {0} の作成に失敗しました。', $entity->name));
            } catch (\Throwable $e) {
                $form = $service->getForm($this->getRequest()->getData());
                $this->BcMessage->setError(__d('baser', 'ファイル {0} の作成に失敗しました。', $entity->name) . $e->getMessage());
            }
        }

        $this->set($service->getViewVarsForEdit($entity, $form, $args));
    }

    /**
     * テーマファイル編集
     *
     * @param ThemeFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function edit(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $themeFile = $service->get($args['fullpath']);
        $themeFileForm = $service->getForm($themeFile->toArray());

        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $themeFileForm = $service->update($this->getRequest()->getData());
                $themeFile = $service->get($themeFileForm->getData('fullpath'));
                $this->BcMessage->setInfo(sprintf(__d('baser', 'ファイル %s を更新しました。'), $themeFile->name));
                $this->redirect(array_merge(
                    [$args['theme'], $args['plugin'], $args['type']],
                    explode('/', dirname($args['path'])),
                    [$themeFile->name]
                ));
            } catch (BcFormFailedException $e) {
                $themeFileForm = $e->getForm();
                $this->BcMessage->setError(__d('baser', 'ファイル {0} の更新に失敗しました。', $themeFile->name));
            } catch (\Throwable $e) {
                $themeFileForm = $service->getForm($this->getRequest()->getData());
                $this->BcMessage->setError(__d('baser', 'ファイル {0} の更新に失敗しました。', $themeFile->name) . $e->getMessage());
            }
        }

        $this->set($service->getViewVarsForEdit($themeFile, $themeFileForm, $args));
    }

    /**
     * ファイルを削除する
     *
     * @param ThemeFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function delete(ThemeFilesAdminServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);

        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        if ($service->delete($args['fullpath'])) {
            $this->BcMessage->setSuccess(__d('baser', 'ファイル {0} を削除しました。', $args['path']));
        } else {
            $this->BcMessage->setError(__d('baser', 'ファイル {0} の削除に失敗しました。', $args['path']));
        }

        $this->redirect(array_merge(
            ['action' => 'index', $args['theme'], $args['type']],
            explode('/', dirname($args['path']))
        ));
    }

    /**
     * ファイルを削除する
     *
     * @param ThemeFoldersAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function delete_folder(ThemeFoldersAdminServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);

        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        if ($service->delete($args['fullpath'])) {
            $this->BcMessage->setSuccess(__d('baser', 'フォルダ {0} を削除しました。', $args['path']));
        } else {
            $this->BcMessage->setError(__d('baser', 'フォルダ {0} の削除に失敗しました。', $args['path']));
        }

        $this->redirect(array_merge(
            ['action' => 'index', $args['theme'], $args['type']],
            explode('/', dirname($args['path']))
        ));
    }

    /**
     * テーマファイル表示
     *
     * @param ThemeFilesAdminService $service
     * @return    void
     * @checked
     * @noTodo
     */
    public function view(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $entity = $service->get($args['fullpath']);
        $form = $service->getForm($entity->toArray());
        $this->set($service->getViewVarsForView($entity, $form, $args));
    }

    /**
     * テーマファイルをコピーする
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function copy(ThemeFilesAdminServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);

        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        if ($service->copy($args['fullpath'])) {
            $this->BcMessage->setSuccess(__d('baser', 'ファイル {0} をコピーしました。', $args['path']));
        } else {
            $this->BcMessage->setError(__d('baser', 'ファイル {0} のコピーに失敗しました。上位フォルダのアクセス権限を見直してください。', $args['path']));
        }

        $this->redirect(array_merge(
            ['action' => 'index', $args['theme'], $args['type']],
            explode('/', dirname($args['path']))
        ));
    }

    /**
     * テーマフォルダをコピーする
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function copy_folder(ThemeFoldersAdminServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);

        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        if ($service->copy($args['fullpath'])) {
            $this->BcMessage->setSuccess(__d('baser', 'フォルダ {0} をコピーしました。', $args['path']));
        } else {
            $this->BcMessage->setError(__d('baser', 'フォルダ {0} のコピーに失敗しました。上位フォルダのアクセス権限を見直してください。', $args['path']));
        }

        $this->redirect(array_merge(
            ['action' => 'index', $args['theme'], $args['type']],
            explode('/', dirname($args['path']))
        ));
    }

    /**
     * ファイルをアップロードする
     *
     * @param ThemeFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function upload(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $this->request->allowMethod(['post', 'put']);
        try {
            $service->upload($args['fullpath'], $this->getRequest()->getData());
            $this->BcMessage->setSuccess(__d('baser', 'アップロードに成功しました。'));
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser', 'アップロードに失敗しました。' . $e->getMessage()));
        }
        $this->redirect(array_merge(['action' => 'index', $args['theme'], $args['type']], explode('/', $args['path'])));
    }

    /**
     * フォルダ追加
     *
     * @param ThemeFoldersAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function add_folder(ThemeFoldersAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $entity = $service->getNew($args['fullpath']);
        $form = $service->getForm($entity->toArray());

        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $form = $service->create($this->getRequest()->getData());
                $entity = $service->get($form->getData('fullpath'));
                $this->BcMessage->setInfo('フォルダ「' . $entity->name . '」を作成しました。');
                return $this->redirect(array_merge(
                    ['action' => 'index', $args['theme'], $args['type']],
                    explode('/', dirname($args['path']))
                ));
            } catch (BcFormFailedException $e) {
                $form = $e->getForm();
                $this->BcMessage->setError(__d('baser', 'フォルダの作成に失敗しました。'));
            } catch (\Throwable $e) {
                $form = $service->getForm($this->getRequest()->getData());
                $this->BcMessage->setError(__d('baser', 'フォルダの作成に失敗しました。') . $e->getMessage());
            }
        }

        $this->set($service->getViewVarsForAdd($entity, $form, $args));
    }

    /**
     * フォルダ編集
     *
     * @param ThemeFoldersAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function edit_folder(ThemeFoldersAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $entity = $service->get($args['fullpath']);
        $form = $service->getForm($entity->toArray());

        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $form = $service->update($this->getRequest()->getData());
                $entity = $service->get($form->getData('fullpath'));
                $this->BcMessage->setInfo('フォルダ名を ' . $entity->name . ' に変更しました。');
                return $this->redirect(array_merge(
                    ['action' => 'index', $args['theme'], $args['type']],
                    explode('/', dirname($args['path']))
                ));
            } catch (BcFormFailedException $e) {
                $form = $e->getForm();
                $this->BcMessage->setError(__d('baser', 'フォルダ名の変更に失敗しました。'));
            } catch (\Throwable $e) {
                $form = $service->getForm($this->getRequest()->getData());
                $this->BcMessage->setError(__d('baser', 'フォルダ名の変更に失敗しました。') . $e->getMessage());
            }
        }
        $this->set($service->getViewVarsForEdit($entity, $form, $args));
    }

    /**
     * フォルダ表示
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function view_folder(ThemeFoldersAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $entity = $service->get($args['fullpath']);
        $form = $service->getForm($entity->toArray());
        $this->set($service->getViewVarsForView($entity, $form, $args));
    }

    /**
     * コアファイルを現在のテーマにコピーする
     *
     * @param ThemeFilesAdminService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function copy_to_theme(ThemeFilesAdminServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $targetPath = $service->copyToTheme($args);
        $currentTheme = BcUtil::getCurrentTheme();
        if ($targetPath) {
            $this->BcMessage->setSuccess(__d('baser',
                "コアファイル {0} を テーマ {1} の次のパスとしてコピーしました。\n{2}",
                basename($args['path']),
                $currentTheme,
                $targetPath
            ));
            return $this->redirect(array_merge(
                ['action' => 'edit', $currentTheme, $args['plugin'], $args['type']],
                explode('/', $args['path'])
            ));
        } else {
            $this->BcMessage->setError(__d('baser',
                'コアファイル {0} のコピーに失敗しました。',
                basename($args['path'])
            ));
        }
        return $this->redirect(array_merge(
            ['action' => 'view', $args['theme'], $args['plugin'], $args['type']],
            explode('/', $args['path'])
        ));
    }

    /**
     * コアファイルのフォルダを現在のテーマにコピーする
     *
     * @param ThemeFoldersService $service
     * @return void
     * @checked
     * @noTodo
     */
    public function copy_folder_to_theme(ThemeFoldersServiceInterface $service)
    {
        $args = $this->parseArgs(func_get_args());
        if (!BcThemeFileUtil::getTemplateTypeName($args['type'])) $this->notFound();

        $targetPath = $service->copyToTheme($args);
        $currentTheme = BcUtil::getCurrentTheme();
        if ($targetPath) {
            $this->BcMessage->setInfo(__d('baser',
                "コアフォルダ {0} を テーマ {1} の次のパスとしてコピーしました。\n{2}",
                basename($args['path']),
                $currentTheme,
                $targetPath
            ));
            $this->redirect(array_merge(
                ['action' => 'edit_folder', $currentTheme, $args['plugin'], $args['type']],
                explode('/', $args['path'])
            ));
        } else {
            $this->BcMessage->setError(__d('baser',
                'コアフォルダ {0} のコピーに失敗しました。',
                basename($args['path'])
            ));
        }
        $this->redirect(array_merge(
            ['action' => 'view_folder', $args['theme'], $args['plugin'], $args['type']],
            explode('/', $args['path'])
        ));
    }

    /**
     * 画像を表示する
     * コアの画像等も表示可
     *
     * @param ThemeFilesAdminService $service
     * @checked
     * @noTodo
     */
    public function img(ThemeFilesAdminServiceInterface $service)
    {
        $this->disableAutoRender();
        $args = $this->parseArgs(func_get_args());
        return $this->getResponse()->withStringBody($service->getImg($args));
    }

    /**
     * 画像を表示する
     * コアの画像等も表示可
     *
     * @param ThemeFilesAdminService $service
     * @checked
     * @noTodo
     */
    public function img_thumb(ThemeFilesAdminServiceInterface $service)
    {
        $args = func_get_args();
        unset($args[0]);
        $args = array_merge($args);
        $width = $args[0];
        $height = $args[1];
        unset($args[0]);
        unset($args[1]);
        $args = array_values($args);

        if ($width == 0) $width = 100;
        if ($height == 0) $height = 100;

        $args = $this->parseArgs($args);
        return $this->getResponse()->withStringBody($service->getImgThumb($args, $width, $height));
    }

}
