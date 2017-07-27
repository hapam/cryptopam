{literal}<div class="w3-code">
    <div>private $cmd = 'province', $table = T_PROVINCE;</div>
	<div>public $perm, $link;</div>
{/literal}</div>
<div class="m-t-20">
	2 biến <b>$perm</b> và <b>$link</b> sẽ phải được để <b>public</b> và được khởi tạo trong hàm <b>__construct</b>
	<ul>
		<li><b>$perm</b> chứa các quyền hạn của module (Thêm, sửa, xóa)</li>
		<li><b>$link</b> chứa các link của module (Thêm, sửa, xóa)</li>
	</ul>
</div>
{literal}<div class="w3-code m-t-20">
	$this->link = array(<br />
		<div class="m-l-20">'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),<br />
		'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),<br />
		'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete'))</div>
	);<br /><br />
    $this->perm = array(<br />
		<div class="m-l-20">'add' => User::user_access("add province"),<br />
		'edit' => User::user_access("edit province"),<br />
		'del' => User::user_access("delete province")</div>
	);
{/literal}</div>
<div class="m-t-20">
	Nếu bỏ quyền <b>add</b>, <b>edit</b> hoặc <b>del</b> thì form sẽ không hiển thị nút <b>Thêm mới</b>, <b>Sửa</b> hoặc <b>Xóa</b> <br />
	Tương tự nếu thiếu link <b>add</b>, <b>edit</b> hoặc <b>del</b> thì form sẽ không hiển thị nút <b>Thêm mới</b>, <b>Sửa</b> hoặc <b>Xóa</b> 
</div>