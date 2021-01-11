<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View.Helper
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcSearchIndexHelper
 */
class BcSearchIndexHelper extends AppHelper
{

	/**
	 * 公開状態確認
	 *
	 * @param array $data
	 * @return bool
	 */
	public function allowPublish($data)
	{
		return ClassRegistry::init('SearchIndex')->allowPublish($data);
	}

}
