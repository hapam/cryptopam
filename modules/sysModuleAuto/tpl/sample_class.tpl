<?php
class {$mod_name} extends Module{literal}{{/literal}
	static function permission(){literal}{{/literal}
		return array();
	{literal}}{/literal}
	function __construct($row){literal}{{/literal}
		Module::Module($row);

		require_once 'forms/{$mod_name}.php';
		$this->add_form(new {$mod_name}Form());
	{literal}}{/literal}
{literal}}{/literal}