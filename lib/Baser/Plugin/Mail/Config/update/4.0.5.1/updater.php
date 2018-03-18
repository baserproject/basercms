<?php
/**
 * 既存のアップロードファイルのパスを変更する
 */
ClassRegistry::flush();
CakePlugin::load('Mail');
$MailContent = ClassRegistry::init('Mail.MailContent');
$mailContents = $MailContent->find('all');
$files = WWW_ROOT . 'files' . DS;
$result = true;
if($mailContents) {
	foreach($mailContents as $mailContent) {
		$oldFiles = $files . 'mail' . DS . 'limited' . DS . $mailContent['Content']['name'];
		$newFiles = $files . 'mail' . DS . 'limited' . DS . $mailContent['MailContent']['id'];
		$Folder = new Folder();
		if(is_dir($oldFiles)) {
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
	$this->setUpdateLog(__d('baser', 'メールプラグインのアップロードファイルのパス変更に成功しました。'));
} else {
	$this->setUpdateLog(__d('baser', 'メールプラグインのアップロードファイルのパス変更に失敗しました。', true));
}