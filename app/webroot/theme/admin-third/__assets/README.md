# admin-third テーマ

## 開発に参加する

### 前提条件

- Gulp で監視する前提で、CSSのコンパイルに Sass を利用します。
- Gulp を利用する際、Node.js のバージョンが、v8.15.1 である必要があります。nodebrew などで切り替えれるようにしておきましょう。

### Gulp を起動する

```shell script
cd app/webroot/theme/admin-third/__assets
npm run dev
```

### CSS を編集する

Sass ファイルは、次のフォルダ配下に存在します。
```shell script
cd app/webroot/theme/admin-third/__assets/css/
```
CSS ファイルの出力先は次のフォルダとなります。
```shell script
cd app/webroot/theme/admin-third/css/
```

### プロキシの設定

Gulp の browser browser-sync では、`http://localhost:3000` の URLにて、sass ファイルを更新する度に、ブラウザの自動リロードを行いますが、デフォルトでは、 `localhost` にマッピングされています。
 
マッピング先を変更するには、 `proxy.json.sample` を `proxy.json` としてコピーし、ファイル内の、`proxy` の値を変更します。
  
標準搭載の、 Vagrant にマッピングする場合は、 `192.168.33.10` に変更します。
