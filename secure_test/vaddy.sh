#!/bin/bash
###################################################
# Vaddy を実行する為のスクリプト
#
# TODO 現在は、プッシュが連続して発生した場合、
# テスト対象の整合性が崩れる、２回目のテストが実行されない
# という問題がある。
# Vaddy側にステータス確認のAPIの準備ができたら、
# それを利用して二重実行を防ぐ処理に変更する
# 上記問題はCLIからAPIにテスト実行を変更したことで応急的に解決
###################################################

# 前回のVaddyが終わっているか、Vaddyが実行できるかを確認
while :
do
    curl -Ss https://api.vaddy.net/v1/scan -X POST -d "action=start" -d "user=$VADDY_USER" -d "auth_key=$VADDY_AUTH_KEY" -d "fqdn=$VADDY_FQDN" | tee result.txt
    msg=`cat result.txt`
    if [[ $msg =~ "Scan has already been running" ]]; then
          echo "既に他のテスト実行中のため5分後にもう一度テストリクエストします。"
          sleep 300
    fi
    if [[ $msg =~ scan_id\":\"(.*)\" ]]; then
        SCAN_ID=${BASH_REMATCH[1]}
        curl -Ss https://api.vaddy.net/v1/scan -X POST -d "action=cancel" -d "user=$VADDY_USER" -d "auth_key=$VADDY_AUTH_KEY" -d "scan_id=$SCAN_ID" -d "fqdn=$VADDY_FQDN"
        break
    fi
done

# デプロイ
openssl aes-256-cbc -k "$SERVER_KEY" -in deploy_key.enc -d -a -out deploy.key
cp deploy.key ~/.ssh/
chmod 600 ~/.ssh/deploy.key
ssh -i ~/.ssh/deploy.key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null $TEST_SERVER 'bash -s' < deploy.sh

#Vaddy実行
curl -Ss https://api.vaddy.net/v1/scan -X POST -d "action=start" -d "user=$VADDY_USER" -d "auth_key=$VADDY_AUTH_KEY" -d "fqdn=$VADDY_FQDN" | tee result.txt
msg=`cat result.txt`
echo "$msg"
if [[ $msg =~ scan_id\":\"(.*)\" ]]; then
    SCAN_ID=${BASH_REMATCH[1]}
fi;

#結果取得
while :
do
    echo "$SCAN_ID"
    curl -G -d "user=$VADDY_USER&auth_key=$VADDY_AUTH_KEY&fqdn=$VADDY_FQDN&scan_id=$SCAN_ID" https://api.vaddy.net/v1/scan/result | tee result.txt
    msg=`cat result.txt`
    if [[ $msg =~ status\":\"scanning ]]; then
        echo "テストが完了していないため、5分後にもう一度確認します。"
        sleep 300
    fi
    if [[ ! $msg =~ status\":\"scanning ]]; then
        break;
    fi
done

#MEMO: slack通知スクリプト
#echo "$msg" | ./slack_webhooks.sh
