#!/bin/bash

go-vaddy/bin/vaddy-linux-64bit $VADDY_AUTH_KEY $VADDY_USER $VADDY_FQDN | tee result.txt

msg=`cat result.txt`
#echo "$msg" | ./slack_webhooks.sh

if [[ $msg =~ "Scan has already been running" ]]; then
    while :
    do
      echo "既に他のテスト実行中のため５分後にもう一度テストリクエストします。"
      sleep 300
      go-vaddy/bin/vaddy-linux-64bit $VADDY_AUTH_KEY $VADDY_USER $VADDY_FQDN | tee result.txt
      msg=`cat result.txt`
      if [[ ! $msg =~ "Scan has already been running" ]]; then
        break
      fi
    done
fi
