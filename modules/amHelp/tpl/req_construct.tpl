{literal}<div class="w3-code">
    <div>function __construct() {</div>
        <div class="m-l-20">parent::__construct();</div>
	<div>}</div>
{/literal}</div>
<div class="m-t-20">Mục đích là để khởi tạo <b>FormLayout</b> được gán trong biến <b>layout</b> nằm trong class <b>Form</b>, từ đó ta có thể dùng <b>$this->layout</b> để sinh HTML tự động theo mẫu trong hàm <b>draw</b> của <b>Form</b></div>