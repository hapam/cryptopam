{literal}<div class="w3-code">
$this->layout->addItemView('id', array(
	<div class="m-l-20">
	'title' => 'ID',<br />
	'order' => true,<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Đưa các thành phần vào bảng hiển thị
	<ul>
		<li><b>'id'</b> là từ khóa định danh, cũng chính là tên trường trong mảng dữ liệu truyền vào <b>items</b> trong hàm <b>genFormAuto</b>. 1 số key cố định: <b>btn-edit</b> (Nút sửa), <b>btn-del</b> (Nút xóa)</li>
		<li><b>title</b> là header của cột dữ liệu</li>
		<li><b>order</b> nhận giá trị <b>true</b> hoặc <b>false</b>, để xác định xem có sắp xếp cột nào đó không</li>
		<li><b>head</b> các thành phần sẽ được chèn vào thẻ tiêu đề <b>th</b>, ví dụ như: class, id, style, .... </li>
		<li><b>ext</b> các thành phần sẽ được chèn vào thẻ <b>td</b>, ví dụ như: class, id, style, .... </li>
		<li><b>type</b> nhận các giá trị: <b>del</b> (Ô checkbox xóa), <b>index</b> (Hiển thị số thứ tự), <b>icon</b> (Hiển thị icon)</li>
		<li><b>only</b> nhận các giá trị: <b>true</b> hoặc <b>false</b>, xác định kiểu icon đơn giản hay đầy đủ
		<li><b>per</b> nhận các giá trị: <b>true</b> hoặc <b>false</b>, quyền xem cột dữ liệu nào đó, nếu không có quyền xem thì không hiển thị
	</ul>
</div>

{literal}<div class="w3-code">
$r['btn-del'] = array('hide' => true);
{/literal}</div>
<div class="m-t-20">
	Muốn ẩn ô có kiểu icon chỉ cần thêm như trên lúc fetch dữ liệu
</div>

{literal}<div class="w3-code m-t-20">
$this->layout->addItemView('btn-del-check', array(
	<div class="m-l-20">
	'per'	=>	$this->perm['del'],<br />
	'type'	=>	'del',<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Ví dụ về ô checkbox xóa
</div>

{literal}<div class="w3-code m-t-20">
$this->layout->addItemView('index', array(
	<div class="m-l-20">
	'title' => 'STT',<br />
	'type' => 'index',<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Ví dụ về hiển thị số thứ tự
</div>

{literal}<div class="w3-code m-t-20">
$r['cache'] = $this->link['clean']."?id=".$r['id'];
<br /><br />
$this->layout->addItemView('cache', array(
	<div class="m-l-20">
	'title' => 'Cache',<br />
	'type'  =>	'icon',<br />
	'icon' => 'cached',<br />
	'per'   => $this->perm['edit'],<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Ví dụ về icon đơn giản - Khai báo & thêm vào group
</div>

{literal}<div class="w3-code m-t-20">
$r['active_icon'] = array(
	<div class="m-l-20">
	'icon' => 'check_circle',<br />
	'des'  => "Click để thay đổi trạng thái kích hoạt", <br />
	'color'=> ($r['is_active'] == 1) ? '' : 'grey', <br />
	'link' => "javascript: shop.admin.user.changeActive(this,".$r['id'].",".$r['is_active'].")"
	</div>
);<br/><br/>

$this->layout->addItemView('active_icon', array(
	<div class="m-l-20">
	'title' => 'Active',<br />
	'type'  => 'icon',<br />
	'only'	=> true,<br />
	'per'   => $this->perm['block'],<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Ví dụ về icon đầy đủ - Khai báo & thêm vào group
</div>

{literal}<div class="w3-code m-t-20">
$this->layout->addItemView('btn-del', array(
	<div class="m-l-20">
	'title' => 'Xóa',<br />
	'type'  =>	'icon',<br />
	'per'   => $this->perm['del'],<br />
	'head' => array(
		<div class="m-l-20">'width' => 50</div>
	),<br />
	'ext' => array(
		<div class="m-l-20">'align' => 'center'</div>
	)</div>
));
{/literal}</div>
<div class="m-t-20">
	Ví dụ về icon xóa, sửa - Khai báo & thêm vào group
</div>