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
 * [ADMIN] ブログカテゴリ 一覧　行
 * @var BcBlog\View\BlogAdminAppView $this
 * @var string $rowGroupClass
 * @var BcBlog\Model\Entity\BlogContent $blogContent
 * @var BcBlog\Model\Entity\BlogCategory $blogCategory
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<tr<?php echo $rowGroupClass ?>>
    <td class="row-tools bca-table-listup__tbody-td">
        <?php if ($this->BcBaser->isAdminUser()): ?>
            <?php echo $this->BcAdminForm->control('batch_targets.' . $blogCategory->id, [
                'type' => 'checkbox',
                'label' => '<span class="bca-visually-hidden">チェックする</span>',
                'class' => 'batch-targets bca-checkbox__input',
                'value' => $blogCategory->id,
                'escape' => false
            ]) ?>
        <?php endif ?>
    </td>
    <td class="bca-table-listup__tbody-td"><?php echo $blogCategory->no ?></td>
    <td class="bca-table-listup__tbody-td">
        <?php $this->BcBaser->link(
            $blogCategory->name,
            ['action' => 'edit', $blogContent->id, $blogCategory->id]
        ) ?>
    </td>
    <td class="bca-table-listup__tbody-td"><?php echo html_entity_decode(h($blogCategory->layered_title)) ?></td>
    <?php echo $this->BcListTable->dispatchShowRow($blogCategory) ?>
    <td class="bca-table-listup__tbody-td">
        <?php echo $this->BcTime->format($blogCategory->created, 'yyyy-MM-dd'); ?>
        <br/>
        <?php echo $this->BcTime->format($blogCategory->modified, 'yyyy-MM-dd'); ?>
    </td>
    <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
        <?php $this->BcBaser->link('', $this->Blog->getCategoryUrl($blogCategory->id), [
                'title' => __d('baser', '確認'),
                'target' => '_blank',
                'class' => 'bca-btn-icon',
                'data-bca-btn-type' => 'preview',
                'data-bca-btn-size' => 'lg']
        ) ?>
        <?php if (\BaserCore\Utility\BcUtil::isAdminUser()): ?>
            <?php $this->BcBaser->link('',
                ['action' => 'edit', $blogContent->id, $blogCategory->id],
                [
                    'title' => __d('baser', '編集'),
                    'class' => 'bca-btn-icon',
                    'data-bca-btn-type' => 'edit',
                    'data-bca-btn-size' => 'lg'
                ]
            ) ?>
            <?= $this->BcAdminForm->postLink('',
                ['action' => 'delete', $blogContent->id, $blogCategory->id],
                [
                    'confirm' => __d('baser', "このデータを本当に削除してもいいですか？\n\nこのカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。"),
                    'title' => __d('baser', '削除'),
                    'class' => 'btn-delete bca-btn-icon',
                    'data-bca-btn-type' => 'delete',
                    'data-bca-btn-size' => 'lg',
                ]
            ) ?>
        <?php endif ?>
    </td>
</tr>
