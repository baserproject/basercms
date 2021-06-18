# 全体的な変更点

## BcUtil の変更
`BcUtil` 等を配置していた `lib` を `Utility` に変更しています。

　
## basic.php の変更 
basics.php 関数について `BcUtil` に静的メソッドとして統合していきます。   
`getVersion()` → `BcUtil::getVersion()`

　
## BcReplacePrefixComponent の廃止
UsersController を 管理画面や、マイページなどで使い回すための仕組みでしたが、複雑さを増すため、一旦、廃止としました。

## モデルの変更点
BcCacheBehaviorは廃止

## Helperの変更点
パンくずリストの廃止
