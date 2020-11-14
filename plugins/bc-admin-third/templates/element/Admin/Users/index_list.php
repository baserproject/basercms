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

use BaserCore\View\AppView;

/**
 * users index list
 * @var AppView $this
 * @var Cake\ORM\ResultSet $users
 */

$this->BcListTable->setColumnNumber(7);
?>


<?php $this->BcBaser->element('Admin/pagination') ?>

<table class="list-table bca-table-listup" id="ListTable">
    <thead class="bca-table-listup__thead">
    <tr>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('id',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'No'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'No')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('name',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'アカウント名'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'アカウント名')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('email',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'Eメール'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'Eメール')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('nickname',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', 'ニックネーム'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', 'ニックネーム')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <th class="bca-table-listup__thead-th">
            <?php echo __d('baser', 'グループ') ?>
        </th>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('real_name_1',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '氏名'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '氏名')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <?php echo $this->BcListTable->dispatchShowHead() ?>
        <th class="bca-table-listup__thead-th">
            <?php echo $this->Paginator->sort('created',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '登録日'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '登録日')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?><br/>
            <?php echo $this->Paginator->sort('modified',
                [
                    'asc' => '<i class="bca-icon--asc" title="' . __d('baser', '昇順') . '"></i>' . __d('baser', '更新日'),
                    'desc' => '<i class="bca-icon--desc" title="' . __d('baser', '降順') . '"></i>' . __d('baser', '更新日')
                ],
                ['escape' => false, 'class' => 'btn-direction bca-table-listup__a']) ?>
        </th>
        <th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if ($users->count()): ?>
        <?php foreach($users as $user): ?>
            <?php $this->BcBaser->element('Admin/Users/index_row', ['user' => $user]) ?>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
                <p class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php $this->BcBaser->element('Admin/list_num') ?>
