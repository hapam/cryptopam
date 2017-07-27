<?php
class {$mod_name_class}{literal}{{/literal}
{if $mod_check.file}
	static function get{$mod_name_class}Image($img = '', $time = 0, $size = 0) {literal}{{/literal}
        return ImageUrl::getImageURL($img, $time, $size, {$mod_table}_KEY, {$mod_table}_FOLDER);
    {literal}}{/literal}
{/if}
	static function autoList(&$form, $data = array()){literal}{{/literal}
		$form->layout->init(array(
			'style'		=>	'list',
			'method'	=>	'GET'
		));
		//add group search
		$form->layout->addGroup('main', array('title' => 'Thông tin'));
{if $mod_groups.opt}
		$form->layout->addGroup('filter', array('title' => 'Bộ lọc'));
{/if}
{if $mod_groups.time}
		$form->layout->addGroup('time', array('title' => 'Thời gian'));
{/if}
		//add item to search
{foreach from=$mod_groups.search item=entry}
		$form->layout->addItem('{$entry.name}', array(
			'type'	=> '{$entry.edit_t}',
			'title' => '{if $entry.title}{$entry.title}{else}{$entry.name}{/if}'
		), 'main');
{/foreach}
{foreach from=$mod_groups.opt item=entry}
		${$entry.name}Opt = array(
			-69 => '-- Không chọn --',
{foreach from=$entry.opt item=i}
			{$i.k} => '{$i.v}',
{/foreach}
		);
		$form->layout->addItem('{$entry.name}', array(
			'type'	=> 'select',
			'title' => '{if $entry.title}{$entry.title}{else}{$entry.name}{/if}',
			'options' => FunctionLib::getOption(${$entry.name}Opt, '')
		), 'filter');
{/foreach}
{foreach from=$mod_groups.time item=entry}
		$form->layout->addItem('{$entry.name}_time', array(
			'type'	=> 'text',
			'title' => '{if $entry.title}{$entry.title}{else}{$entry.name}{/if} từ',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016'
		), 'time');
		$form->layout->addItem('{$entry.name}_time_to', array(
			'type'	=> 'text',
			'title' => '{if $entry.title}{$entry.title}{else}{$entry.name}{/if} đến',
			'time'  => true,
			'holder'=> 'Ext: 30-07-2016'
		), 'time');
{/foreach}
		
		//add item to view
		$form->layout->addItemView('btn-del-check', array(
			'per'	=>	$form->perm['del'],
			'type'	=>	'del',
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
{foreach from=$mod_cols item=entry}
{if $entry.show}
		$form->layout->addItemView('{$entry.name}', array(
			'title' => '{if $entry.title}{$entry.title}{else}{$entry.name}{/if}',
{if $entry.edit_t == 'number' || $entry.name == 'id' || $entry.edit_t == 'time' || $entry.edit_t == 'file'}
			'head' => array(
				'width' => {if $entry.edit_t == 'file'}105{else}50{/if}
			),
			'ext' => array(
				'align' => 'center'
			)
{/if}
		));
{/if}
{/foreach}
{if $mod_edit}
		$form->layout->addItemView('btn-edit', array(
			'title' =>	'Sửa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['edit'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
{/if}
		$form->layout->addItemView('btn-del', array(
			'title' =>	'Xóa',
			'type'  =>	'icon',
			'per'	=>	$form->perm['del'],
			'head' => array(
				'width' => 50
			),
			'ext' => array(
				'align' => 'center'
			)
		));
		
		return $form->layout->genFormAuto($form, $data);
	{literal}}

	static function autoEdit(&$form, &$data = array(), $action = ''){{/literal}
		$form->layout->init(array(
			'style'		=>	'edit',
			'method'	=>	'POST'
		));

		//add group
		$form->layout->addGroup('main', array('title' => 'Thông tin cơ bản'));
{if $mod_check.file}
		$form->layout->addGroup('gr-image', array('title' => 'Hình ảnh'));
{/if}
{if $mod_check.text}
		$form->layout->addGroup('gr-text', array('title' => 'Nội dung'));
{/if}

		//add form item by Group main
		$form->layout->addItem('id', array(
			'type'	=> 'hidden',
			'value' => $form->id,
			'save'  => false
		), 'main');
{foreach from=$mod_cols item=entry}{if $entry.edit}
{if $entry.edit_t == 'select'}
		${$entry.name}Opt = array(
{foreach from=$mod_groups.opt[$entry.name] item=i}
			'{$i.k}' => '{$i.v}',
{/foreach}
		);
{/if}
		$form->layout->addItem('{$entry.name}', array(
			'type'	=> '{if $entry.edit_t == 'checkbox-onoff'}checkbox{elseif $entry.edit_t == 'time' || $entry.edit_t == 'number'}text{elseif $entry.edit_t == 'textarea-fck'}textarea{else}{$entry.edit_t}{/if}',
{if $entry.require}
			'required' => true,
{/if}
{if $entry.edit_t != 'checkbox-onoff'}
			'title' => '{$entry.title}',
{else}
			'label' => '{$entry.title}',
			'label_pos' => 'left',
			'style' => 'onoff',
{/if}
{if $entry.edit_t == 'select'}
			'options' => FunctionLib::getOption(${$entry.name}Opt, Url::getParam('{$entry.name}', $form->item['{$entry.name}'])),
{elseif $entry.edit_t != 'checkbox-onoff' && $entry.edit_t != 'checkbox' && $entry.edit_t != 'checkbox-group' && $entry.edit_t != 'radio-group' && $entry.edit_t != 'radio'}
{if $entry.edit_t != 'file'}
			'value' => Url::getParam{if $entry.edit_t == 'number'}Int{/if}('{$entry.name}', $form->item['{$entry.name}']),
{/if}
{if $entry.edit_t == 'time'}
			'time' => true,
{/if}
{if $entry.edit_t == 'number'}
			'number' => true,
			'ext' => array(
				'onkeypress' => 'return shop.numberOnly(this, event)',
				'maxlength'  => 5
			),
{elseif $entry.edit_t == 'textarea-fck'}
			'editor'=> true,
			'image' => true,
			'width' => 700,
			'height'=> 300,
{elseif $entry.edit_t == 'textarea'}
			'ext' => array(
				'rows' => 10
			),
{elseif $entry.edit_t == 'file'}
			'save' => false,
			'old'=> array(
				'id' => 'old_{$entry.name}',
				'value' => $form->item['{$entry.name}'],
				'src' => $form->item['{$entry.name}_src'],
				'ext' => array(
					'style' => 'border:2px dashed #ccc;padding:2px',
					'height'=> 50
				)
			),
{/if}
{else}
			'checked' => Url::getParamInt('{$entry.name}', $form->item['{$entry.name}']) == 1,
{/if}
		), {if $entry.edit_t == 'file'}'gr-image'{elseif $entry.edit_t == 'textarea' || $entry.edit_t == 'textarea-fck'}'gr-text'{else}'main'{/if});
{/if}{/foreach}
		if($action == 'draw'){literal}{
			return $form->layout->genFormAuto($form, $data);
		}elseif($action == 'submit'){
			return $form->auto_submit($data);
		}
		return false;
	}
}{/literal}