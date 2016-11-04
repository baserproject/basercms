/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright Copyright (c) baserCMS Users Community
 * @link http://basercms.net baserCMS Project
 * @since baserCMS v 4.0.0
 * @license http://basercms.net/license/index.html
 */

/**
 * TODO: startup.js で定義されている alertBox というグローバル汚染関数が使用されている
 */
$(function () {
  $('#BtnSave').click(function () {
    if ($('#SelfUpdate').html()) {
      if (confirm('更新内容をログイン情報に反映する為、一旦ログアウトします。よろしいですか？')) {
        return true
      }
    } else {
      return true
    }
    return false
  })
  $('#btnSetUserGroupDefault').click(function () {
    if (!confirm('登録されている「よく使う項目」を、このユーザーが所属するユーザーグループの初期設定として登録します。よろしいですか？')) {
      return true
    }
    var data = {}
    $('#DefaultFavorites li').each(function (i) {
      data[i] = {
        'name': $(this).find('.favorite-name').val(),
        'url': $(this).find('.favorite-url').val()
      }
    })

    $.bcToken.check(function () {
      data = $.extend(data, {
        _Token: {
          key: $.bcToken.key
        }
      })
      $.ajax({
        url: $('#UserGroupSetDefaultFavoritesUrl').html(),
        type: 'POST',
        data: data,
        dataType: 'html',
        beforeSend: function () {
          $('#Waiting').show()
          alertBox()
        },
        success: function (result) {
          $('#ToTop a').click()
          if (result) {
            alertBox('登録されている「よく使う項目」を所属するユーザーグループの初期値として設定しました。')
          } else {
            alertBox('処理に失敗しました。')
          }
        },
        /**
         * @param {XMLHttpRequest} xhr
         */
        error: function (xhr, textStatus, errorThrown) {
          var errorMessage = ''
          if (xhr.status === 404) {
            errorMessage = '<br />' + '送信先のプログラムが見つかりません。'
          } else {
            if (xhr.responseText) {
              errorMessage = '<br />' + xhr.responseText
            } else {
              errorMessage = '<br />' + errorThrown
            }
          }
          alertBox('処理に失敗しました。(' + xhr.status + ')' + errorMessage)
        },
        complete: function () {
          $('#Waiting').hide()
        }
      })
    })
  })
})
