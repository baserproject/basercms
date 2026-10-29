<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSeo\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use BaserCore\Routing\Route\BcContentsRoute;
use BaserCore\Utility\BcUtil;

/**
 * Class SeoHelper
 */
class SeoHelper extends Helper
{
    private $fields;
    private $seoMetasTable;

    public array $helpers = [
        'BaserCore.BcBaser',
        'BaserCore.BcUpload',
        'BcBlog.Blog',
    ];

    /**
     * initialize
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->fields = Configure::read('BcSeo.fields');
        $this->seoMetasTable = TableRegistry::getTableLocator()->get('BcSeo.SeoMetas');
    }

    /**
     * beforeRender
     *
     * カノニカル URL の設定値をコアへ引き渡す。
     *
     * 設定があれば BcBaserHelper::setCanonicalUrl() がその値を、
     * 無ければ従来どおり自身の URL を出力する。
     * 同メソッドは View の afterRender で実行されるため、beforeRender で渡す。
     */
    public function beforeRender(EventInterface $event, $viewFile): void
    {
        if (BcUtil::isAdminSystem()) {
            return;
        }
        $meta = $this->getMeta(['canonical_url']);
        if (!empty($meta['canonical_url']['value'])) {
            $this->getView()->set('canonicalUrl', $meta['canonical_url']['value']);
        }
    }

    /**
     * 設定されているメタタグを出力
     */
    public function meta(array $fields = [])
    {
        // カノニカル URL は BcBaserHelper::setCanonicalUrl() が出力するため、
        // ここで出力すると重複する
        $metaData = $this->getMeta($fields);
        unset($metaData['canonical_url']);
        $this->getView()->set('metaData', $metaData);
        $this->BcBaser->element('BcSeo.seo_meta');
    }

