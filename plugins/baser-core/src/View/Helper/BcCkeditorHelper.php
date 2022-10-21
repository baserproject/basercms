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

namespace BaserCore\View\Helper;

use Cake\Core\Plugin;
use Cake\View\Helper;
use Cake\Utility\Inflector;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Event\BcEventDispatcherTrait;


/**
 * Class BcCkeditorHelper
 *
 * @package Baser.View.Helper
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcHtmlHelper $BcHtml
 */
class BcCkeditorHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * ヘルパー
     * @var array
     */
    public $helpers = ['BcHtml', 'BcAdminForm', 'Url', 'BcBaser'];

    /**
     * スクリプト
     * 既にjavascriptが読み込まれている場合はfalse
     *
     * @var boolean
     */
    protected $_script = false;

    /**
     * 初期設定スタイル
     * StyleSet 名 basercms
     * 翻訳がある為、コンストラクタで初期化
     *
     * @var array
     */
    public $style = [];

    /**
     * ツールバー
     *
     * @var array
     */
    public $toolbars = [
        'simple' => [
            ['Bold', 'Underline', '-',
                'NumberedList', 'BulletedList', '-',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight',
                'Format', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', '-', 'Image'],
            ['Maximize', 'ShowBlocks', 'Source']
        ],
        'normal' => [
            ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo', '-', 'Bold', 'Italic', 'Underline', 'Strike', '-',
                'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
                'Smiley', 'Table', 'HorizontalRule', '-'],
            ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', 'Anchor', '-', 'Image'],
            ['Maximize', 'ShowBlocks', 'Source']
        ]
    ];

    /**
     * initialize
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize($config): void
    {
        $this->style = [
            ['name' => __d('baser', '青見出し') . '(h3)',
                'element' => 'h3',
                'styles' => ['color' => 'Blue']],
            ['name' => __d('baser', '赤見出し') . '(h3)',
                'element' => 'h3',
                'styles' => ['color' => 'Red']],
            ['name' => __d('baser', '黄マーカー') . '(span)',
                'element' => 'span',
                'styles' => ['background-color' => 'Yellow']],
            ['name' => __d('baser', '緑マーカー') . '(span)',
                'element' => 'span',
                'styles' => ['background-color' => 'Lime']],
            ['name' => __d('baser', '大文字') . '(big)',
                'element' => 'big'],
            ['name' => __d('baser', '小文字') . '(small)',
                'element' => 'small'],
            ['name' => __d('baser', 'コード') . '(code)',
                'element' => 'code'],
            ['name' => __d('baser', '削除文') . '(del)',
                'element' => 'del'],
            ['name' => __d('baser', '挿入文') . '(ins)',
                'element' => 'ins'],
            ['name' => __d('baser', '引用') . '(cite)',
                'element' => 'cite'],
            ['name' => __d('baser', 'インライン') . '(q)',
                'element' => 'q']
        ];
        parent::initialize($config);
    }

    /**
     * CKEditor のスクリプトを構築する
     * 【ボタン一覧】
     * Source            - ソース
     * Save                - 保存
     * NewPage            - 新しいページ
     * Preview            - プレビュー
     * Templates        - テンプレート
     * Cut                - 切り取り
     * Copy                - コピー
     * Paste            - 貼り付け
     * PasteText        - プレーンテキスト貼り付け
     * PasteFromWord    - ワードから貼り付け
     * Print            - 印刷
     * SpellChecker        - スペルチェック
     * Scayt            - スペルチェック設定
     * Undo                - 元に戻す
     * Redo                - やり直し
     * Find                - 検索
     * Replace            - 置き換え
     * SelectAll        - すべて選択
     * RemoveFormat        - フォーマット削除
     * Form                - フォーム
     * Checkbox            - チェックボックス
     * Radio            - ラジオボタン
     * TextField        - 1行テキスト
     * Textarea            - テキストエリア
     * Select            - 選択フィールド
     * Button            - ボタン
     * ImageButton        - 画像ボタン
     * HiddenField        - 不可視フィールド
     * Bold                - 太字
     * Italic            - 斜体
     * Underline        - 下線
     * Strike            - 打ち消し線
     * Subscript        - 添え字
     * Superscript        - 上付き文字
     * NumberedList        - 段落番号
     * BulletedList        - 箇条書き
     * Outdent            - インデント解除
     * Indent            - インデント
     * Blockquote        - ブロック引用
     * JustifyLeft        - 左揃え
     * JustifyCenter    - 中央揃え
     * JustifyRight        - 右揃え
     * JustifyBlock        - 両端揃え
     * Link                - リンク挿入／編集
     * Unlink            - リンク解除
     * Anchor            - アンカー挿入／編集
     * Image            - イメージ
     * Flash            - FLASH
     * Table            - テーブル
     * HorizontalRule    - 横罫線
     * Smiley            - 絵文字
     * SpecialChar        - 特殊文字
     * PageBreak        - 改ページ挿入
     * Styles            - スタイル
     * Format            - フォーマット
     * Font                - フォント
     * FontSize            - フォントサイズ
     * TextColor        - テキスト色
     * BGColor            - 背景色
     * Maximize            - 最大化
     * ShowBlocks        - ブロック表示
     * About            - CKEditorバージョン情報
     * Publish            - 本稿に切り替え
     * Draft            - 草稿に切り替え
     * CopyPublish        - 本稿を草稿にコピー
     * CopyDraft        - 草稿を本稿にコピー
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function buildTmpScript($fieldName, $options = [])
    {
        $options = array_merge([
            'editorLanguage' => 'ja', // 言語
            'editorSkin' => 'moono', // スキン
            'editorToolType' => 'normal', // ツールバータイプ
            'editorToolbar' => [], // ツールバータイプ
            'editorWidth' => '600px', // エディタサイズ
            'editorHeight' => '300px', // エディタ高さ
            'editorCollapser' => false, //
            'editorBaseFloatZIndex' => 900, //
            'editorStylesSet' => 'basercms', // スタイルセット
            'editorUseDraft' => false, // 草稿利用
            'editorDraftField' => false, // 草稿用フィールド
            'editorDisablePublish' => false, // 本稿利用不可
            'editorDisableDraft' => true, // 草稿利用不可
            'editorDisableCopyDraft' => false, // 草稿へコピー利用不可
            'editorDisableCopyPublish' => false, // 本稿へコピー利用不可
            'editorReadOnlyPublish' => false, // 本稿読み込みのみ許可
            'editorUseTemplates' => true, // テンプレート利用
            'editorEnterBr' => false, // エンター時に改行を入れる
            'editorStyles' => [],  // スタイル
            'editorPreviewModeId' => 'ContentPreviewMode' // プレビュー状態を格納するフィールドのID
        ], $options);

        if (!$this->_script) {
            $this->_script = true;
            $this->BcHtml->script(['vendor/ckeditor/ckeditor'], ["block" => true]);
        }

        $options = $this->setEditorToolbar($options);

        if (strpos($fieldName, '.')) {
            [, $field] = explode('.', $fieldName);
        } else {
            $field = $fieldName;
        }
        if ($options['editorUseDraft']) {
            $options = $this->setDraft($fieldName, $options);
            $field .= '_tmp';
            $fieldName .= '_tmp';
        }
        if(strpos($fieldName, '.') !== false) {
            $dom = explode('.', $fieldName);
            $domId = Inflector::camelize($dom[0]) . Inflector::camelize($dom[1]);
        } else {
            $domId = Inflector::camelize($fieldName);
        }

        $fieldCamelize = Inflector::camelize($field);

        // 2022/10/10 ryuring
        // block を true にしないと、JSファイルが２回呼び出されてしまう。
        // 原因不明
        $script = $this->BcHtml->script('ckeditor.bundle', [
            "block" => false,
            'id' => 'CkeditorScript',
            'data-ckeditorField' => "editor_{$field}",
            'data-editorStylesSet' => $options['editorStylesSet'],
            'data-editorEnterBr' => $options['editorEnterBr'],
            'data-editorDomId' => $domId,
            'data-editorUseDraft' => $options['editorUseDraft'],
            'data-publishAreaId' => $options['publishAreaId'] ?? null,
            'data-draftAreaId' => $options['draftAreaId'] ?? null,
            'data-editorReadonlyPublish' => $options['editorReadOnlyPublish'],
            'data-editorDisableDraft' => $options['editorDisableDraft'],
            'data-editorDisablePublish' => $options['editorDisablePublish'],
            'data-previewModeId' => $options['editorPreviewModeId'],
            'data-editorUrl' => ($options['editorUseTemplates'])?
                $this->Url->build(['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'editor_templates', 'action' => 'js']) : '',
            'data-initialStyle' => json_encode($this->style),
            'data-editorStyle' => json_encode($options['editorStyles']),
            'data-themeEditorCsses' => json_encode($this->getThemeEditorCsses()),
            'data-editorOptions' => json_encode([
                'language' => $options['editorLanguage'],
                'skin' => $options['editorSkin'],
                'toolbar' => $options['editorToolbar'],
                'width' => $options['editorWidth'],
                'height' => $options['editorHeight'],
                'collapser' => $options['editorCollapser'],
                'baseFloatZIndex' => $options['editorBaseFloatZIndex'],
                'styles' => $options['editorStyles'],
            ])
        ]);

        return $script;
    }

    /**
     * ツールバーの設定
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setEditorToolbar($options)
    {
        if (!$options['editorToolbar']) {
            $options['editorToolbar'] = $this->toolbars[$options['editorToolType']];
            if ($options['editorUseTemplates']) {
                switch($options['editorToolType']) {
                    case 'simple':
                        $options['editorToolbar'][0][] = 'Templates';
                        break;
                    case 'normal':
                        $options['editorToolbar'][1][] = 'Templates';
                        break;
                }
            }
        }
        return $options;
    }

    /**
     * 下書きの設定
     * @param string $field
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     */
    public function setDraft($field, $options)
    {
        $lastBar = $options['editorToolbar'][count($options['editorToolbar']) - 1];
        $lastBar = array_merge($lastBar, ['-', 'Publish', '-', 'Draft']);
        if (!$options['editorDisableCopyDraft']) {
            $lastBar = array_merge($lastBar, ['-', 'CopyDraft']);
        }
        if (!$options['editorDisableCopyPublish']) {
            $lastBar = array_merge($lastBar, ['-', 'CopyPublish']);
        }
        $model = null;
        if(strpos('.', $field) !== false) {
            [$model, $field] = explode('.', $field);
        }
        $options['editorToolbar'][count($options['editorToolbar']) - 1] = $lastBar;
        $options['publishAreaId'] = Inflector::camelize($model? $model . '_' . $field : $field);
        $options['draftAreaId'] = Inflector::camelize($model? $model . '_' . $options['editorDraftField'] : $options['editorDraftField']);
        // フィールド名を変更するためセキュリティコンポーネントの対象外とする 2022/10/10 ryuring
        // TODO: 対象外とせずとも送信できるよう検討する
        $this->BcAdminForm->unlockField($options['editorDraftField']);
        return $options;
    }

    /**
     * エディタCSS設定
     * @return array
     * @checked
     * @noTodo
     */
    public function getThemeEditorCsses()
    {
        $themeEditorCsses = [];
        $site = $this->getView()->getRequest()->getAttribute('currentSite');
        if (!empty($site->theme)) {
            // $this->webroot で、フロントテーマのURLを取得できるようにするため、
            // 一旦テーマをフロントのテーマに切り替える
            $currentFrontTheme = $site->theme;
            $currentTheme = $this->getView()->getTheme();
            $this->getView()->setTheme($currentFrontTheme);
            if ($site->alias) {
                $themeEditorCsses = [
                    'path' => Plugin::path(Inflector::camelize($currentFrontTheme)) . 'webroot' . DS . 'css' . DS . $site->alias . DS . 'editor.css',
                    'url' => $this->Url->webroot('/css/' . $site->alias . '/editor.css')
                ];
            }
            $themeEditorCsses[] = [
                'path' => Plugin::path(Inflector::camelize($currentFrontTheme)) . 'webroot' . DS . 'css' . DS . 'editor.css',
                'url' => $this->Url->webroot('/css/editor.css')
            ];
            $this->getView()->setTheme($currentTheme);
        }
        foreach($themeEditorCsses as $key => $themeEditorCss) {
            if (!file_exists($themeEditorCss['path'])) {
                unset($themeEditorCsses[$key]);
            } else {
                $themeEditorCsses[$key] = $themeEditorCss['url'];
            }
        }
        return $themeEditorCsses;
    }

    /**
     * CKEditorのテキストエリアを出力する
     *
     * @param string $fieldName エディタのid, nameなどの名前を指定
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function editor($fieldName, $options = [])
    {
        if (!empty($options['editorUseDraft']) && !empty($options['editorDraftField'])) {
            $model = null;
            if(strpos('.', $fieldName) !== false) {
                [$model] = explode('.', $fieldName);
            }
            // フィールド名を変更するためセキュリティコンポーネントの対象外とする 2022/10/10 ryuring
            // TODO: 対象外とせずとも送信できるよう検討する
            $this->BcAdminForm->unlockField($fieldName);
            $inputFieldName = $fieldName . '_tmp';
            $hiddenIdElement = pluginSplit($fieldName);
            $hiddenId = $hiddenIdElement[0] . Inflector::camelize($hiddenIdElement[1]);
            $hidden = $this->BcAdminForm->hidden($fieldName, ['id' => $hiddenId]) .
                $this->BcAdminForm->hidden(
                    ($model)? $model . '.' . $options['editorDraftField'] : $options['editorDraftField'],
                    ['id' => $hiddenIdElement[0] . 'Draft']
                );
        } else {
            $inputFieldName = $fieldName;
            $hidden = '';
        }
        $options['type'] = 'textarea';
        $_options = [];
        foreach($options as $key => $option) {
            if (!preg_match('/^editor/', $key)) {
                $_options[$key] = $option;
            }
        }
        $textIdElement = pluginSplit($inputFieldName);
        $_options['id'] = $textIdElement[0] . Inflector::camelize($textIdElement[1]);
        return $this->BcAdminForm->control($inputFieldName, $_options) . $hidden . $this->buildTmpScript($fieldName, $options);
    }
}
