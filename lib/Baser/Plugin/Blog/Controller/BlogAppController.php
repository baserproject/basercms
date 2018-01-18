<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログコントローラー基底クラス
 *
 * @package			Blog.Controller
 * @property BlogPost $BlogPost
 * @property BlogCategory $BlogCategory
 */
class BlogAppController extends AppController {

/**
 * コメントを管理者メールへメール送信する
 * 
 * @param int $postId
 * @param array $data
 * @return boolean
 */
	protected function _sendCommentAdmin($postId, $data) {
		if (!$postId || !$data || empty($this->siteConfigs['email'])) {
			return false;
		}

		$data = array_merge($data, $this->BlogPost->find('first', [
				'conditions' => ['BlogPost.id' => $postId],
				'recursive' => 0
		]));
		$data['SiteConfig'] = $this->siteConfigs;
		$to = $this->siteConfigs['email'];
		$title = '【' . $this->siteConfigs['name'] . '】コメントを受け付けました';
		return $this->sendMail($to, $title, $data, [
				'template' => 'Blog.blog_comment_admin',
				'agentTemplate' => false
		]);
	}

/**
 * コメント投稿者にアラートメールを送信する
 * 
 * @param int $postId
 * @param array $data
 * @return boolean 
 */
	protected function _sendCommentContributor($postId, $data) {
		if (!$postId || !$data || empty($this->siteConfigs['email'])) {
			return false;
		}

		$_data = $this->BlogPost->find('first', [
			'conditions' => [
				'BlogPost.id' => $postId
			],
			'recursive' => 1
		]);

		// 公開されているコメントがない場合は true を返して終了
		if (empty($_data['BlogComment'])) {
			return true;
		}

		$blogComments = $_data['BlogComment'];
		unset($_data['BlogComment']);
		$data = array_merge($_data, $data);

		$data['SiteConfig'] = $this->siteConfigs;
		$title = '【' . $this->siteConfigs['name'] . '】コメントが投稿されました';

		$result = true;
		$sended = [];
		foreach ($blogComments as $blogComment) {
			if ($blogComment['email'] && $blogComment['status'] && !in_array($blogComment['email'], $sended) && $blogComment['email'] != $data['BlogComment']['email']) {
				$result = $this->sendMail($blogComment['email'], $title, $data, [
					'template' => 'Blog.blog_comment_contributor',
					'agentTemplate' => false
				]);
			}
			$sended[] = $blogComment['email'];
		}

		return $result;
	}

}
