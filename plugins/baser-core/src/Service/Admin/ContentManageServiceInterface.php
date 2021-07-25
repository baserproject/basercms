<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service\Admin;


/**
 * Interface PluginManageServiceInterface
 * @package BaserCore\Service
 */
interface ContentManageServiceInterface
{
     /**
      * コンテンツ情報を取得する
      * @return array
      */
      public function getContensInfo ();
      
}