<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ情報
 * @var string $mainSiteDisplayName メインサイト表示名称
 */
?>


<?php if ($this->request->action === 'admin_edit' || $this->request->action === 'admin_edit_alias'): ?>
	<section id="EtcSetting" class="bca-section" data-bca-section-type="form-group">
		<div class="bca-collapse__action">
			<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
					data-bca-target="#formContentsInfoBody" aria-expanded="false"
					aria-controls="formOptionBody"><?php echo __d('baser', 'その他情報') ?>&nbsp;&nbsp;<i
					class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
		</div>
		<div class="bca-collapse" id="formContentsInfoBody" data-bca-state="">
			<div class="bca-box">
				<ul class="bca-list" data-bca-list-layout="horizon" data-bca-list-type='circle'>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'コンテンツID') ?></span>：<?php echo $this->request->data['Content']['id'] ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', '実体ID') ?></span>：<?php echo $this->request->data['Content']['entity_id'] ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'プラグイン') ?></span>：<?php echo $this->request->data['Content']['plugin'] ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'コンテンツタイプ') ?></span>：<?php echo $this->request->data['Content']['type'] ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'データ作成日') ?></span>：<?php echo $this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['created']) ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'データ更新日') ?></span>：<?php echo $this->BcTime->format('Y/m/d H:i:s', $this->request->data['Content']['modified']) ?>
					</li>
					<li class="bca-list__item">
						<span><?php echo __d('baser', 'サイト') ?></span>：<?php echo h($this->BcText->noValue($this->request->data['Site']['display_name'], $mainSiteDisplayName)) ?>
					</li>
					<li class="bca-list__item"><span><?php echo __d('baser', 'タイプ') ?></span>：
						<?php if (!$this->BcForm->value('Content.alias_id')): ?>
							<?php if (!empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
								<?php echo h($this->BcContents->settings[$this->BcForm->value('Content.type')]['title']) ?>
							<?php else: ?>
								<?php echo __d('baser', 'デフォルト') ?>
							<?php endif ?>
						<?php else: ?>
							<?php echo __d('baser', 'エイリアス') ?>
						<?php endif ?>
						<?php if (empty($this->BcContents->settings[$this->BcForm->value('Content.type')])): ?>
							<p class="bca-notice"><?php echo __d('baser', 'タイプ「デフォルト」は、プラグインの無効処理等が理由となり、タイプとの関連付けが外れてしまっている状態です。<br>プラグインがまだ存在する場合は有効にしてください。') ?></p>
						<?php endif ?>
					</li>
				</ul>
			</div>
		</div>
	</section>
<?php endif ?>
