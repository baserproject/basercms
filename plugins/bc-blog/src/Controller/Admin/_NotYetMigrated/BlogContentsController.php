<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログコンテンツコントローラー
 *
 * @package Blog.Controller
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property Content $Content
 */
class BlogContentsController extends BlogAppController
{
    /**
     * モデル
     *
     * @var
     */
    public $uses = ['Blog.BlogContent', 'SiteConfig', 'Blog.BlogCategory'];

    /**
     * ヘルパー
     *
     * @var array
     */
    public $helpers = ['BcHtml', 'BcTime', 'BcForm', 'Blog.Blog'];

    /**
     * コンポーネント
     *
     * @var array
     */
    public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['useForm' => true]];

    /**
     * サブメニューエレメント
     *
     * @var array
     */
    public $subMenuElements = [];

    /**
     * before_filter
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        if (isset($this->params['prefix']) && $this->params['prefix'] === 'admin') {
            $this->subMenuElements = ['blog_common'];
        }
    }

    /**
     * ブログ登録
     *
     * @return mixed json|false
     */
    public function admin_ajax_add()
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        $this->request = $this->request->withData('BlogContent',  $this->BlogContent->getDefaultValue()['BlogContent']);
        $this->request->data = $this->BlogContent->deconstructEyeCatchSize(
            $this->request->data
        );

        $data = $this->BlogContent->save($this->request->data);
        if (!$data) {
            $this->ajaxError(500, $this->BlogContent->validationErrors);
            return false;
        }

        $message = sprintf(
            __d('baser', 'ブログ「%s」を追加しました。'),
            $this->request->getData('Content.title')
        );
        $this->BcMessage->setSuccess($message, true, false);
        return json_encode($data['Content']);
    }

    /**
     * [ADMIN] ブログコンテンツ追加
     *
     * @return void
     */
    public function admin_add()
    {
        $this->setTitle(__d('baser', '新規ブログ登録'));

        if (!$this->request->data) {

            $this->request->data = $this->BlogContent->getDefaultValue();
        } else {

            $this->request->data = $this->BlogContent->deconstructEyeCatchSize(
                $this->request->data
            );
            $this->BlogContent->create($this->request->data);

            if ($this->BlogContent->save()) {
                $this->BcMessage->setSuccess(
                    sprintf(
                        __d('baser', '新規ブログ「%s」を追加しました。'),
                        $this->request->getData('BlogContent.title')
                    )
                );
                $this->redirect(
                    [
                        'action' => 'edit',
                        $this->BlogContent->getLastInsertId()
                    ]
                );
            } else {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }

            $this->request->data = $this->BlogContent->constructEyeCatchSize(
                $this->request->data
            );
        }

        // テーマの一覧を取得
        $this->set('themes', $this->SiteConfig->getThemes());
        $this->render('form');
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param int $id
     * @return void
     */
    public function admin_edit($id)
    {
        if (!$id && empty($this->request->data)) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect([
                'plugin' => false,
                'admin' => true,
                'controller' => 'contents',
                'action' => 'index'
            ]);
        }

        if ($this->request->is(['post', 'put'])) {
            if ($this->BlogContent->isOverPostSize()) {
                $this->BcMessage->setError(
                    __d(
                        'baser',
                        '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                        ini_get('post_max_size')
                    )
                );
                $this->redirect(['action' => 'edit', $id]);
            }

            $this->request->data = $this->BlogContent->deconstructEyeCatchSize(
                $this->request->data
            );
            $this->BlogContent->set($this->request->data);

            if ($this->BlogContent->save()) {
                $this->BcMessage->setSuccess(
                    sprintf(
                        __d('baser', 'ブログ「%s」を更新しました。'),
                        $this->request->getData('Content.title')
                    )
                );
                if ($this->request->getData('BlogContent.edit_blog_template')) {
                    $this->redirectEditBlog(
                        $this->request->getData('BlogContent.template')
                    );
                } else {
                    $this->redirect(['action' => 'edit', $id]);
                }
            } else {
                $this->BcMessage->setError(
                    __d('baser', '入力エラーです。内容を修正してください。')
                );
            }
            $this->request->data = $this->BlogContent->constructEyeCatchSize(
                $this->request->data
            );
        } else {
            $this->request->data = $this->BlogContent->constructEyeCatchSize(
                $this->BlogContent->read(null, $id)
            );
            if (!$this->request->data) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect([
                    'plugin' => false,
                    'admin' => true,
                    'controller' => 'contents',
                    'action' => 'index'
                ]);
            }
        }
        $site = BcSite::findById($this->request->getData('Content.site_id'));
        if (!empty($this->request->getData('Content.status'))) {
            $this->set(
                'publishLink',
                $this->Content->getUrl(
                    $this->request->getData('Content.url'),
                    true,
                    $site->useSubDomain
                )
            );
        }
        $this->request = $this->request->withParam('Content',  $this->BcContents->getContent($id)['Content']);
        $this->set('blogContent', $this->request->data);
        $this->subMenuElements = ['blog_posts'];
        $this->set('themes', $this->SiteConfig->getThemes());
        $this->setTitle(__d('baser', 'ブログ設定編集'));
        $this->setHelp('blog_contents_form');
        $this->render('form');
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

    /**
     * 削除
     *
     * Controller::requestAction() で呼び出される
     *
     * @return bool
     */
    public function admin_delete()
    {
        if (empty($this->request->getData('entityId'))) {
            return false;
        }
        if ($this->BlogContent->delete($this->request->getData('entityId'))) {
            return true;
        }
        return false;
    }

    /**
     * コピー
     *
     * @return bool
     */
    public function admin_ajax_copy()
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $user = $this->BcAuth->user();
        $data = $this->BlogContent->copy(
            $this->request->getData('entityId'),
            $this->request->getData('parentId'),
            $this->request->getData('title'),
            $user['id'],
            $this->request->getData('siteId')
        );
        if (!$data) {
            $this->ajaxError(500, $this->BlogContent->validationErrors);
            return false;
        }

        $message = sprintf(
            __d('baser', 'ブログのコピー「%s」を追加しました。'),
            $this->request->getData('title')
        );
        $this->BcMessage->setSuccess($message, true, false);
        return json_encode($data['Content']);
    }
}
