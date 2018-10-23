var frame3 = {}

// url拆分
frame3.URL = location.href.split('?')[0].split('/');
frame3.APP_URL = (frame3.URL.length >= 3) ? '/' + frame3.URL[3] : 'f';
frame3.CONTROLLER_URL = (frame3.URL.length >= 4) ? frame3.APP_URL + '/' + frame3.URL[4] : 'index';
frame3.FUNCTION_URL = (frame3.URL.length >= 5) ? frame3.CONTROLLER_URL + '/' + frame3.URL[5] : 'index';

// ajax
frame3.ajax = function(url, param) {
    var r;
    $.ajax({
        type: "POST",
        url: url,
        cache: false,
        async: false,
        dataType: "json",
        crossDomain: true, // 如果用到跨域，需要后台开启CORS
        processData: false, // 注意：不要 process data
        contentType: false, // 注意：不设置 contentType
        data: form_data
    }).done(function(resp) {
        r = (resp.code == 0) ? resp.data : false;
    });
    return r;
}

// 传图
frame3.UPLOAD_URL = frame3.APP_URL + '/upload';

// frame3.img_upload = function(callback,key) {

// }

/**
 * @name ajax上传图片
 * @param  {[type]}   input    [description]
 * @param  {Function} callback [description]
 * @return {[type]}            [description]
 */
frame3.img_upload = function(callback,key) {
    // 上传用input是否存在，不存在需要初始化一个隐藏的input
    if ($('#frame3-input-upload').length <= 0) {
        $('body').append('<input type="file" style="display:none;" id="frame3-input-upload">');
    }
    $('#frame3-input-upload').off().on('change',function (e) {
        //  绑定了一次，so 以后change要重新绑定新的回调function
        var form_data = new FormData();
        // 把上传的数据放入form_data
        var file = $('#frame3-input-upload')[0].files[0];
        key = key?key:'img';
        form_data.append(key,file);
        $.ajax({
            type: "POST", // 上传文件要用POST
            url: frame3.UPLOAD_URL,
            cache: false,
            async: false,
            dataType: "json", // 预期服务器返回的数据类型
            // crossDomain: true, // 如果用到跨域，需要后台开启CORS
            processData: false, // 不转换为查询字符串
            contentType: false, // 发送信息至服务器时内容编码类型
            data: form_data
        }).done(function(resp) {
            if(resp.code == 0) {
                return callback(resp.data);
            }else{
                return false;
            }
        }).always(function (e) {
            $('#frame3-input-upload').val('');
            return;
        });
        return;
    });
    $('#frame3-input-upload').click();
}

// modal
frame3.dialog = function(text_cfg, fun_ok_callback, fun_cancel_callback) {
    frame3.reset_dialog();
    $('#frame3-dialog').modal('show');
    $('#frame3-dialog .modal-footer button').unbind('click').on('click', function(e, o) {
        if ($(e.target).hasClass('btn-ok') && fun_ok_callback && {}.toString.call(fun_ok_callback) === '[object Function]') {
            fun_ok_callback();
            $('#frame3-dialog').modal('hide');
        }
        if ($(e.target).hasClass('btn-cancel') && fun_cancel_callback && {}.toString.call(fun_cancel_callback) === '[object Function]') {
            fun_cancel_callback();
        }
    });
    if (text_cfg && text_cfg.title) {
        $('#frame3-dialog-label').html(text_cfg.title);
    }
    if (text_cfg && text_cfg.body) {
        $('#frame3-dialog .modal-body').html(text_cfg.body);
    }
    if (text_cfg && text_cfg.btn_cancel_text) {
        $('#frame3-dialog .modal-footer button.btn-cancel').html(text_cfg.btn_cancel_text);
    }
    if (text_cfg && text_cfg.btn_ok_text) {
        $('#frame3-dialog .modal-footer button.btn-ok').html(text_cfg.btn_ok_text);
    }
}
frame3.reset_dialog = function() {
    $('#frame3-dialog .modal-body').html('');
    $('#frame3-dialog .modal-footer button.btn-cancel').html('取消');
    $('#frame3-dialog .modal-footer button.btn-ok').html('确定');
    $('#frame3-dialog-label').html('title');
}