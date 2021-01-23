<?
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Utility\BcUtil;
use BaserCore\View\AppView;

/**
 * @var AppView $this
 * @var string $title
 */

$this->assign('title', $title);
$request = $this->getRequest();
$attributes = $request->getAttributes();
$base = $attributes['base'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow"/>
    <title><?= h($this->fetch('title')) ?></title>
    <? echo $this->fetch('meta') ?>
    <? echo $this->BcBaser->js([
        'admin/vendor.bundle',
        'vendor/vue.min',
        'vendor/jquery-3.5.1.min',
        'vendor/jquery.bt.min',
        'vendor/jquery-ui-1.11.4.min.js',
        'vendor/i18n/ui.datepicker-ja',
        'vendor/jquery.timepicker',
    ]) ?>
    <? echo $this->BcBaser->js('admin/common.bundle', true, [
        'id' => 'AdminScript',
        'data-baseUrl' => h($base),
        'data-adminPrefix' => BcUtil::getAdminPrefix()
    ]) ?>
    <? echo $this->BcBaser->js([
        'admin/startup.bundle'
    ]) ?>
    <? echo $this->fetch('script') ?>
    <? echo $this->Html->css([
        'vendor/bootstrap-4.1.3/bootstrap',
        'vendor/jquery-ui/jquery-ui.min',
        'vendor/jquery.timepicker',
        'admin/style',
    ]) ?>
    <? echo $this->fetch('css') ?>
</head>

<body id="<? $this->BcBaser->contentsName(true) ?>" class="normal">

<div id="Page" class="bca-app">

    <? echo $this->element('Admin/header') ?>

    <div id="Wrap" class="bca-container">

        <? if ($this->BcAdmin->isAvailableSideBar()): ?>
            <? $this->BcBaser->element('Admin/sidebar') ?>
        <? endif ?>

        <main id="Contents" class="bca-main">

            <article id="ContentsBody" class="contents-body bca-main__body">

                <div class="bca-main__header">

                    <h1 class="bca-main__header-title"><? $this->BcAdmin->title() ?></h1>

                    <div class="bca-main__header-actions">
                        <? $this->BcBaser->element('Admin/main_body_header_links'); ?>
                    </div>

                    <div class="bca-main__header-menu">
                        <? $this->BcAdmin->contentsMenu() ?>
                    </div>

                </div>

                <? $this->BcAdmin->help() ?>

                <? $this->BcAdmin->search() ?>

                <? $this->BcBaser->flash() ?>

                <div id="BcMessageBox">
                    <div id="BcSystemMessage" class="notice-message"></div>
                </div>

                <div class="bca-main__contents clearfix">
                    <?= $this->fetch('content') ?>
                </div>

                <!-- / bca-main__body --></article>

            <!-- / .bca-main --></main>

        <!-- / #Wrap --></div>

        <? echo $this->element('Admin/footer') ?>

    <!-- / #Page --></div>

</body>

</html>
