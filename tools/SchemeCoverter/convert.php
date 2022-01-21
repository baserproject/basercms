<?php
$target =
// 例
$pages = [
    'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
    'contents' => ['type' => 'text', 'null' => true, 'default' => null],
    'draft' => ['type' => 'text', 'null' => true, 'default' => null],
    'page_template' => ['type' => 'string', 'null' => true, 'default' => null],
    'code' => ['type' => 'text', 'null' => true, 'default' => null],
    'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
    'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
    'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
];

if (is_array($target)) {
    $text = "";
    foreach ($target as $key => $value) {
        if (in_array($key, ['indexes', 'tableParameters'])) {
            continue;
        }
        $default = json_encode($value['default'] ?? null);
        $null = json_encode($value['null'] ? true : false);
        $limit = $value['length'] ?? json_encode(null);
        $text .= "->addColumn('{$key}', '{$value['type']}', ['null' => {$null}, 'default' => {$default}, 'limit' => {$limit}])\n";
        if (isset($value['key']) && $value['key'] === 'primary') {
            $text .= "->addPrimaryKey(['{$key}'])\n";
        }
    }
    echo $text;
}


