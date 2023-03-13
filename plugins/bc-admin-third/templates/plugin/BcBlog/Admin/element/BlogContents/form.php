<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use Cake\Routing\Router;

/**
 * [ADMIN] ブログコンテンツ フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $editorEnterBr
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<section class="bca-section" data-bca-section-type="form-group">
  <table class="form-table bca-form-table" data-bca-table-type="type2">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('description', __d('baser_core', 'ブログ説明文')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php
        echo $this->BcAdminForm->editor('description', [
          'editorWidth' => 'auto',
          'editorHeight' => '120px',
          'editorToolType' => 'simple',
          'editorEnterBr' => $editorEnterBr
        ])
        ?>
        <?php echo $this->BcAdminForm->error('description') ?>
      </td>
    </tr>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</section>

<section class="bca-section" data-bca-section-type="form-group">
  <div class="bca-collapse__action">
    <button
      type="button"
      class="bca-collapse__btn"
      data-bca-collapse="collapse"
      data-bca-target="#blogContentsSettingBody"
      aria-expanded="false"
      aria-controls="blogContentsSettingBody">
      詳細設定&nbsp;&nbsp;
      <i class="bca-icon--chevron-down bca-collapse__btn-icon"></i>
    </button>
  </div>
  <div class="bca-collapse" id="blogContentsSettingBody" data-bca-state="">
    <table class="form-table bca-form-table" data-bca-table-type="type2">
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('list_count', __d('baser_core', '一覧表示件数')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('list_count', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
          &nbsp;件&nbsp;
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('list_count') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', '公開サイトの一覧に表示する件数を指定します。') ?></li>
              <li><?php echo __d('baser_core', '半角数字で入力してください。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('list_direction', __d('baser_core', '一覧に表示する順番')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('list_direction', ['type' => 'select', 'options' => ['DESC' => __d('baser_core', '新しい記事順'), 'ASC' => __d('baser_core', '古い記事順')]]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('list_direction') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', '公開サイトの一覧における記事の並び方向を指定します。') ?></li>
              <li><?php echo __d('baser_core', '新しい・古いの判断は投稿日が基準となります。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('list_count', __d('baser_core', 'RSSフィード出力件数')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('feed_count', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
          &nbsp;件&nbsp;
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('feed_count') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'RSSフィードに出力する件数を指定します。') ?></li>
              <li><?php echo __d('baser_core', '半角数字で入力してください。') ?></li>
              <?php if ($this->getRequest()->getParam('action') === 'admin_edit'): ?>
                <li><?php echo __d('baser_core', 'RSSフィードのURL') ?>&nbsp;
                  <?php $this->BcBaser->link(Router::url('/' . $this->BcAdminForm->getSourceValue('Content.name') . '/index.rss', true), '/' . $this->BcAdminForm->getSourceValue('Content.name') . '/index.rss', ['target' => '_blank']) ?>
                </li>
              <?php endif ?>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('comment_use', __d('baser_core', 'コメント受付機能')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('comment_use', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
          <?php echo $this->BcAdminForm->error('comment_use') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('comment_approve', __d('baser_core', 'コメント承認機能')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('comment_approve', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('comment_approve') ?>
          <div class="bca-helptext"><?php echo __d('baser_core', '承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。') ?></div>
        </td>
      </tr>

			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('auth_capthca', __d('baser_core', 'コメントイメージ認証')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcAdminForm->control('auth_captcha', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcAdminForm->error('auth_captcha') ?>
					<div class="bca-helptext">
						<ul>
							<li><?php echo __d('baser_core', 'ブログコメント送信の際、表示された画像の文字入力させる事で認証を行ないます。') ?></li>
							<li><?php echo __d('baser_core', 'スパムなどいたずら送信が多いが多い場合に設定すると便利です。') ?></li>
						</ul>
					</div>
				</td>
			</tr>

      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('tag_use', __d('baser_core', 'タグ機能')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('tag_use', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
          <?php echo $this->BcAdminForm->error('tag_use') ?>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('widget_area', __d('baser_core', 'ウィジェットエリア')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required">
            <?php echo __d('baser_core', '必須') ?>
          </span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('widget_area', [
            'type' => 'select',
            'options' => $this->BcAdminForm->getControlsource('BcWidgetArea.WidgetAreas.id'),
            'empty' => __d('baser_core', 'サイト基本設定に従う')
          ]) ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('widget_area') ?>
          <div class="bca-helptext">
            <?php echo __d('baser_core', 'ブログコンテンツで利用するウィジェットエリアを指定します。') ?><br>
            <?php echo __d('baser_core', 'ウィジェットエリアはウィジェットエリア管理より追加できます。') ?><br>
            <ul>
              <li><?php //$this->BcBaser->link(__d('baser_core', 'ウィジェットエリア管理'), ['plugin' => null, 'controller' => 'widget_areas', 'action' => 'index']) ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('template', __d('baser_core', 'コンテンツテンプレート名')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <?php
          echo $this->BcAdminForm->control('template', [
            'type' => 'select',
            'options' => $this->Blog->getBlogTemplates($blogContent->content->site_id)])
          ?>
          <?php echo $this->BcAdminForm->control('edit_blog', ['type' => 'hidden']) ?>
          <?php $this->BcAdminForm->unlockField('edit_blog') ?>
          <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
            <?php $this->BcBaser->link('<i class="bca-icon--edit"></i>' . __d('baser_core', '編集する'), 'javascript:void(0)', [
              'id' => 'EditBlogTemplate',
              'escape' => false
            ]) ?>
          <?php endif ?>
          <i class="bca-icon--question-circle bca-help"></i>
          <?php echo $this->BcAdminForm->error('template') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'ブログの本体のテンプレートを指定します。') ?></li>
              <li><?php echo __d('baser_core', '「編集する」からテンプレートの内容を編集する事ができます。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('eye_catch_size_thumb_width', __d('baser_core', 'アイキャッチ画像サイズ')) ?>&nbsp;
          <span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
        </th>
        <td class="col-input bca-form-table__input">
          <table class="bca-table" data-bca-table-type="type1">
            <thead>
            <tr>
              <th></th>
              <th><?php echo __d('baser_core', '幅') ?></th>
              <th><?php echo __d('baser_core', '高さ') ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <th><?php echo __d('baser_core', 'PCサイズ') ?></th>
              <td><?php echo $this->BcAdminForm->control('eye_catch_size_thumb_width', ['type' => 'text', 'size' => '8']) ?>
                &nbsp;px
              </td>
              <td><?php echo $this->BcAdminForm->control('eye_catch_size_thumb_height', ['type' => 'text', 'size' => '8']) ?>
                px
              </td>
            </tr>
            <tr>
              <th><?php echo __d('baser_core', '携帯サイズ') ?></th>
              <td><?php echo $this->BcAdminForm->control('eye_catch_size_mobile_thumb_width', ['type' => 'text', 'size' => '8']) ?>
                &nbsp;px
              </td>
              <td><?php echo $this->BcAdminForm->control('eye_catch_size_mobile_thumb_height', ['type' => 'text', 'size' => '8']) ?>
                px
              </td>
            </tr>
            </tbody>
          </table>
          <?php echo $this->BcAdminForm->error('eye_catch_size_thumb_width') ?>
          <div class="bca-helptext">
            <ul>
              <li><?php echo __d('baser_core', 'アイキャッチ画像のサイズを指定します。') ?></li>
            </ul>
          </div>
        </td>
      </tr>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('use_content', __d('baser_core', '記事概要')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('use_content', ['type' => 'checkbox', 'label' => __d('baser_core', '利用する')]) ?>
          <?php echo $this->BcAdminForm->error('use_content') ?>
        </td>
      </tr>
      <?php echo $this->BcAdminForm->dispatchAfterForm('option') ?>
    </table>
  </div>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

