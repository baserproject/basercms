#!/bin/bash
###################################################
# Vaddy を実行する為のスクリプト
#
# TODO 現在は、プッシュが連続して発生した場合、
# テスト対象の整合性が崩れる、２回目のテストが実行されない
# という問題がある。
# Vaddy側にステータス確認のAPIの準備ができたら、
# それを利用して二重実行を防ぐ処理に変更する
###################################################

# Vaddyクライアント取得
git clone https://github.com/vaddy/go-vaddy.git;

# デプロイ
openssl aes-256-cbc -k "$SERVER_KEY" -in deploy_key.enc -d -a -out deploy.key
cp deploy.key ~/.ssh/
chmod 600 ~/.ssh/deploy.key
ssh -i ~/.ssh/deploy.key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null $TEST_SERVER 'bash -s' < deploy.sh

# Vaddy実行
while :
do
    go-vaddy/bin/vaddy-linux-64bit $VADDY_AUTH_KEY $VADDY_USER $VADDY_FQDN | tee result.txt
    msg=`cat result.txt`
    if [[ $msg =~ "Scan has already been running" ]]; then
          echo "既に他のテスト実行中のため５分後にもう一度テストリクエストします。"
          sleep 300
    fi
    if [[ ! $msg =~ "Scan has already been running" ]]; then
        break
    fi
done


#MEMO:
# timeoutコマンドにより、Scanキャンセルが正常にできないため、２回目(pull後)のテスト実行ができないが、
# テスト中にpullを実行していて影響がでていると考えられるため保留
#go-vaddy/bin/vaddy-linux-64bit $VADDY_AUTH_KEY $VADDY_USER $VADDY_FQDN | tee result.txt
#msg=`cat result.txt`

#MEMO: slack通知スクリプト
#echo "$msg" | ./slack_webhooks.sh