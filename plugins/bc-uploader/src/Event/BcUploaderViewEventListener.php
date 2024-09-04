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

namespace BcUploader\Event;

use BaserCore\Event\BcViewEventListener;
use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcHtmlHelper;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\View\View;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * UploaderViewEventListener
 *
 * @property BcHtmlHelper $BcHtml
 */
class BcUploaderViewEventListener extends BcViewEventListener
{

    /**
     * Events
     * @var string[]
     */
    public $events = [
        'afterLayout'
    ];

    /**
     * After Layout
     * @param Event $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterLayout(Event $event)
    {
        /** @var View $view */
        $view = $event->getSubject();
        if (!BcUtil::isAdminSystem()
            || $view->getName() === 'CakeError'
            || !$view->helpers()->has('BcHtml')
            || !$view->helpers()->has('BcCkeditor')
        ) {
            return;
        }

        $view->assign('content', $this->setUploader(
            $view->helpers()->get('BcHtml'),
            $view->fetch('content'),
            $view->getRequest()->getAttribute('base')
        ));
    }

    private function setUploader(BcHtmlHelper $bcHtmlHelper, string $content, string $base): string
    {
        if (preg_match_all('/"ckeditorField":"editor_(.+?)"/', $content, $matches)) {
            /* JSを読み込む */
            $jscode = $bcHtmlHelper->scriptBlock("var baseUrl ='" . $base . "/';");
            $jscode .= $bcHtmlHelper->scriptBlock("var adminPrefix ='" . BcUtil::getAdminPrefix() . "';");
            $jscode .= $bcHtmlHelper->i18nScript([
                'ckeditorTitle' => __d('baser_core', 'ファイルプロパティ'),
                'ckeditorAlertMessage1' => __d('baser_core', '画像を選択するか、URLを直接入力して下さい。'),
                'ckeditorInfoLabel' => __d('baser_core', 'イメージ情報'),
                'ckeditorInfoTitle' => __d('baser_core', 'イメージ情報'),
                'ckeditorDescriptionLabel' => __d('baser_core', '説明文'),
                'ckeditorCaptionLabel' => __d('baser_core', 'キャプション'),
                'ckeditorHspaceLabel' => __d('baser_core', '横間隔'),
                'ckeditorVspaceLabel' => __d('baser_core', '縦間隔'),
                'ckeditorAlignLabel' => __d('baser_core', '行揃え'),
                'ckeditorLeft' => __d('baser_core', '左'),
                'ckeditorAbsBottom' => __d('baser_core', '下部(絶対的)'),
                'ckeditorAbsMiddle' => __d('baser_core', '中央(絶対的)'),
                'ckeditorBaseline' => __d('baser_core', 'ベースライン'),
                'ckeditorBottom' => __d('baser_core', '下'),
                'ckeditorMiddle' => __d('baser_core', '中央'),
                'ckeditorRight' => __d('baser_core', '右'),
                'ckeditorTextTop' => __d('baser_core', 'テキスト上部'),
                'ckeditorTop' => __d('baser_core', '上'),
                'ckeditorSizeLabel' => __d('baser_core', 'サイズ'),
                'ckeditorOriginSize' => __d('baser_core', '元サイズ'),
                'ckeditorSmall' => __d('baser_core', '小'),
                'ckeditorMidium' => __d('baser_core', '中'),
                'ckeditorLarge' => __d('baser_core', '大'),
            ], ['block' => false]);
            $uploaderFilesTable = TableRegistry::getTableLocator()->get('BcUploader.UploaderFiles');
            $jscode .= $bcHtmlHelper->script('BcUploader.admin/uploader_files/ckeditor_uploader.bundle', [
                'id' => 'CkeditorUploaderScript',
                'block' => false,
                'data-imageSettings' => json_encode($uploaderFilesTable->getSettings()['fields']['name']['imagecopy']),
                'data-loaderUrl' => $bcHtmlHelper->Url->image('admin/ajax-loader.gif')
            ]);
            $content = str_replace('</head>', $jscode . '</head>', $content);

            /* CSSを読み込む */
            // 適用の優先順位の問題があるので、bodyタグの直後に読み込む
            $css = $bcHtmlHelper->css('admin/uploader_files/index');
            $content = str_replace('</body>', $css . '</body>', $content);

            /* VIEWのCKEDITOR読込部分のコードを書き換える */
            foreach($matches[1] as $match) {
                $jscode = $this->__getCkeditorUploaderScript($bcHtmlHelper, $match);
                $pattern = "/<script>(.*?let config.+?\"ckeditorField\":\"editor_" . $match . "\".+?)<\/script>/ms";
                $matchOutput = preg_replace($pattern, $bcHtmlHelper->scriptBlock("$1\n" . $jscode . "\n"), $content);
                if (!is_null($matchOutput)) $content = $matchOutput;
                /* 通常の画像貼り付けダイアログを画像アップローダーダイアログに変換する */
                $pattern = '/("ckeditorField":"editor_' . $match . '".+?)Image(.+?")/';
                $matchOutput = preg_replace($pattern, "$1BaserUploader$2", $content);
                if (!is_null($matchOutput)) $content = $matchOutput;
            }
        }
        return $content;
    }

    /**
     * CKEditorのアップローダーを組み込む為のJavascriptを返す
     *
     * 「baserUploader」というコマンドを登録し、そのコマンドが割り当てられてボタンをツールバーに追加する
     * {EDITOR_NAME}.addCommand    // コマンドを追加
     * {EDITOR_NAME}.addButton    // ツールバーにボタンを追加
     * ※ {EDITOR_NAME} は、コントロールのIDに変換する前提
     *
     * @param BcHtmlHelper $bcHtmlHelper
     * @param string $id
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    private function __getCkeditorUploaderScript(BcHtmlHelper $bcHtmlHelper, string $id)
    {
        $fieldName = 'editor_' . Inflector::underscore($id);
        $css = $bcHtmlHelper->Url->css('admin/uploader_files/contents.css');
        $label = __d('baser_core', 'アップローダー');
        return <<< DOC_END
			$(function(){
				if(!(CKEDITOR.config.contentsCss instanceof Array)) {
					CKEDITOR.config.contentsCss = [CKEDITOR.config.contentsCss];
				}
				CKEDITOR.config.contentsCss.push('{$css}');
				$.bcCkeditor.editor['{$fieldName}'].on( 'pluginsLoaded', function( ev ) {
					$.bcCkeditor.editor['{$fieldName}'].addCommand( 'baserUploader', new CKEDITOR.dialogCommand( 'baserUploaderDialog' ));
					$.bcCkeditor.editor['{$fieldName}'].ui.addButton( 'BaserUploader', { icon: 'image', label : '{$label}', command : 'baserUploader' });
				});
			});
DOC_END;
    }

}
