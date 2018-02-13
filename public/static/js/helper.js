var url = location.href.split('?')[0].split('/');
var APP_URL = (url.length >= 3) ? '/' + url[3] : 'demo';
var CONTROLLER_URL = (url.length >= 4) ? APP_URL + '/' + url[4] : 'index';
var FUNCTION_URL = (url.length >= 5) ? CONTROLLER_URL + '/' + url[5] : 'index';
var UPLOAD_INTERFACE = APP_URL + '/upload';

function ajax_upload(o, key) {
    var form_data = new FormData();
    var file_data = $(o).prop("files")[0];
    // 把上传的数据放入form_data
    form_data.append(key, file_data);
    var r;
    $.ajax({
        type: "POST", // 上传文件要用POST
        url: UPLOAD_INTERFACE,
        cache: false,
        async: false,
        dataType: "json",
        crossDomain: true, // 如果用到跨域，需要后台开启CORS
        processData: false, // 注意：不要 process data
        contentType: false, // 注意：不设置 contentType
        data: form_data
    }).success(function(resp) {
        r = (resp.code == 0) ? resp.data : false;
    }).fail(function() {
        r = false;
    });
    $(o).val('');
    return r;
}