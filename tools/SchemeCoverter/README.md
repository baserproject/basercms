## ツール

### convert.phpについて
basercms4系のSchema
$cakephp2系のSchemeをmigration fileのaddColumnに変換する

#### convert.php使い方
Schemeファイルから作りたいカラムの配列をコピペする
```
// 例) Permissionの例

// 1} permissions.phpから下記をコピペ
    $permissions = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
        'no' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
        'sort' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
        'name' => ['type' => 'string', 'null' => true, 'default' => null],
        'user_group_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
        'url' => ['type' => 'string', 'null' => true, 'default' => null],
        'auth' => ['type' => 'boolean', 'null' => true, 'default' => null],
        'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci']
    ];

// 2) $targetに$permissionsを代入し、php convert.phpを実行

// 3) 出力されたaddColumnをCreatePermissions.phpにコピペする
->addColumn('id', 'integer', ['null' => false, 'default' => null, 'limit' => 8])
->addPrimaryKey(['id'])
->addColumn('no', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
->addColumn('sort', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
->addColumn('name', 'string', ['null' => true, 'default' => null, 'limit' => null])
->addColumn('user_group_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
->addColumn('url', 'string', ['null' => true, 'default' => null, 'limit' => null])
->addColumn('auth', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
->addColumn('status', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
```

