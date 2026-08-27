<?php
declare(strict_types=1);

namespace BcMcp\Command;

use Cake\Console\Arguments;
use Cake\Console\BaseCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

/**
 * OAuth2 Cleanup Command
 */
class Oauth2CleanupCommand extends BaseCommand
{

    /**
     * Hook method for defining this command's option parser.
     *
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('期限切れのOAuth2トークンと認可コードをクリーンアップします');

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param Arguments $args The command arguments.
     * @param ConsoleIo $io The console io
     * @return int|null The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->out('OAuth2 認可コードとリフレッシュトークンのクリーンアップを開始します...');

        try {
            // 認可コードのクリーンアップ
            $authCodesTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AuthCodes');
            $expiredAuthCodes = $authCodesTable->cleanExpiredCodes();
            $io->success("期限切れの認可コード {$expiredAuthCodes} 件を削除しました");

            // リフレッシュトークンのクリーンアップ
            $refreshTokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2RefreshTokens');
            $expiredTokens = $refreshTokensTable->cleanExpiredTokens();
            $io->success("期限切れのリフレッシュトークン {$expiredTokens} 件を削除しました");

            // 未使用クライアントのクリーンアップ
            $removedClients = $this->cleanUnusedClients();
            $io->success("一度も認可に使われていないクライアント {$removedClients} 件を削除しました");

            // 統計情報を表示
            $remainingAuthCodes = $authCodesTable->find()->count();
            $remainingRefreshTokens = $refreshTokensTable->find()->count();

            $io->out('');
            $io->out('現在の状況:');
            $io->out("有効な認可コード: {$remainingAuthCodes} 件");
            $io->out("有効なリフレッシュトークン: {$remainingRefreshTokens} 件");

        } catch (\Exception $e) {
            $io->error('クリーンアップ中にエラーが発生しました: ' . $e->getMessage());
            return self::CODE_ERROR;
        }

        $io->success('クリーンアップが完了しました');
        return self::CODE_SUCCESS;
    }

    /**
     * 一度も認可に使われていない古いクライアントを削除する
     *
     * 動的クライアント登録は無認証で開いているため、使われないクライアントが
     * 溜まり続ける。アクセストークンの発行実績が無く、登録から一定期間が
     * 経過したものを削除する。
     *
     * @return int 削除した件数
     */
    private function cleanUnusedClients(): int
    {
        $retentionDays = (int)Configure::read('BcMcp.registration.unusedClientRetentionDays', 30);
        $clientsTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        $accessTokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');

        $usedClientIds = $accessTokensTable->find()
            ->select(['client_id'])
            ->distinct(['client_id'])
            ->all()
            ->extract('client_id')
            ->toArray();

        $conditions = ['created <' => new DateTime('-' . $retentionDays . ' days')];
        if ($usedClientIds) {
            // 空配列を NOT IN に渡すと SQL が壊れるため、実績がある場合のみ付ける
            $conditions['client_id NOT IN'] = $usedClientIds;
        }

        return $clientsTable->deleteAll($conditions);
    }
}
