# ルーティングにおける注意点

`BaserCore` のルーティングについては、`BaserCore\BcPlugin::routes()` にて定義します。

その他のプラグインのルーティングについては、それぞれのプラグイン内の、 `Plugin::routes()` に定義します。

```
<!-- cakephp2 -->
Router::parse($url);
<!-- cakephp4 -->
Router::getRouteCollection()->parse($url);
```
