
$(function () {
     if ( $.browser.msie && $.browser.version <= 7 ) {
             $('body').prepend('<div id="ie_error">あなたは <b>旧式ブラウザ(InternetExplorer7)をご利用中</b> です。このウェブサイトを快適に閲覧するにはブラウザを <a href="http://www.microsoft.com/japan/windows/products/winfamily/ie/function/default.mspx" target="_blank">アップグレード</a> してください。</div>');
     }
});
