{literal}<div class="w3-code">
$this->layout->addGroup('main', array('title' => 'Thông tin'));<br />
$this->layout->addGroup('filter', array('title' => 'Bộ lọc'));
{/literal}</div>
<div class="m-t-20">
	Tạo ra các nhóm phục vụ tìm kiếm
	<div>
		<b>Lưu ý: </b>
		<ul>
			<li><b>main</b> được tạo lúc đầu, nên buộc phải dùng key <b>main</b> cho nhóm 1, hoặc phải xóa nhóm <b>main</b> trước khi addGroup (dùng hàm removeGroup)</li>
			<li>Chiều rộng của các nhóm sẽ được tự động chia, ví dụ có 1 nhóm thì width = 100%, 2 nhóm width = 50%</li>
		</ul>
	</div>
</div>
{literal}<div class="w3-code">
$form->layout->addItem('search_username', array(
	'type'	=> 'text',
	'title' => 'Tìm theo tên đăng nhập'
), 'main');
{/literal}</div>
<div class="m-t-20">
	Đưa các input form vào từng nhóm bộ lọc tìm kiếm, xem thêm các loại <a href="{$link_input}" target="_blank" class="col-green"><b>input form</b></a>
</div>