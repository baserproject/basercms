<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * composer のインストーラー
 *
 * - composer をインストール
 * - composer self-update を実行
 * - composer install を実行
 *
 * ブラウザでアクセスした際、/vendor/autoload.php が存在しない場合に /webroot/index.php より呼び出される。
 * ロゴと jQuery 以外は、このファイルだけで完結させる。
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(dirname(__DIR__)) . DS);
$url = $_SERVER['REQUEST_URI'];
$phpPath = whichPhp();
if (preg_match('/\/logo\.png$/', $url)) {
    header('Content-Type: image/png; charset=utf-8');
    echo file_get_contents(ROOT_DIR . 'plugins' . DS . 'bc-admin-third' . DS . 'webroot' . DS . 'img' . DS . 'admin' . DS . 'logo_large.png');
    return;
} elseif (preg_match('/\/install_composer$/', $url) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        command($_POST['php_path']);
        echo json_encode([
            'result' => true,
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'result' => false,
            'error' => $e->getMessage()
        ]);
    }
    return;
} elseif (!preg_match('/\/$/', $url)) {
    return;
}

function whichPhp()
{
    exec('which php', $out, $code);
    if ($code === 0) return $out[0];
    return '';
}

function command($phpPath)
{
    if (!$phpPath) $phpPath = 'php';
    if (!is_writable(ROOT_DIR . 'composer')) {
        throw new Exception('/composer に書き込み権限がありません。書き込み権限を与えてください。');
    }
    if (!is_writable(ROOT_DIR . 'vendor')) {
        throw new Exception('/vendor に書き込み権限がありません。書き込み権限を与えてください。');
    }
    if (!is_writable(ROOT_DIR . 'config')) {
        throw new Exception('/config に書き込み権限がありません。書き込み権限を与えてください。');
    }
    if (!is_writable(ROOT_DIR . 'tmp')) {
        throw new Exception('/tmp に書き込み権限がありません。書き込み権限を与えてください。');
    }
    if (!is_writable(ROOT_DIR . 'logs')) {
        throw new Exception('/logs に書き込み権限がありません。書き込み権限を与えてください。');
    }
    $composerDir = ROOT_DIR . 'composer' . DS;
    $command = "cd {$composerDir}; export HOME={$composerDir}; curl -sS https://getcomposer.org/installer | {$phpPath}";
    exec($command, $out, $code);
    if ($code !== 0) throw new Exception('composer のインストールに失敗しました。');
    $command = "cd " . ROOT_DIR . "; export HOME={$composerDir} ; {$phpPath} {$composerDir}composer.phar self-update";
    exec($command, $out, $code);
    if ($code !== 0) throw new Exception('composer のアップデートに失敗しました。');
    $command = "cd " . ROOT_DIR . "; export HOME={$composerDir} ; {$phpPath} {$composerDir}composer.phar install";
    exec($command, $out, $code);
    if ($code !== 0) throw new Exception('ライブラリのインストールに失敗しました。<br>コマンド実行をお試しください<br>' . $command);
    if (!copy(ROOT_DIR . 'config' . DS . '.env.example', ROOT_DIR . 'config' . DS . '.env')) {
        throw new Exception('.env のコピーに失敗しました。<br>/config/.env.example を /config/.env としてリネームしてください。');
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow"/>
    <title>baserCMSのインストール</title>
    <style>
        :root {
            font-size: 10px;
            font-family: Arial, Geneva, sans-serif, "メイリオ", Verdana, "Hiragino Kaku Gothic Pro", "ヒラギノ角ゴ Pro W3", "ＭＳ Ｐゴシック";
            color: #424f44;
        }

        body {
            min-width: 960px;
            height: 100%;
            line-height: 1.15;
            margin: 0;
        }

        .bca-app {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100vh;
        }

        #AdminBaserCoreUsersLogin .bca-container {
            height: auto !important;
            background: #F4F5F1;
        }

        .bca-main {
            font-size: 1.4rem;
            position: relative;
            flex-basis: 100%;
            overflow: auto;
            background: #f8f8f8;
            line-height: 1.4;
        }

        #AdminBaserCoreUsersLogin .bca-main__contents {
            border: none;
            background: none;
        }

        .bca-main__contents {
            padding: 20px;
            margin: 20px;
            background-color: #fff;
            border: 1px solid #eeeeea;
        }

        .bca-login {
            position: relative;
            z-index: 100;
            max-width: 500px;
            margin: 30px auto;
            padding: 40px 25px 25px;
            border-radius: 5px;
            background: #fff;
            font-size: 1.4rem;
        }

        .bca-login, .bca-login *, .bca-login *::before, .bca-login *::after {
            box-sizing: border-box;
        }

        .bca-login__title {
            margin-top: 0;
            margin-bottom: 40px;
            text-align: center;
            color: #6fa83d;
            font-weight: bold;
            font-size: 4rem;
        }

        .bca-login__logo {
            width: 230px;
        }

        .bca-login button.bca-btn--login {
            font-size: 1.6rem;
            line-height: 1;
            display: block;
            width: 100%;
            height: 52px;
        }

        .bca-btn[data-bca-btn-type=login] {
            color: #fff;
            border: 1px solid #639536;
            background-image: linear-gradient(#6fa83d 10%, #639536 100%);
        }

        .bca-btn {
            display: inline-block;
            border-radius: 3px;
            padding: 0.7em 1em 0.6em;
            line-height: 1.2;
            border: 1px solid #ccc;
            cursor: pointer;
            font-size: 1.4rem;
            box-sizing: border-box;
            outline: none;
            color: #424f44;
            text-decoration: none;
            background-image: linear-gradient(rgb(255, 255, 255) 10%, rgb(244, 245, 241) 100%);
            transition: all 0.3s ease-out 0s;
            white-space: nowrap;
        }

        .bca-textbox__input {
            /*display: block;*/
            width: 100%;
            height: 52px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin: 0 0 20px;
            padding: 0.69em 1em;
            color: #424f44;
            font-size: 1.6rem;
            font-weight: bold;
            line-height: 1;
            /*-webkit-appearance: none;*/
            /*-moz-appearance: none;*/
            /*appearance: none;*/
        }

        .bca-textbox__input:focus {
            border-color: #6fa83d;
            border-width: 1px;
            outline: none;
        }

        .bca-footer {
            width: 100%;
            color: #ccc;
            background-color: #2a332c;
        }

        .bca-footer__inner--full {
            display: flex;
            justify-content: space-between;
            flex-direction: row-reverse;
            align-items: center;
            padding: 7px 20px 7px 12px;
        }

        .bca-footer__inner--full .bca-footer__main {
            display: flex;
            align-items: center;
        }

        .bca-footer__inner--full .bca-footer__main .bca-footer__banner {
            margin: 0;
        }

        .bca-footer__banner {
            display: flex;
            justify-content: center;
            margin: 0 0 20px;
            padding: 0;
            list-style: none;
        }

        .bca-footer__banner__item {
            margin: 0;
            padding: 0 5px;
        }

        .bca-footer__copyright {
            margin: 0;
            text-align: center;
        }

        #MessageBox {
            display: none;
        }

        #flashMessage {
            padding: 10px 10px 10px 10px;
            margin: 20px 0;
            font-size: 16px;
            border-radius: 10px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            font-weight: normal;
        }

        .message {
            color: #0087bc;
            font-weight: normal;
            margin: 20px;
            line-height: 1.6;
        }

        #MessageBox .alert-message {
            color: #f20014 !important;
        }

        #InstallStatus {
            margin-top: 20px;
            line-height: 1.6;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script>
        $(function () {
            let messageBox = $("#MessageBox");
            let message = $("#flashMessage");
            let status = $("#InstallStatus");
            const phpPathExists = <?php echo ($phpPath)? 'true' : 'false' ?>;
            if (!phpPathExists) {
                message.addClass('alert-message').html('PHPのパスが取得できません。<br>確認の上、手動で入力してください。');
                messageBox.fadeIn(500);
            }
            $('#BtnInstall').click(function () {
                $('#BtnInstall').attr('disabled', 'disabled');
                message.removeClass();
                messageBox.hide();
                status.show().append('Library install start.');
                let id = setInterval(function () {
                    status.append('.');
                }, 1000);
                $.ajax({
                    type: "POST",
                    url: "./install_composer",
                    data: $("#AdminInstallerForm").serialize(),
                    dataType: "json",
                    success: function (result) {
                        if (result.result) {
                            message.addClass('message').html('ライブラリのインストールが完了しました。');
                            messageBox.fadeIn(500);
                            let id = setInterval(function () {
                                location.href = './';
                            }, 1000);
                        } else {
                            message.addClass('alert-message').html(result.error);
                            messageBox.fadeIn(500);
                            clearInterval(id);
                            $('#BtnInstall').removeAttr('disabled');
                        }
                    },
                    complete: function () {
                        status.html('').hide();
                    }
                });
                return false;
            });
        });
    </script>
