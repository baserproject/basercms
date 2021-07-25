<?php
$target =
// 例
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

if (is_array($target)) {
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


