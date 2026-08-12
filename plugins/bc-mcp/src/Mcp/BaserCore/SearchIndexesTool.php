<?php
declare(strict_types=1);

namespace BcMcp\Mcp\BaserCore;

use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\Routing\Router;
use Cake\Utility\Text;
use BcMcp\Mcp\BaseMcpTool;
use Mcp\Types\ResourceLinkContent;

/**
 * 検索インデックスツールクラス
 */
class SearchIndexesTool extends BaseMcpTool
{
    use LogTrait;
    use BcContainerTrait;

    /**
     * SearchIndexesService
     * @var SearchIndexesService|SearchIndexesServiceInterface
     */
    private SearchIndexesService|SearchIndexesServiceInterface $searchIndexesService;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->searchIndexesService = $this->getService(SearchIndexesServiceInterface::class);
        Configure::write('App.fullBaseUrl', preg_replace('/\/$/', '', env('SITE_URL', 'https://localhost/')));
    }

    /**
     * 検索インデックス用のツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
    {
        return $server
            ->tool(
                callback: [$this, 'search'],
                outputSchema: self::OUTPUT_SCHEMA,
                name: 'search',
                description: 'クエリ文字列でサイトを検索します。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => '検索クエリ']
                    ],
                    'required' => ['query']
                ]
            )->tool(
                callback: [$this, 'fetch'],
                outputSchema: self::OUTPUT_SCHEMA,
                name: 'fetch',
                description: '識別子を指定してデータを取得します。',
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string', 'description' => '識別子（必須）']
                    ],
                    'required' => ['id']
                ]
            );
    }

    /**
     * IDを指定して検索インデックスのデータを取得
     * @param string $id
     * @return array
     */
    public function fetch(string $id): array
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $entity = $this->searchIndexesService->get((int)$id, [
                'status' => 'publish',
                'site_id' => null
            ]);

            if ($entity) {
                $result = [
                    'type' => 'resource',
                    'resource' => [
                        'url' => Router::url($entity->url, true),
                        'text' => $entity->detail,
                        'mineType' => 'text/html',
                    ]
                ];
                return $this->createSuccessResponse($result);
            } else {
                return $this->createErrorResponse('指定されたIDの検索インデックスが見つかりません');
            }
        });
    }

    /**
     * クエリ文字列で検索インデックスを検索
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        return $this->executeWithErrorHandling(function() use ($query) {
            $entities = $this->searchIndexesService->getIndex([
                'status' => 'publish',
                'keyword' => $query,
                'site_id' => null,
                'op' => 'or'
            ]);

            $results = [];
            foreach($entities as $entity) {
                $link = new ResourceLinkContent(
                    uri: Router::url($entity->url, true),
                    name: (string)$entity->id,
                    description: mb_substr($entity->detail, 0, 200, 'UTF-8'),
                );
                // title は MCP 仕様の resource_link では任意項目だが SDK の型が
                // プロパティを持たないため、追加フィールドとして付与する。
                // Content::jsonSerialize() が extraFields をマージするため出力に含まれる。
                $link->title = $entity->title;
                $results[] = $link;
            }

            return $this->createSuccessResponse($results);
        });
    }

}
