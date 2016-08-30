<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiBlog.Model
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * MultiBlogContent
 *
 * @package MultiBlog.Model
 * @property MultiBlogPost $MultiBlogPost
 * @property Content $Content
 */
class MultiBlogContent extends AppModel {

/**
 * Behavior Setting
 *
 * @var array
 */
	public $actsAs = array('BcContents');

/**
 * belongsTo
 *
 * @var array
 */
    public $hasMany = array(
        'MultiBlogPost' => array(
            'className'	=> 'MultiBlog.MultiBlogPost',
            'foreignKey'=> 'blog_content_id',
            'dependent'	=> true
        )
    );

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = array(
		'content' => array(
			array(
				'rule'		=> array('notBlank'),
				'message'	=> 'ブログの内容を入力してください。',
				'required'	=> true
			)
		)
	);

/**
 * ブログをコピーする
 *
 * @param $id
 * @param $title
 * @param $authorId
 * @return bool|mixed
 */
	public function copy($id, $newTitle, $newAuthorId, $newSiteId = null) {
		$data = $this->find('first', array('conditions' => array('MultiBlogContent.id' => $id)));
		if(!$data) {
			return false;
		}
		unset($data['MultiBlogContent']['id']);
		unset($data['MultiBlogContent']['modified']);
		unset($data['MultiBlogContent']['created']);
		$this->getDataSource()->begin();
		$result = $this->save($data['MultiBlogContent']);
		if($result) {
			if(!empty($data['MultiBlogPost'])) {
				$no = 1;
				foreach($data['MultiBlogPost'] as $post) {
					unset($post['id']);
					unset($post['modified']);
					unset($post['created']);
					$post['blog_content_id'] = $this->id;
					$post['no'] = $no;
					$this->MultiBlogPost->create($post);
					if(!$this->MultiBlogPost->save()) {
						$result = false;
					}
				}
			}
		}
		if ($result) {
			$data = $this->Content->copy($data['Content']['id'], $this->id, $newTitle, $newAuthorId, $newSiteId);
			if($data) {
				$this->getDataSource()->commit();
				return $data;
			}
		}
		$this->getDataSource()->rollback();
		return false;
	}

}