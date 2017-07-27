<?php
class List{$mod_name}Form extends Form {literal}{{/literal}
    private $cmd = '{$mod_cmd}', $table = T_{$mod_table}{if $mod_file}, $size = 0, $sizeKey = {$mod_table}_KEY{/if};
	public $perm, $link;
    function __construct() {literal}{{/literal}
		parent::__construct();
	    $this->perm = array(
{if $mod_edit}
			'add' => User::user_access("admin {$mod_name}"),
			'edit' => User::user_access("edit {$mod_name}"),
{/if}
			'del' => User::user_access("delete {$mod_name}")
        );
		$this->link = array(
{if $mod_edit}
			'add' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'add')),
            'edit' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'edit')),
{/if}
			'del' => Url::buildAdminURL('admin', array('cmd' => $this->cmd, 'action' => 'delete'))
        );
{if $mod_file}
		$this->size = ImageUrl::getSize($this->sizeKey, 'min');
{/if}
{if $mode_js}
		$this->link_js_me('{$mod_name}Admin.js', __FILE__);
{/if}	{literal}}{/literal}

    function draw() {literal}{{/literal}
        global $display;
{foreach from=$mod_cols item=entry}{if $entry.filter}{if $entry.edit_t == 'time'}
        ${$entry.name}_time_from = Url::getParam('{$entry.name}_time', '');
        ${$entry.name}_time_to = Url::getParam('{$entry.name}_time_to', '');
{else}
		${$entry.name} = {if $entry.type == 'tinyint' || $entry.type == 'int'}Url::getParamInt('{$entry.name}', -69){else}Url::getParam('{$entry.name}', ''){/if};
{/if}{/if}{/foreach}
        $order_by = Url::getParam('order_by', 'id');
        $order_dir = Url::getParam('order_dir', 'DESC');

        $cond = array();
{foreach from=$mod_cols item=entry}
{if $entry.filter}{if $entry.edit_t == 'time'}
		// search time
        if (${$entry.name}_time_from) {literal}{{/literal}
            $date_arr = explode('-', ${$entry.name}_time_from);
            if (isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])) {literal}{{/literal}
                ${$entry.name}_time_from = mktime(0, 0, 0, (int) $date_arr[1], (int) $date_arr[0], (int) $date_arr[2]);
                $cond[] = "{$entry.name} >= ${$entry.name}_time_from";{literal}
			}
		}{/literal}
        if (${$entry.name}_time_to) {literal}{{/literal}
            $date_arr = explode('-', ${$entry.name}_time_to);
            if (isset($date_arr[0]) && isset($date_arr[1]) && isset($date_arr[2])) {literal}{{/literal}
                ${$entry.name}_time_to = mktime(23, 59, 59, (int) $date_arr[1], (int) $date_arr[0], (int) $date_arr[2]);
                $cond[] = "{$entry.name} <= ${$entry.name}_time_to";{literal}
            }
        }
{/literal}{elseif $entry.name == 'status'}
		if($status == -69){literal}{{/literal}
			$cond[] = "status > 0";
		{literal}}else{{/literal}
			$cond[] = "status = $status";
		{literal}}{/literal}
{elseif $entry.name == 'id'}{assign var="check_id" value="ok"}{else}
		if(${$entry.name} != {if $entry.type == 'tinyint' || $entry.type == 'int'}-69{else}''{/if}){literal}{{/literal}
{if $entry.type == 'tinyint' || $entry.type == 'int'}
			$cond[] = "{$entry.name} = '".${$entry.name}."'";
{else}
			$cond[] = "{$entry.name} LIKE '%".${$entry.name}."%'";
{/if}		{literal}}{/literal}
{/if}{/if}
{/foreach}
{if $check_id == 'ok'}
		if($id > 0){literal}{{/literal}
			$cond = array("id = $id");
		{literal}}{/literal}
{/if}

        $data = array();
        $paging = '';
        $item_per_page = 20;

        $search_value = FunctionLib::addCondition($cond);
        $search_value = ($search_value != '') ? ' WHERE ' . $search_value : '';

        $sql = 'SELECT * FROM  ' . $this->table . $search_value . ' ORDER BY ' . $order_by . ' ' . $order_dir;
        $re = Pagging::pager_query($sql, $item_per_page);
        if ($re) {literal}{{/literal}
            while ($r = mysql_fetch_assoc($re)) {literal}{{/literal}
{if $mod_file}{foreach from=$mod_cols item=entry}
{if $entry.show && $entry.edit_t == 'file'}
				$r['{$entry.name}'] = {$mod_name_class}::get{$mod_name_class}Image($r['{$entry.name}'], $r['created'], $this->size);
				$r['{$entry.name}'] = $r['{$entry.name}'] ? '<img src="'.$r['{$entry.name}'].'" width="80" />' : ' --- ';
{/if}
{/foreach}{/if}
{foreach from=$mod_cols item=entry}
{if $entry.show && $entry.edit_t == 'time'}
				$r['{$entry.name}'] = $r['{$entry.name}'] > 0 ? FunctionLib::dateFormat($r['{$entry.name}'], 'd/m/Y H:i:s') : '---';
{/if}
{/foreach}
                $data[$r['id']] = $r;
            {literal}}{/literal}
            $paging = Pagging::getPager(3, false, 'page_no', true);
        {literal}}{/literal}

		{$mod_name_class}::autoList($this, array(
			'items' => $data,
			'pagging' => array(
				'start_page' => (Pagging::$page-1)*$item_per_page,
				'total_item' => Pagging::$totalResult,
				'total_page' => Pagging::$totalPage,
				'pager'	=> $paging
			)
		));
    {literal}}{/literal}

    function on_submit() {literal}{{/literal}
        if (!empty($_POST['selected_ids'])) {literal}{{/literal}
            $ids = implode(',', $_POST['selected_ids']);
            DB::update($this->table, array("status" => -1), " id IN ($ids)");

            $this->setFormSucces('', "Xóa thành công! Bản ghi có mã ($ids) đã bị xóa");
        {literal}}{/literal} else {literal}{{/literal}
            $this->setFormError('', "Bạn chưa chọn bản ghi cần xóa");
        {literal}}{/literal}
    {literal}}{/literal}
{literal}}{/literal}
