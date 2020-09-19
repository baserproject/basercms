# 外部のCakePHPアプリケーションで baserCMSを利用する

## composer でインストール

```
composer require baser-core
```

## Application クラスの修正

`src/Application` の継承先クラスを `BaserCore\BcApplication` に変更する
