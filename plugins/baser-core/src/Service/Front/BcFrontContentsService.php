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

namespace BaserCore\Service\Front;

use BaserCore\Model\Entity\Content;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcFrontContentsService
 *
 * コンテンツ管理を利用しているコンテンツについて、
 * フロントエンドで利用する変数を生成するためのサービス
 */
class BcFrontContentsService
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * フロント用の view 変数を取得する
     * @param Content $content
     * @param bool $isContentsPage 対象コンテンツのページかどうか
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForFront($content, $isContentsPage = true)
    {
        return [
            // パンくず
            'crumbs' => $this->getCrumbs($content->id, $isContentsPage),
            // 説明文
            'description' => $content->description,
            // タイトル
            'title' => $content->title
        ];
    }

    /**
     * パンくず用のデータを取得する
     *
     * @param $id
     * @param bool $isContentsPage 対象コンテンツのページかどうか
     * true の場合、パンくずの親要素に対象コンテンツを表示しない（カレントページとしてパンくずの最後に表示する）
     * false の場合、パンくずの親要素に対象コンテンツを表示する
     *
     * 例えばブログコンテンツのタイトルをパンくずの親要素に表示して、
     * ブログ記事のタイトルをパンくずの最後に表示する場合に false を設定する
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    protected function getCrumbs($id, $isContentsPage = true)
    {
        if(!$id) return [];
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $contents = $contentsService->getPath($id)->all()->toArray();
        $crumbs = [];
        if($isContentsPage) {
            unset($contents[count($contents) - 1]);
        }
        foreach($contents as $content) {
            if (!$content->site_root) {
                $crumb = [
                    'name' => $content['title'],
                    'url' => $content['url']
                ];
                $crumbs[] = $crumb;
            }
        }
        return $crumbs;
    }

}
