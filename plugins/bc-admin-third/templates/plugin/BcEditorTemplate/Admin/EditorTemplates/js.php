<?php
/**
 * [ADMIN] エディタ設定用Javascript
 *
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\ORM\ResultSet $templates
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php
// 各値は JavaScript の文字列リテラルとして出力するため、json_encode() でリテラル化する。
// 文字列リテラルへ無エスケープで出力すると、テンプレートの名前・説明・HTML に含まれる
// 引用符等でリテラルから脱出し、全ての WYSIWYG 編集画面で任意のスクリプトが実行される。
// html はエディタへ挿入する HTML そのものなので内容は変更せず、リテラル化のみ行う。
$jsFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
?>
CKEDITOR.addTemplates('default',{
imagesPath:CKEDITOR.getUrl(<?php echo json_encode($this->BcBaser->Url->webroot('/files/editor/'), $jsFlags) ?>),
templates:[
<?php if ($templates): ?>
  <?php foreach($templates as $key => $template): ?>
    {
    title:<?php echo json_encode(h((string)$template->name), $jsFlags) ?>,
    <?php if ($template->image && file_exists(WWW_ROOT . 'files' . DS . 'editor' . DS . $template->image)): ?>
      image:<?php echo json_encode(h((string)$template->image), $jsFlags) ?>,
    <?php endif ?>
    description:<?php echo json_encode(preg_replace("/(\n|\r)/", "", nl2br(h((string)$template->description))), $jsFlags) ?>,
    html:<?php echo json_encode(preg_replace('/(\n|\r)/', '', (string)$template->html), $jsFlags) ?>
    }<?php if (!$this->BcArray->last($templates, $key)): ?>, <?php endif ?>
  <?php endforeach ?>
<?php endif ?>
]
});
