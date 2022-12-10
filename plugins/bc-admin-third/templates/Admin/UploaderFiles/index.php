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

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->css('admin/uploader_files/index', false);
$this->BcAdmin->setSearch('uploader_files_index');
$this->BcAdmin->setTitle(__d('baser', 'アップロードファイル一覧'));
?>


<?php $this->BcBaser->element('UploaderFiles/index') ?>
