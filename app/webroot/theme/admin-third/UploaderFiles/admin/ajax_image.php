<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			https://basercms.net/license/index.html
 */
$url = $this->BcBaser->getUrl($this->Uploader->getFileUrl($file['UploaderFile']['name']));
?>


<p class="url">
	<a href="<?php echo $url  ?>" target="_blank"><?php echo FULL_BASE_URL.$url ?></a>
</p>
<p class="image">
	<a href="<?php echo $url ?>" target="_blank"><?php echo $this->Uploader->file($file, ['size' => $size, 'alt' => $file['UploaderFile']['name']]) ?></a>
</p>