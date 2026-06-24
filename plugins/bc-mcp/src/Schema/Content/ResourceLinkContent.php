<?php
declare(strict_types=1);

namespace BcMcp\Schema\Content;

use JsonSerializable;
use PhpMcp\Schema\Content\Content;

/**
 * ResourceLinkContent class for MCP resource_link type
 *
 * MCPのresource_linkタイプをサポートするためのカスタムクラス
 */
class ResourceLinkContent extends Content implements JsonSerializable
{
    public function __construct(
        public readonly string $uri,
        public readonly string $name,
        public readonly string $title,
        public readonly ?string $description = null
    ) {
        parent::__construct('resource_link');
    }

    /**
     * ファクトリーメソッド - resource_linkを作成
     */
    public static function make(
        string $uri,
        string $name,
        string $title,
        ?string $description = null
    ): self {
        return new self(
            uri: $uri,
            name: $name,
            title: $title,
            description: $description
        );
    }

    /**
     * 配列として出力
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->type,
            'uri' => $this->uri,
            'name' => $this->name,
            'title' => $this->title,
        ];

        if ($this->description !== null) {
            $result['description'] = $this->description;
        }

        return $result;
    }

    /**
     * JSON形式で出力
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
