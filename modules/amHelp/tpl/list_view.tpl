{literal}<div class="w3-code">
$this->layout->init(array(
	<div class="m-l-20">
	'style'		=>	'list',<br />
	'method'	=>	'GET'</div>
)); 
{/literal}</div>
<div class="m-t-20">
	Khởi tạo cấu hình trước khi sinh code HTML, mảng có thể chứa các thành phần sau:<br />
	<div class="m-l-20">
		<b>Bắt buộc: </b>
		<ul>
			<li><b>style</b> buộc phải set là <b>list</b></li>
			<li><b>method</b> mặc định là <b>POST</b>, tuy nhiên cần sử dụng phân trang nên buộc phải set là <b>GET</b></li>
		</ul>
		<b>Mở rộng: </b>
		<ul>
			<li><b>del</b> nhận giá trị <b>true</b> hoặc <b>false</b>, quyết định xem có chức năng xóa hay không</li>
			<li><b>path</b> đường dẫn thư mục chứa tpl mẫu, mặc định là: <b>sysLayout/themes/default</b></li>
		</ul>
	</div>
</div>
{literal}<div class="w3-code">
$this->layout->genFormAuto($this, array(
	<div class="m-l-20">
	'items' => $items,<br />
	'pagging' => array(
		<div class="m-l-20">
		'start_page' => (Pagging::$page-1)*$item_per_page,<br />
		'total_item' => Pagging::$totalResult,<br />
		'total_page' => Pagging::$totalPage,<br />
		'pager'	=> $paging</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Trực tiếp sinh ra code HTML theo mẫu, mảng bao gồm các thành phần sau:<br />
	<div class="m-l-20">
		<b>Bắt buộc: </b>
		<ul>
			<li><b>items</b> dữ liệu cần hiển thị trong bảng Kết quả</li>
		</ul>
		<b>Mở rộng: </b>
		<ul>
			<li><b>pagging</b> hiển thị phân trang, thống kê dữ liệu</li>
			<li><b>html_extra_head</b> được chèn vào đầu của tpl trước panel Tìm kiếm</li>
			<li><b>html_search</b> thay thế cho panel Tìm kiếm</li>
			<li><b>html_search_header</b> thay thế cho toàn bộ phần Tiêu đề của panel Tìm kiếm</li>
			<li><b>html_search_label</b> chỉ thay thế cho phần nội dung Tiêu đề của panel Tìm kiếm, mặc định là "TÌM KIẾM" và mô tả "Bộ lọc tìm kiếm dữ liệu"</li>
			<li><b>html_search_button</b> thay thế cho nút Tìm kiếm của panel Tìm kiếm</li>
			<li><b>html_view</b> thay thế cho phần hiển thị của panel Kết quả</li>
			<li><b>html_view_header</b> thay thế cho toàn bộ phần Tiêu đề của panel Kết quả</li>
			<li><b>html_view_buttons</b> thay thế cho các nút trong Tiêu đề của panel Kết quả, mặc định có thể là 2 nút Thêm mới và Xóa nhiều</li>
			<li><b>html_view_label</b> thay thế tên Tiêu đề của panel Kết quả, mặc định là "KẾT QUẢ TÌM KIẾM" và <b>pager</b> nếu có</li>
			<li><b>html_view_table</b> thay thế bảng hiển thị của panel Kết quả</li>
			<li><b>html_extra_view</b> được chèn vào ngay dưới bảng hiện thị của panel Kết quả, nằm sau <b>pager</b> nếu có</li>
			<li><b>html_extra_foot</b> được chèn vào cuối tpl sau panel Kết quả</li>
		</ul>
	</div>
</div>