# インストール時の参考情報

レンタルサーバー毎の設定情報は、 [baserCMS公式ガイド - レンタルサーバー毎の設定](http://wiki.basercms.net/%E3%83%AC%E3%83%B3%E3%82%BF%E3%83%AB%E3%82%B5%E3%83%BC%E3%83%90%E3%83%BC%E6%AF%8E%E3%81%AE%E8%A8%AD%E5%AE%9A) に掲載されています。
 
さくらのレンタルサーバー等、ブラウザでインストーラーを表示できない場合、次の作業を行う事で表示できる場合があります。  

/.htaccess を開き、下記の行の # を削除
```shell script
#RewriteBase /
```

/app/webroot/.htaccess を開き、下記の行の # を削除
```shell script
#RewriteBase /
```