    /**
     * メタ情報を取得
     */
    public function getMeta(array $fields = []): array
    {
        if (empty($fields)) {
            $fields = $this->fields;
        } elseif ($fields) {
            $fields = array_intersect_key($this->fields, array_flip($fields));
        }

        $controller = $this->getView()->getRequest()->getParam('controller');
        $action = $this->getView()->getRequest()->getParam('action');
        $content = $this->getView()->getRequest()->getAttribute('currentContent');
        $route = $this->getView()->getRequest()->getAttribute('route');

        $metaValueLayers = [];
        if ($content) {
            // サイト
            $metaValueLayers[] = $this->getMetaValues('Sites', 0, $content->site_id);

            // コンテンツ管理で管理されていないページの場合、currentContentにサイトのトップが入ってくるため対策
            // BcFrontMiddleware->setCurrentに仕様の記載あり
            if (!$route instanceof BcContentsRoute) {
                $content = null;
            }
        }

        // コンテンツ個別の設定
        $contentValues = $this->getContentValues();

        // コンテンツ
        if ($content) {
            $metaValueLayers[] = $this->getMetaValues('Contents', 0, $content->id);
            $eyecatch = $this->BcUpload->uploadImage('eyecatch', $content,
                ['table' => 'BaserCore.Contents', 'output' => 'url']);
            if ($eyecatch) {
                $contentValues['og_image'] = $this->BcBaser->getUrl($eyecatch, true);
            }
        }

        // ブログカテゴリ・ブログ記事
        if ($controller === 'Blog' && $action === 'archives') {
            if ($this->Blog->isCategory()) {
                $blogCategory = $this->getView()->get('blogCategory');
                if ($blogCategory) {
                    $metaValueLayers[] = $this->getMetaValues('BlogCategories', 0, $blogCategory->id);
                }
            } elseif ($this->Blog->isSingle()) {
                // 記事が見つからない場合は null になる
                $blogPost = $this->getView()->get('post');
                if ($blogPost && $blogPost->id) {
                    $metaValueLayers[] = $this->getMetaValues('BlogPosts', 0, $blogPost->id);
                    $eyecatch = $this->Blog->getEyecatch($blogPost, ['output' => 'url']);
                    if ($eyecatch) {
                        $contentValues['og_image'] = $this->BcBaser->getUrl($eyecatch, true);
                    }
                }
            }
        }

        // カスタムコンテンツ
        if ($controller === 'CustomContent' && $action === 'view') {
            $customEntry = $this->getView()->get('customEntry');
            if ($customEntry && $customEntry->id) {
                $metaValueLayers[] = $this->getMetaValues('CustomEntries',
                    $customEntry->custom_table_id, $customEntry->id);
            }
        }

        // カスタムコンテンツのアーカイブ（関連データのカテゴリ）
        // 関連先エントリーのSEO設定で上書きする
        if ($controller === 'CustomContent' && $action === 'archives') {
            // URLから、フィールド名と関連先エントリーのIDに分ける
            [$field, $value] = $this->getView()->getRequest()->getParam('pass');
            // フィールドを探す対象のカスタムテーブルを特定するため、カスタムコンテンツの情報を取り出す
            $customContent = $this->getView()->get('customContent');
            // フィールド名が存在する、エントリーのIDが数字、カスタムコンテンツの情報がある場合
            if ($field && ctype_digit((string)$value) && $customContent) {
                // カスタムコンテンツが使っているカスタムテーブルのフィールド一覧から、フィールド名が一致するものを、詳しい設定（種類・関連先）と合わせて1件取得
                $customLink = TableRegistry::getTableLocator()->get('BcCustomContent.CustomLinks')->find()
                    ->contain(['CustomFields'])
                    ->where([
                        'CustomLinks.custom_table_id' => $customContent->custom_table_id,
                        'CustomLinks.name' => $field,
                    ])->first();
                // フィールドと、その詳しい設定が見つかった場合のみ
                if ($customLink && $customLink->custom_field) {
                    $customField = $customLink->custom_field;
                    // フィールド設定から関連先のカスタムテーブルIDを取得（取得できない場合は 0）
                    $relatedTableId = (int)($customField->meta['BcCcRelated']['custom_table_id'] ?? 0);
                    // 関連データのフィールドで、関連先テーブルが設定されている場合のみ
                    if ($customField->type === 'BcCcRelated' && $relatedTableId) {
                        // 関連先エントリーのSEO設定を取得する
                        $metaValueLayers = [$this->getMetaValues('CustomEntries', $relatedTableId, (int)$value)];
                        // 親側のSEO設定は全て削除する
                        $contentValues = [];
                    }
                }
            }
        }

        // 複数の要素の設定をマージ
        $self = array_pop($metaValueLayers);
        $metaValues = [];
        foreach ($fields as $fieldKey => $field) {
            // 親要素の設定を引き継がない
            if (!empty($field['ignoreParent'])) {
                if (!empty($self[$fieldKey])) {
                    $metaValues[$fieldKey] = $self[$fieldKey];
                }
            // 親要素の設定を引き継ぐ
            } else {
                foreach ($metaValueLayers as $metaValueLayer) {
                    if (!empty($metaValueLayer[$fieldKey])) {
                        $metaValues[$fieldKey] = $metaValueLayer[$fieldKey];
                    }
                }
            }
            // コンテンツ個別の設定
            if (!empty($contentValues[$fieldKey])) {
                $metaValues[$fieldKey] = $contentValues[$fieldKey];
            }
            // 自身の設定
            if (!empty($self[$fieldKey])) {
                $metaValues[$fieldKey] = $self[$fieldKey];
            }
        }

        // 絶対URLに変換
        foreach ($fields as $fieldKey => $field) {
            if (!empty($field['url']) && !empty($metaValues[$fieldKey])) {
                $metaValues[$fieldKey] = $this->BcBaser->getUrl($metaValues[$fieldKey], true);
            }
        }

        // 値が設定されているものだけをビューに渡す
        $metaData = [];
        foreach ($fields as $fieldKey => $field) {
            if (empty($metaValues[$fieldKey])) {
                continue;
            }
            $field['value'] = $metaValues[$fieldKey];
            $metaData[$fieldKey] = $field;
        }

        return $metaData;
    }

    /**
     * コンテンツ個別の設定を取得
     */
    private function getContentValues(): array
    {
        return [
            'description' => $this->getView()->get('description'),
            'keywords' => $this->getView()->get('keywords'),
        ];
    }

    /**
     * メタ情報を取得
     */
    private function getMetaValues(string $tableAlias, int $tableId, int $entityId): array
    {
        if (!$entityId) {
            return [];
        }

        $query = $this->seoMetasTable->find()
            ->where(['table_alias' => $tableAlias])
            ->where(['entity_id' => $entityId]);
        if ($tableId) {
            $query->where(['table_id' => $tableId]);
        }
        $seoMeta = $query->first();
        if (!$seoMeta) {
            return [];
        }

        $metaValues = [];
        foreach ($this->fields as $fieldKey => $field) {
            if (!empty($seoMeta->$fieldKey)) {
                $metaValues[$fieldKey] = $seoMeta->$fieldKey;
                if ($field['type'] === 'file') {
                    $ogImage = $this->BcUpload->uploadImage($fieldKey, $seoMeta,
                        ['table' => 'BcSeo.SeoMetas', 'output' => 'url']);
                    $metaValues[$fieldKey] = $this->BcBaser->getUrl($ogImage, true);
                }
            }
        }
        return $metaValues;
    }
}