</head>

<body id="AdminInstaller" class="normal">

<div id="Page" class="bca-app">
    <header id="Header" class="bca-header"></header>
    <div id="Wrap" class="bca-container">
        <main class="bca-main">
            <article id="ContentsBody" class="contents-body bca-main__body">
                <div class="bca-main__contents clearfix">
                    <div id="Login" class="bca-login">
                        <div id="LoginInner">
                            <h1 class="bca-login__title">
                                <img src="./logo.png" alt="ライブラリのインストール" class="bca-login__logo"/>
                            </h1>
                            <form id="AdminInstallerForm" method="POST">
                                <p>baserCMSのインストールを開始する前にライブラリのインストールが必要です。
                                    /tmp と /logs と /config と /composer と /vendor フォルダに書き込み権限が必要となります。</p>
                                <small>PHPのパス</small>
                                <input type="text" name="php_path" value="<?php echo $phpPath ?>" class="bca-textbox__input"/>

                                <div class=" submit bca-login-form-btn-group">
                                    <button type="button" class="bca-btn--login bca-btn" data-bca-btn-type="login" id="BtnInstall" tabindex="4">
                                        ライブラリをインストールする
                                    </button>
                                </div>
                                <div id="MessageBox" class="message-box">
                                    <div id="flashMessage" class=""></div>
                                </div>
                                <div id="InstallStatus"></div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- / bca-main__body -->
            </article>
            <!-- / .bca-main --></main>
        <!-- / #Wrap --></div>

    <div id="Footer" class="bca-footer" data-loggedin="">
        <div class="bca-footer__inner--full">
            <div class="bca-footer__main">
                <ul class="bca-footer__banner">
                    <li class="bca-footer__banner__item"></li>
                    <li class="bca-footer__banner__item"></li>
                </ul>
            </div>
            <div class="bca-footer__sub">
                <div class="bca-footer__copyright">Copyright &copy; baserCMS Users Community All rights reserved.</div>
            </div>
        </div>
    </div>
    <!-- / #Page --></div>

</body>
</html>
