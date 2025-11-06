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

foreach ($metaData as $meta) {
  if (!empty($meta['property'])) {
    echo $this->BcHtml->meta(['property' => $meta['property'], 'content' => $meta['value']]) . PHP_EOL;
  } elseif (!empty($meta['name'])) {
    echo $this->BcHtml->meta(['name' => $meta['name'], 'content' => $meta['value']]) . PHP_EOL;
  } elseif (!empty($meta['rel'])) {
    echo $this->BcHtml->meta(['rel' => $meta['rel'], 'link' => $meta['value']]) . PHP_EOL;
  }
}
