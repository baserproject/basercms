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
 * @var string $layoutType
 * @checked
 * @noTodo
 * @unitTest
 */
if ($layoutType === 'table') {
  $this->BcBaser->element('UploaderFiles/index_list_table');
} else {
  $this->BcBaser->element('UploaderFiles/index_list_panel');
}
