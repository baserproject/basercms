<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * baserCMS Contents Event Listener
 *
 * 階層コンテンツと連携したフォーム画面を表示する為のイベント
 * BcContentsComponent でコントロールされる
 *
 * @package Baser.Event
 */
class BcContentsEventListener extends Object implements CakeEventListener {

/**
 * Implemented Events
 *
 * @return array
 */
	public function implementedEvents() {
		return [
			'Helper.Form.beforeCreate' => ['callable' => 'formBeforeCreate'],
			'Helper.Form.afterCreate' => ['callable' => 'formAfterCreate'],
			'Helper.Form.afterSubmit' => ['callable' => 'formAfterSubmit']
		];
	}

/**
 * Form Before Create
 *
 * @param CakeEvent $event
 */
	public function formBeforeCreate(CakeEvent $event) {
		if(!BcUtil::isAdminSystem()) {
			return;
		}
		$event->data['options']['type'] = 'file';
	}

/**
 * Form After Create
 *
 * @param CakeEvent $event
 * @return string
 */
	public function formAfterCreate(CakeEvent $event) {
		if(!BcUtil::isAdminSystem()) {
			return;
		}
		$View = $event->subject();
		if($event->data['id'] == 'FavoriteAdminEditForm' || $event->data['id'] == 'PermissionAdminEditForm') {
			return;
		}
		if(!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->data['id'])) {
			return;
		}
		return $event->data['out'] . "\n" . $View->element('admin/content_fields');
	}

/**
 * Form After Submit
 *
 * フォームの保存ボタンの前後に、一覧、プレビュー、削除ボタンを配置する
 * プレビューを配置する場合は、設定にて、preview を true にする
 *
 * @param CakeEvent $event
 * @return string
 */
	public function formAfterSubmit(CakeEvent $event) {
		if(!BcUtil::isAdminSystem()) {
			return $event->data['out'];
		}
		$View = $event->subject();
		$data = $View->request->data;
		if(!preg_match('/(AdminEditForm|AdminEditAliasForm)$/', $event->data['id'])) {
			return $event->data['out'];
		}
		$output = $View->BcHtml->link('一覧に戻る', ['plugin' => '', 'admin' => true, 'controller' => 'contents', 'action' => 'index'], ['class' => 'button']);
		$setting = Configure::read('BcContents.items.' . $data['Content']['plugin'] . '.' . $data['Content']['type']);
		if (!empty($setting['preview']) && $data['Content']['type'] != 'ContentFolder') {
			$output .= "\n" . $View->BcForm->button('プレビュー', ['class' => 'button', 'id' => 'BtnPreview']);
		}
		$output .= $event->data['out'];
		if(empty($data['Content']['site_root'])) {
			if($data['Content']['alias_id']) {
				$deleteText = '削除';
			} else {
				$deleteText = 'ゴミ箱へ移動';
			}
			$output .= $View->BcForm->button($deleteText, ['class' => 'button', 'id' => 'BtnDelete']);
		}
		$event->data['out'] = $output;
		return $output;
	}

}
