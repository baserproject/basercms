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
 * RSS
 *
 * @var \BcBlog\View\BlogFrontAppView $this
 * @var \Cake\ORM\ResultSet $posts
 */
if (!$posts->count()) return;

echo $this->Rss->items($posts->toArray(), function($entity) {
  $view = new Cake\View\View();
  $blogHelper = new \BcBlog\View\Helper\BlogHelper($view);
  $bcBaserhelper = new \BaserCore\View\Helper\BcBaserHelper($view);
  $url = $bcBaserhelper->getContentsUrl(null, false, null, false) . 'archives/' . ($entity->name)? $entity->name : $entity->no;
  $eyeCatch = [
    'url' => '',
    'type' => '',
    'length' => '',
  ];
  if (!empty($entity->eye_catch)) {
    $eyeCatch['url'] = \Cake\Routing\Router::url($blogHelper->getEyeCatch($entity, ['imgsize' => '', 'output' => 'url']), true);
  }
  return [
    'title' => $entity->title,
    'link' => $url,
    'guid' => $url,
    'category' => $entity->category->title?? null,
    'description' => $blogHelper->removeCtrlChars($entity->content . $entity->detail),
    'pubDate' => $entity->posted,
    'enclosure' => $eyeCatch,
    'convertEntities' => false
  ];
});
