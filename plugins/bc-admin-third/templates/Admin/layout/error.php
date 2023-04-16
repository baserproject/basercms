<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex,nofollow"/>
  <title><?php echo h($this->fetch('title')) ?></title>
  <?php echo $this->fetch('meta') ?>
  <?php $this->BcBaser->css([
    'vendor/bootstrap-4.1.3/bootstrap',
    'vendor/jquery-ui/jquery-ui.min',
    'vendor/jquery.timepicker',
    'admin/style',
    '../js/admin/vendors/jquery.jstree-3.3.8/themes/proton/style.min',
  ]) ?>
  <?php echo $this->fetch('css') ?>
  <?php $this->BcBaser->js([
    'admin/vendor.bundle',
    'vendor/vue.min',
    'vendor/jquery-3.5.1.min',
    'vendor/jquery.bt.min',
    'vendor/jquery-ui-1.11.4.min.js',
    'vendor/i18n/ui.datepicker-ja',
    'vendor/jquery.timepicker',
    'admin/functions',
  ]) ?>
  <?php echo $this->fetch('script') ?>
</head>

<body>

  <div id="Page" class="bca-app">

    <header id="Header" class="bca-header">
      <div id="ToolBar" class="bca-toolbar">
        <div id="ToolbarInner" class="clearfix bca-toolbar__body">

          <div class="bca-toolbar__logo">
            <div class="bca-toolbar__logo-link">
              <?php
                echo $this->BcBaser->getImg('admin/logo_icon.svg',
                  ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol']);
              ?>
            </div>
          </div>

        </div>
      </div>
    </header>

    <div id="Wrap" class="bca-container">
      <main id="Contents" class="bca-main">
        <article id="ContentsBody" class="contents-body bca-main__body">
          <div class="bca-main__header">
            <h1 class="bca-main__header-title"><?php $this->BcAdmin->title() ?></h1>
          </div>
          <div class="bca-main__contents clearfix">
            <?php echo $this->fetch('content') ?>
          </div>
        </article>
      </main>
    </div>

    <?php $this->BcBaser->element('footer') ?>
  </div>

</body>

</html>
