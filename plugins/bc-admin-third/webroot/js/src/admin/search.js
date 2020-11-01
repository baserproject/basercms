$(function() {
    var script = $("#AdminSearchScript");
    var adminSearchOpened = script.attr('data-adminSearchOpened');
    var adminSearchOpenedSaveUrl = script.attr('data-adminSearchOpenedSaveUrl');

    changeSearchBox(adminSearchOpened);

	$('#BtnMenuSearch').click(function(){
		if($('#Search').css('display') === 'none'){
			changeSearchBox(true);
		} else {
			changeSearchBox(false);
		}
	});

	$('#CloseSearch').click(function(){
		changeSearchBox(false);
	});

    $('#BtnSearchClear').click(function () {
        $('#Search input[type="text"]').val("");
        $('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr('checked');
        $('#Search select').val('');
        return false;
    });

    /**
     * 検索ボックスの開閉切り替え
     */
    function changeSearchBox(open) {
        var url = adminSearchOpenedSaveUrl;
        if(open){
            $('#Search').slideDown(300);
            url += '/1';
        } else {
            $('#Search').slideUp(300);
            url += '/';
        }
        $.ajax({type: "GET", url: url});
    }

});
