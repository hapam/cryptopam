//auto load when document ready
shop.ready.add(function(){
	$.AdminBSB.browser.activate();
    $.AdminBSB.leftSideBar.activate();
    $.AdminBSB.rightSideBar.activate();
    $.AdminBSB.navbar.activate();
    $.AdminBSB.dropdownMenu.activate();
    $.AdminBSB.input.activate();
    $.AdminBSB.select.activate();
    $.AdminBSB.search.activate();
	
	if(shop.is_func(jQuery(".form-date-picker").bootstrapMaterialDatePicker)){
		jQuery(".form-date-picker").bootstrapMaterialDatePicker(shop.configDatePick());
	}
	
	$('[data-toggle="tooltip"]').tooltip({container: 'body'});

	shop.skinChanger();
	shop.textarea();
	shop.inputfile();

    setTimeout(function () { $('.page-loader-wrapper').fadeOut(); }, 50);
},true);