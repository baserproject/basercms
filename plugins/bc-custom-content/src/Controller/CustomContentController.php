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

namespace BcCustomContent\Controller;

use BaserCore\Controller\BcFrontAppController;
use BcCustomContent\Service\Front\CustomContentFrontServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentController
 */
class CustomContentController extends BcFrontAppController
{

    /**
     * initialize
     *
     * コンポーネントをロードする
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        if ($this->getRequest()->getParam('action') === 'index') {
            $this->loadComponent('BaserCore.BcFrontContents');
        } else {
            $this->loadComponent('BaserCore.BcFrontContents', ['viewContentCrumb' => true]);
        }
    }

    /**
     * カスタムエントリーの一覧ページを表示する
     *
     * @param CustomContentFrontServiceInterface $service
     * @return \Cake\Http\Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomContentFrontServiceInterface $service)
    {
        $customContent = $service->getCustomContent(
            (int)$this->getRequest()->getAttribute('currentContent')->entity_id
        );

        if(!$customContent->custom_table_id) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            $this->notFound();
        }

        $this->set($service->getViewVarsForIndex(
            $customContent,
            $this->paginate(
                $service->getCustomEntries($customContent, $this->getRequest()->getQueryParams()),
                ['limit' => $customContent->list_count]
            )
        ));
        $this->setRequest($this->getRequest()->withParsedBody($this->getRequest()->getQueryParams()));
        $this->render($service->getIndexTemplate($customContent));
    }

    /**
     * カスタムエントリーの詳細ページを表示する
     *
     * @param CustomContentFrontServiceInterface $service
     * @return \Cake\Http\Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomContentFrontServiceInterface $service, $entryId)
    {
        if(!$this->getRequest()->getAttribute('currentContent')->entity_id) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            $this->notFound();
        }
        $customContent = $service->getCustomContent(
            (int)$this->getRequest()->getAttribute('currentContent')->entity_id
        );

        if(!$customContent->custom_table_id) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            $this->notFound();
        }

        $this->set($service->getViewVarsForView(
            $customContent,
            $entryId
        ));

        $this->render($service->getViewTemplate($customContent));
    }

    /**
     * カスタムエントリーのアーカイブを表示する
     *
     * ### URL例
     * - カテゴリ別記事一覧： /products/archives/category/category-name
     */
    public function archives(CustomContentFrontServiceInterface $service, $field, $value)
    {
        $customContent = $service->getCustomContent(
            (int)$this->getRequest()->getAttribute('currentContent')->entity_id
        );

        if(!$customContent->custom_table_id) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            $this->notFound();
        };

    $this->set($service->getViewVarsForIndex(
        $customContent,
        $this->paginate(
            $service->getCustomEntries($customContent, [
            $field => urldecode($value)
        ]),
    )));

    $this->render($service->getArchivesTemplate($customContent));
    }

    /**
     * 年別のアーカイブを表示する
     *
     * ### URL例
     * - 年別記事一覧： /products/year/2025
     */
    public function year(CustomContentFrontServiceInterface $service, $year)
    {
        $customContent = $service->getCustomContent(
            (int)$this->getRequest()->getAttribute('currentContent')->entity_id
        );

        if(!$customContent->custom_table_id) {
            $this->BcMessage->setWarning(__d('baser_core', 'カスタムコンテンツにカスタムテーブルが紐付けられていません。カスタムコンテンツの編集画面よりカスタムテーブルを選択してください。'));
            $this->notFound();
        };

        $this->set($service->getViewVarsForIndex(
            $customContent,
            $this->paginate(
                $service->getCustomEntries($customContent, [
                    'created' => $year . '%',
                ]),
            )));

        $this->render($service->getArchivesTemplate($customContent));
    }
}
