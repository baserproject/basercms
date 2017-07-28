<?php
/**
 * 既存のアップロードファイルのパスを変更する
 */
ClassRegistry::flush();
CakePlugin::load('Blog');
$BlogContent = ClassRegistry::init('Blog.BlogContent');
$blogContents = $BlogContent->find('all');
$files = WWW_ROOT . 'files' . DS;
$result = true;
if($blogContents) {
	foreach($blogContents as $blogContent) {
		$oldFiles = $files . 'blog' . DS . $blogContent['Content']['name'];
		$newFiles = $files . 'blog' . DS . $blogContent['BlogContent']['id'];
		if(is_dir($oldFiles)) {
			$Folder = new Folder();
			if (!$Folder->move([
				'to' => $newFiles,
				'from' => $oldFiles,
				'mode' => 0777
			])) {
				$result = false;
			};
		}
	}
}
if($result) {
	$this->setUpdateLog('ブログプラグインのアップロードファイルのパス変更に成功しました。');
} else {
	$this->setUpdateLog('ブログプラグインのアップロードファイルのパス変更に失敗しました。', true);
}