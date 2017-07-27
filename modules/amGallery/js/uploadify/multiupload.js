shop.multiupload = function(container, size){
    var data = {
        'act': 'gallery', 
        'code': 'multi-upload',
        'uid': IS_LOGIN,
        'size': size,
        'action': 'insert'
    };
    data[BASE_TOKEN_NAME] = shop.getCSRFToken();
    
    jQuery('#uploadify').uploadify({
        'swf'      : BASE_URL+'javascript/uploadify/uploadify.swf',
        'uploader' : BASE_URL+'ajax.php',
        'formData' : data,
        'buttonText' : 'CHỌN ẢNH',
        'fileTypeDesc' : 'Image Files',
        'fileTypeExts' : '*.gif; *.jpg; *.png',
        'onUploadError': function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        },
        'onUploadSuccess' : function(file, data, response) {
            var myObject;
            try {
              myObject = eval('(' + data + ')');
            } catch (e) {
              alert('Lỗi hệ thống upload '+ data);
              return;
            }
            if (myObject.err == 0) {
                var imgSrc = myObject.msg;
                var text = "<p style='text-align: center;'><img src='"+imgSrc+"' alt=''/></p>";
                CKEDITOR.instances[container].insertHtml(text);
            } else {
                alert(file.name+"\nError !!! "+myObject.msg);
            }
        }
    });
};