{literal}<div class="w3-code">
    <div>private $table = T_PROVINCE, $action = 'add', $cmd = 'province';</div>
	<div>public $id, $item;</div>
{/literal}</div>
<div class="m-t-20">
	<ul>
		<li><b>$id</b> là id của đối tượng cần sửa, nếu id = 0 nghĩa là thêm mới</li>
		<li><b>$item</b> là dữ liệu của 1 đối tượng cần sửa có id > 0</li>
	</ul>
</div>
{literal}<div class="w3-code m-t-20">
	$this->action = Url::getParamAdmin('action');<br />
	if($this->action == 'edit'){<br />
		<div class="m-l-20">
		$this->id = Url::getParamInt('id', 0);<br />
		if($this->id > 0){
			<div class="m-l-20">$this->item = DB::fetch("SELECT * FROM ".$this->table." WHERE id=".$this->id));</div>
		}<br />
		if(!$this->item){
			<div class="m-l-20">Url::redirect('admin', array('cmd' => $this->cmd));</div>
		}</div>
	}<br />
{/literal}</div>
<div class="m-t-20">
	Đây là đoạn code trong hàm khởi tạo của 1 module Edit cơ bản
</div>