<?php
class {$mod_name}Form extends Form{literal}{{/literal}
	function __construct(){literal}{{/literal}
{if $mod_js}
		$this->link_js_me('{$mod_name}.js', __FILE__);
{/if}
	{literal}}{/literal}

	function draw(){literal}{{/literal}
		global $display;

		$display->add('hello', '== TYPE CODE HERE ==');
		$display->output('{$mod_name}');
	{literal}}{/literal}
{literal}}{/literal}
