# ビヘイビアにおける注意点

## treeBehaviorに関して

```php
$list = $categories->generateTreeList(); // generateTreeList() はもう存在しません。
↓
$list = $categories->find('treeList', ['spacer' => ...]);
```

なお、2.xから3.xへのDB構造の重要な変更点は、lft/rghtフィールドが符号なしにできなくなったことです。

lft（整数、符号付き） ツリー構造を維持するために使用されます。
rght（整数、符号付き） ツリー構造を維持するために使用されます。
パフォーマンスを向上させるために、これらのフィールドは負の値にもできるようになりました。
