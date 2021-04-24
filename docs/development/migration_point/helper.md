# ヘルパーにおける注意点

## 継承先の変更

`AppHelper` はなくなりました。  
継承先を、一旦 `AppHelper` から `Helper` に変更します。

## トレイトの利用

`BcHelperTrait` を利用します。

```php
class ClassName extends Cake\View\Helper {

    /**
     * Trait
     */
    use BcHelperTrait;
}    
```
