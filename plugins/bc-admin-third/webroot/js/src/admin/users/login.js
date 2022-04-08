/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

$(function () {

    let alertMessage = $("#AlertMessage")

    $("#BtnLogin").click(function () {
        $.bcUtil.showLoader()
        alertMessage.fadeOut()
        $.bcJwt.login(
            $('#email').val(),
            $('#password').val(),
            $('#saved').prop('checked'),
            function(response){
                let query = decodeURIComponent(location.search).replace('?', '').split('&');
                let redirect
                query.forEach(function(v){
                    let [key, value] = v.split('=')
                    if(key === 'redirect') {
                        redirect = value
                    }
                });
                if(redirect) {
                    location.href = redirect
                } else {
                    location.href = response.redirect
                }
            }, function(){
                alertMessage.fadeIn()
                $.bcUtil.hideLoader()
            }
        )
        return false;
    });
});
