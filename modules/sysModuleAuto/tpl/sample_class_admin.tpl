<?php
class {$mod_name} extends Module{literal}{{/literal}
	static function permission(){literal}{{/literal}
		return array(
			"admin {$mod_name}"  => "Quản trị",
{if $mod_edit}
			"add {$mod_name}"  => "Thêm",
			"edit {$mod_name}"  => "Sửa",
{/if}
			"delete {$mod_name}"  => "Xóa"
		);
	{literal}}{/literal}
	function __construct($row){literal}{{/literal}
		Module::Module($row);

		if(Url::isAdminUrl()){literal}{{/literal}
			$cmd = Url::getParamAdmin('cmd','');
			if ($cmd == '{$mod_cmd}' && User::user_access('admin {$mod_name}', 0, 'access_denied')) {literal}{{/literal}
				$action = Url::getParamAdmin('action', '');
				switch ($action) {literal}{{/literal}
{if $mod_edit}
					case 'add':
						if (User::user_access('add {$mod_name}', 0, 'access_denied')) {literal}{{/literal}
							require_once 'forms/admin_edit.php';
							$this->add_form(new Edit{$mod_name}Form());
						{literal}}{/literal}
						break;
					case'edit':
						if (User::user_access('edit {$mod_name}', 0, 'access_denied')) {literal}{{/literal}
							require_once 'forms/admin_edit.php';
							$this->add_form(new Edit{$mod_name}Form());
						{literal}}{/literal}
						break;
{/if}
					case 'delete':
						$id = Url::getParamInt('id', 0);
						if ($id > 0) {literal}{{/literal}
							if (User::user_access('delete {$mod_name}', 0, 'access_denied')) {literal}{{/literal}
								require_once 'forms/admin_edit.php';
								$this->add_form(new Edit{$mod_name}Form());
							{literal}}{/literal}
						{literal}}{/literal}
						Url::redirect('admin', array('cmd' => '{$mod_cmd}'));
						break;
					default:
						require_once 'forms/admin_list.php';
						$this->add_form(new List{$mod_name}Form());
				{literal}}{/literal}
			{literal}}{/literal}
		{literal}}{/literal}else{literal}{{/literal}
			require_once 'forms/{$mod_name}.php';
			$this->add_form(new {$mod_name}Form());
		{literal}}{/literal}
	{literal}}{/literal}
{literal}}{/literal}