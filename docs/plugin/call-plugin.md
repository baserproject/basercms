# プラグインの呼び出し

## Composerに定義
`composer.json` の `autoload` にプラグインの定義を記述
```
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "BaserCore\\": "plugins/baser-core/src",
            "BcAdminThird\\": "plugins/bc-admin-third/src"
        }
    },
```

　
## dump-autoload の実行
dump-autoload を実行することで、 `/vendor/cakephp-plugins.php` に、CakePHP プラグインとしての定義が書き込まれる。
```
composer dump-autoload
```
/vendor/cakephp-plugins.php
```php
<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'WyriHaximus/TwigView' => $baseDir . '/vendor/wyrihaximus/twig-view/',
        'baser-core' => $baseDir . '/plugins/baser-core/',
        'bc-admin-third' => $baseDir . '/plugins/bc-admin-third/'
    ]
];
```

　
## BcApplication に呼び出し定義
`plugins/baser-core/src/BcApplication.php` の `bootstrap` メソッドに呼び出すプラグインを定義することで呼び出しが可能になる。
```php
$this->addPlugin('BaserCore');
$this->addPlugin('BcAdminThird');
```
※ 現在は、管理画面よりプラグインをインストールすることで呼び出しが可能となる。
