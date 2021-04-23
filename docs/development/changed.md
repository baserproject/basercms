# 全体的な変更点

## BcUtil の変更
`BcUtil` 等を配置していた `lib` を `Utility` に変更しています。

　
## basic.php の変更 
basics.php 関数について `BcUtil` に静的メソッドとして統合していきます。   
`getVersion()` → `BcUtil::getVersion()`

