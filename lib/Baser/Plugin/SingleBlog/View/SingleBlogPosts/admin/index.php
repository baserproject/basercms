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


<?php $this->BcBaser->element('pagination') ?>

<table class="list-table">
    <thead>
        <tr>
            <th>
                <?php echo $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array(
                    'plugin'    => 'single_blog',
                    'controller'=> 'single_blog_posts',
                    'action'    => 'add'
                )) ?>
            </th>
            <th><?php echo $this->Paginator->sort('title', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' タイトル', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' タイトル'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
            <th><?php echo $this->Paginator->sort('created', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 作成日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 作成日'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
            <th><?php echo $this->Paginator->sort('modified', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 編集日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 編集日'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
        </tr>
    </thead>
<?php if($datas): ?>
    <tbody>
    <?php foreach($datas as $data): ?>
        <tr>
            <td>
                <?php echo $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_check.png', array('alt' => '編集', 'class' => 'btn')), $content['Content']['url'] . '/view/' . $data['SingleBlogPost']['id'], array('target' => '_blank')) ?>
                <?php echo $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => '編集', 'class' => 'btn')), array(
                    'plugin' => 'single_blog',
                    'controller' => 'single_blog_posts',
                    'action' => 'edit',
                    $data['SingleBlogPost']['id']
                )) ?>
                <?php echo $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', array('alt' => '削除', 'class' => 'btn')), array(
                    'plugin'    => 'single_blog',
                    'controller'=> 'single_blog_posts',
                    'action'    => 'delete',
                    $data['SingleBlogPost']['id']
                ), array(), '記事を削除します。本当にいいですか？') ?>
            </td>
            <td>
                <?php echo $this->BcBaser->link($data['SingleBlogPost']['title'], array(
                    'plugin' => 'single_blog',
                    'controller' => 'single_blog_posts',
                    'action' => 'edit',
                    $data['SingleBlogPost']['id']
                )) ?>
            </td>
            <td style="width:10%;white-space: nowrap">
                <?php echo $this->BcTime->format('Y-m-d', $data['SingleBlogPost']['created']) ?>
            </td>
            <td style="width:10%;white-space: nowrap">
                <?php echo $this->BcTime->format('Y-m-d', $data['SingleBlogPost']['modified']) ?>
            </td>
        </tr>
    <?php endforeach ?>
<?php else: ?>
        <tr><td colspan="4"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif ?>
    </tbody>
</table>
