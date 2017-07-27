<?php
class GalleryForm extends Form {
    function __construct() {
		parent::__construct();
		$this->link_js("js/jquery/jquery.form.js");
		Gallery::addMultiUploadCore($this, 'gallery.upload.js');

		$this->link_js_me('gallery.js', __FILE__);
		$this->link_js_me('jquery.sortable.js', __FILE__);
		$this->link_css_me("gallery.css", __FILE__);
	}

    function draw() {
        global $display;
        $cat_id = Url::getParamInt('cat', 1);
		$page = Url::getParamInt('page', 1);

		$cat = DB::fetch_all("SELECT * FROM ".T_GALLERY_CATS." ORDER BY title, created DESC");
		
		$html = $this->layout->genItemHtml($this->layout->parseItem('gallery-category', array(
			'title' => 'Chọn danh mục hiển thị',
			'type'  => 'select',
			'options' => FunctionLib::getOptionHasIdTitle($cat, $cat_id),
			'ext' => array(
				'onchange' => 'shop.gallery.category.changeCat()'
			)
		)));
		
		$html.= '
		<div>
			<div class="pull-left m-t-5"><label class="card-inside-title">Upload nhiều ảnh</label></div>
			<div class="pull-left imgContainer m-l-20">
				<input id="uploadify" name="uploadify" type="file" multiple="true" />
				<div id="fileQueue" class="m-t-10"></div>
				<div id="descUploadStatus"><ul></ul></div>
				<div id="logUploadResult"><ul></ul></div>
			</div>
			<div class="pull-right coverImg"></div>
			<div class="clearfix"></div>
		</div>';
		
		$html.= '<script type="text/javascript">var g_page = '.$page.';</script>';
		
		$html = $this->layout->genPanelAuto(array(
			'title' => 'DANH MỤC ẢNH',
			'menu'  => array(
				array(
					'link' => 'javascript:shop.gallery.category.add()',
					'title'=> 'Thêm danh mục',
					'icon' => 'add'
				),
				array(
					'link' => 'javascript:shop.gallery.category.remove()',
					'title'=> 'Xóa danh mục',
					'icon' => 'delete_forever'
				),
				array(
					'link' => 'javascript:shop.gallery.category.add(true)',
					'title'=> 'Sửa danh mục',
					'icon' => 'edit'
				),
				array(
					'link' => 'javascript:shop.gallery.image.upload()',
					'title'=> 'Upload 1 Ảnh',
					'icon' => 'image'
				),
				array(
					'link' => 'javascript:shop.gallery.category.getImages()',
					'title'=> 'Danh sách ảnh',
					'icon' => 'cached'
				)
			),
			'html'  => $html
		));
		$option = array(1 => 1, 5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30, 35 => 35, 40 => 40, 45 => 45, 50 => 50);
		$def = CookieLib::get_cookie('gallery-rec' ,20);
		$html2= 'Danh sách ảnh | <span id="title-album"></span>';
		$html2.= '<div class="pull-right m-t--5"><select id="recperpage" onchange="shop.gallery.category.pager.changeRecPerPage(this.value)">'.FunctionLib::getOption($option, $def).'</select></div>';
		$html.= $this->layout->genPanelAuto(array(
			'title' => $html2,
			'color_head' => 'green',
			'html'  => '
			<div class="gallery-pager p-10"></div>
			<div class="clearfix"></div>
			<div class="gallery" id="gallery"></div>'
		));
		echo '<div class="row clearfix">'.$html.'</div>';
    }
}