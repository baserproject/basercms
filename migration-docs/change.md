# 全体的な変更点

- `BcUtil` 等を配置していた `lib` を `Utility` に変更
- basics.php 関数について `BcUtil` に静的メソッドとして統合する。 `getVersion()` → `BcUtil::getVersion()`
