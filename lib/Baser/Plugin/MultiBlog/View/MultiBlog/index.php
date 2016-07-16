<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<h1><?php echo h($this->content['title']) ?></h1>
<p><?php echo h($blogContent['MultiBlogContent']['content']) ?></p>

<?php if($datas): ?>
    <ul>
        <?php foreach($datas as $data): ?>
            <li>
                <?php echo $this->BcBaser->link($data['MultiBlogPost']['title'], array(
                    'plugin' => '',
                    'controller' => $this->content['url'],
                    'action' => 'view',
                    $data['MultiBlogPost']['no']
                )) ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

