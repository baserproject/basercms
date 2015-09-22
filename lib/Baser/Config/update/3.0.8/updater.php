<?php
/**
 * シリアライズされたデータを更新
 *
 * SiteConfig.content_categories
 * SiteConfig.content_types
 */
App::uses('SiteConfig', 'Model');
$SiteConfig = new SiteConfig();
$data = $SiteConfig->findExpanded('all', array('recursive' => -1));
$data = array_merge($data, array(
    'use_universal_analytics' => '0'
));
if($SiteConfig->saveKeyValue($data)) {
    $this->setUpdateLog('site_configs テーブルの更新に成功しました。');
} else {
    $this->setUpdateLog('site_configs テーブルの更新に失敗しました。', true);
}