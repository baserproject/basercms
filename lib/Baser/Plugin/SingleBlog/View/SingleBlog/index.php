<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>

<h1><?php echo h($this->request->params['Content']['title']) ?></h1>

<?php if($datas): ?>
    <ul>
        <?php foreach($datas as $data): ?>
            <li>
                <?php echo $this->BcBaser->link($data['SingleBlogPost']['title'], array(
                    'plugin' => '',
                    'controller' => $this->request->params['Content']['url'],
                    'action' => 'view',
                    $data['SingleBlogPost']['id']
                )) ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

