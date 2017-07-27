shop.updateScriptData = function(){
    jQuery('#uploadify').uploadifive('destroy');
    shop.multiupload(); 
};

shop.multiupload = function(){
    var data = {
        'act': 'gallery',
        'code': 'multi-upload',
        'cat': jQuery('#gallery-category').val(),
        'lang': jQuery('#lang').val(),
        'uid': IS_LOGIN
    };
    data[BASE_TOKEN_NAME] = shop.getCSRFToken();

    jQuery('#uploadify').uploadifive({
        'uploadScript' : BASE_URL+'ajax.php',
        'formData' : data,
        'buttonText' : 'CHỌN ẢNH',
        'fileType'     : 'image/*',
        'onError': function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        },
        'onUploadComplete' : function(file, data, response) {
            var myObject;
            try {
              myObject = eval('(' + data + ')');
            } catch (e) {
              alert('Lỗi hệ thống upload '+ data);
              return;
            }
            if (myObject.err == 0) {
                //add anh vao danh sach
                var img = shop.gallery.theme.image(myObject.data);
                jQuery('#gallery ul').prepend(img);
                
                //===== Image gallery control buttons =====//
                jQuery(".gallery ul li div").hover(
                    function() { jQuery(this).children(".actions").show("fade", 200); },
                    function() { jQuery(this).children(".actions").hide("fade", 200); }
                );
                
                //===== SORT ABLE =====//
				jQuery(".gallery ul").sortable().bind('sortupdate', function(e, item) {
					shop.gallery.image.changePos(item);
				});
            } else {
                alert(file.name+"\nError !!! "+myObject.msg);
            }
        }
    });
};