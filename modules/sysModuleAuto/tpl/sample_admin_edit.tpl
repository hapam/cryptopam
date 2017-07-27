<?php
class Edit{$mod_name}Form extends Form {literal}{{/literal}
    private $cmd = '{$mod_cmd}', $action = 'add', $table = T_{$mod_table}{if $mod_file}, $sizeKey = {$mod_table}_KEY, $folderUpload = {$mod_table}_FOLDER{/if};
	public $item, $id;
    function __construct(){literal}{
		parent::__construct();
        $this->id = Url::getParamInt('id', 0);
        $this->action = Url::getParamAdmin('action', $this->action);
        if ($this->id > 0) {
            $this->item = DB::fetch("SELECT * FROM {$this->table} WHERE id={$this->id}");
            if (!$this->item) {
                Url::redirect('admin', array('cmd' => $this->cmd));
            }{/literal}
{if $mod_file}
			$size = ImageUrl::getSize($this->sizeKey, 'min');
{foreach from=$mod_cols item=entry}{if $entry.edit}
{if $entry.edit_t == 'file'}
			$this->item['{$entry.name}_src'] = {$mod_name_class}::get{$mod_name_class}Image($this->item['{$entry.name}'], $this->item['created'], $size);
{elseif $entry.edit_t == 'time'}
			$this->item['{$entry.name}'] = FunctionLib::dateFormat($this->item['{$entry.name}'], 'd-m-Y');
{/if}
{/if}{/foreach}{/if}
        {literal}}
		if($this->action == 'delete'){
			$this->delete();
		}{/literal}
{if $mod_js}
		$this->link_js_me('{$mod_name}Admin.js', __FILE__);
{/if}	{literal}}{/literal}

    function draw() {literal}{{/literal}
		$data = array();
        {$mod_name_class}::autoEdit($this, $data, 'draw');
    {literal}}{/literal}

    function on_submit() {literal}{{/literal}
		$data = array();
		if({$mod_name_class}::autoEdit($this, $data, 'submit')){literal}{{/literal}
{foreach from=$mod_cols item=entry}{if $entry.edit_t == 'time' && $entry.edit}
			if(!empty($data['{$entry.name}'])){literal}{{/literal}
				$date_arr = explode('-', $data['{$entry.name}']);
				if (isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])) {literal}{{/literal}
					$data['{$entry.name}'] = mktime(0, 0, 0, (int) $date_arr[1], (int) $date_arr[0], (int) $date_arr[2]);
				{literal}}else{{/literal}
					$data['{$entry.name}'] = TIME_NOW;
				{literal}}{/literal}
			}
{/if}{/foreach}
{if $mod_file}
			$time = TIME_NOW;{literal}
			if ($this->action == 'add') {
				$data['created'] = TIME_NOW;
				$this->id = DB::insert($this->table, $data);
			} else {
				$time = $this->item['created'];
			}{/literal}
{/if}
			//upload image
			$err = '';
{foreach from=$mod_cols item=entry}{if $entry.edit_t == 'file' && $entry.edit}
			$file = $_FILES['{$entry.name}'];
			$fileName = isset($data['title']) ? $data['title'] : $file['name'];
			$fileUploadResult = FileHandler::resizeImageOnServer($file, $fileName, $time, $this->sizeKey, $this->folderUpload, $err, $this->item['{$entry.name}']);
			if ($fileUploadResult) {literal}{{/literal}
				$data['{$entry.name}'] = $err;
			{literal}}{/literal} elseif ($err != '') {literal}{{/literal}
				$this->setFormError('', $err);
				return;
			{literal}}{/literal}
{/if}{/foreach}
			if ($this->errNum == 0){literal}{{/literal}
{if $mod_file}
				DB::update($this->table, $data, 'id=' . $this->id);//cap nhat vao db
{else}
				if($this->action == 'edit'){literal}{
					DB::update($this->table, $data,'id='.$this->id);
				}
				else{
					DB::insert($this->table, $data);
				}{/literal}
{/if}
				//chuyen ve trang quan tri
				Url::redirect('admin', array('cmd' => $this->cmd));
			{literal}}
		}
		$this->setFormError('', 'Lỗi! Không lưu được dữ liệu');
    }

	function delete(){{/literal}
		DB::update($this->table, array('status' => -1), "id=".$this->id);
		Url::redirect('admin', array('cmd' => $this->cmd));
	{literal}}
}{/literal}