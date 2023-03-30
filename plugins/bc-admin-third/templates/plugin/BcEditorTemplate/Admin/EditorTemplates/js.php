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


CKEDITOR.addTemplates('default',{
imagesPath:CKEDITOR.getUrl('<?php echo $this->BcBaser->Url->webroot('/files/editor/') ?>'),
templates:[
<?php if ($templates): ?>
  <?php foreach($templates as $key => $template): ?>
    {
    title:'<?php echo $template->name ?>',
    <?php if (file_exists(WWW_ROOT . 'files' . DS . 'editor' . DS . $template->image)): ?>
      image:'<?php echo $template->image ?>',
    <?php endif ?>
    description:'<?php echo preg_replace("/(\n|\r)/", "", nl2br($template->description)) ?>',
    html:'<?php echo preg_replace('/(\n|\r)/', '', $template->html) ?>'
    }<?php if (!$this->BcArray->last($templates, $key)): ?>, <?php endif ?>
  <?php endforeach ?>
<?php endif ?>
]
});
