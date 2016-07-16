<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

<h1><?php echo h($this->content['title']) ?></h1>

<?php if($datas): ?>
    <ul>
        <?php foreach($datas as $data): ?>
            <li>
                <?php echo $this->BcBaser->link($data['SingleBlogPost']['title'], array(
                    'plugin' => '',
                    'controller' => $this->content['url'],
                    'action' => 'view',
                    $data['SingleBlogPost']['id']
                )) ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

