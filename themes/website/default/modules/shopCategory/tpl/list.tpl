<div class="contentTop">
	<span class="pageTitle">{$title_module}</span>
	{if $add_cat || $delete_cat}<ul class="quickStats">
		{if $add_cat}<li>
			<a href="{$addUrl}" class="blueImg"><img src="style/images/icons/quickstats/plus.png" alt="" /></a>
			<div class="floatR"><strong class="blue">Thêm</strong><span>danh mục</span></div>
		</li>{/if}
		{if $delete_cat}<li>
			<a href="javascript:void(0)" class="redImg" onclick="if(confirm('Bạn muốn xóa ?')) shop.admin.system.postMethod('{$formName}')"><img src="style/images/icons/quickstats/del.png" alt="" /></a>
			<div class="floatR"><strong class="blue">Xóa</strong><span>danh mục</span></div>
		</li>{/if}
	</ul>{/if}

	<div class="clear"></div>
</div>

<div class="breadLine">
	<div class="bc">
		{$breadcum}
	</div>
</div>

<div class="wrapper">
	
	<div class="widget fluid">
		<table width="100%" cellspacing="0" cellpadding="0" class="tDefault searchForm">
			<tbody>
				<tr>
					<td valign="top">
						<div class="formRow">
							<div class="grid3"><label>Di chuyển đến loại danh mục</label></div>
							<div class="grid6" id="m0">
								<select onchange="shop.auto_scroll('#cateType'+this.value)">
									<option value="-1">-- Chọn --</option>
									{foreach from=$type item=entry key=k}
									<option value="{$k}">{$entry}</option>
									{/foreach}
								</select>
							</div>
							<div class="clear"></div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<div class="divider"><span></span></div>
	
	<div class="msgR">{$msg}</div>

	<div class="widget">
		{* tinh toan so colspan dua theo quyen *}
		{assign var="colspan" value=6}
		{if $add_cat}{assign var="colspan" value=$colspan+1}{/if}
		{if $edit_cat}{assign var="colspan" value=$colspan+1}{/if}
		{if $delete_cat}{assign var="colspan" value=$colspan+2}{/if}
		{* -end- *}
		
		{if $delete_cat}<div class="whead">
			<span class="titleIcon check"><input type="checkbox" id="titleCheck" name="titleCheck" /></span><h6>Select All</h6><div class="clear"></div>
		</div>{/if}
		<table cellpadding="0" cellspacing="0" width="100%" class="tDefault checkAll check" id="checkAll">
			<thead>
				<tr>
					{if $delete_cat}<td><img src="style/images/elements/other/tableArrows.png" alt="" /></td>{/if}
					<td width="30">Sort</td>
                    <td width="30">ID</td>
                    <td width="30">PID</td>
                    <td>Tiêu đề</td>
					<td width="40">Status</td>
					<td width="50">Ảnh</td>
					{if $add_cat}<td width="20">Sub</th>{/if}
					{if $edit_cat}<td width="20">Sửa</th>{/if}
					{if $delete_cat}<td width="20">Xóa</th>{/if}
				</tr>
			</thead>
			<tbody>
				{assign var="curType" value=""}
				
				{foreach from=$items item=items}
					
					{insert name=countArray a=$items.items assign=c}
					
					{if $curType != $type[$items.data.type]}
						{assign var="curType" value=$type[$items.data.type]}
						<tr bgcolor="white">
							<td colspan="{$colspan}" style="text-transform:uppercase;color:black;font-size:16px">
								<a name="cateType{$items.data.type}"></a>
								<div style="margin:10px">
									<b>{$type[$items.data.type]}</b>
								</div>
							</td>
						</tr>
					{/if}
					<tr style="background-color:#C4C4FF">
						{if $delete_cat}<td><input name="selected_ids[]" type="checkbox" value="{$items.data.id}" /></td>{/if}
						<td>{$items.data.weight}</td>
						<td>{$items.data.id}</td>
						<td {$items.data.onclick}>{$items.data.parent_id}</td>
						<td class="{if $c>0}cur_pointer{/if}" {if $c>0} onclick="shop.admin.category.toggle(this)" id="{$items.data.id}"{/if}><b style="text-transform:uppercase">{$items.data.title}{if $c>0} ({$c}){/if}</b></td>
						<td align="center">
							{if $items.data.status == 0}<font color="white">Hide</font>{else}...{/if}
						</td>
						<td>
							{if $items.data.image != ''}<img src="{$items.data.image}" border="0" height="25" alt="IMG" />{/if}
						</td>
						{if $add_cat}<td>
							<a href="{$addUrl}?pid={$items.data.id}">
								<img src="{$myIcon.sub}" width="16" height="16" class="tipS" original-title="Thêm mục con" />
							</a>
						</td>{/if}
						{if $edit_cat}<td>
							<a href="{$editLink}?id={$items.data.id}" >
								<img src="{$myIcon.edit}" class="tipS" original-title="Sửa" width="16" height="16" />
							</a>
						</td>{/if}
						{if $delete_cat}<td>
							<a href="{$delLink}?id={$items.data.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" >
								<img src="{$myIcon.del}" class="tipS" original-title="Xóa" width="16" height="16" />
							</a>
						</td>{/if}
					</tr>
					{foreach from=$items.items item=item}
					{insert name=countArray a=$item.extra assign=c}
					<tr class="close{$items.data.id}">
						<td><input name="selected_ids[]" type="checkbox" value="{$item.id}" /></td>
						<td>{$item.weight}</td>
						<td>{$item.id}</td>
						<td {$items.onclick}>{$item.parent_id}</td>
						<td class="{if $c>0}cur_pointer tog{/if}" {if $c>0} onclick="shop.admin.category.toggle(this)" id="{$item.id}"{/if}><b class="mLeft15">{$item.title}{if $c>0} ({$c}){/if}</b></td>
						<td align="center">
							{if $item.status == 0}<font color="red">Hide</font>{else}...{/if}
						</td>
						<td>
							{if $item.image != ''}<img src="{$item.image}" border="0" height="25" alt="IMG" />{/if}
						</td>
						{if $add_cat}<td>
							<a href="{$addUrl}?pid={$item.id}" title="Thêm mục con">
								<img src="{$myIcon.sub}" width="16" height="16" />
							</a>
						</td>{/if}
						{if $edit_cat}<td>
							<a href="{$editLink}?id={$item.id}" >
								<img src="{$myIcon.edit}" class="tipS" original-title="Sửa" width="16" height="16" />
							</a>
						</td>{/if}
						{if $delete_cat}<td>
							<a href="{$delLink}?id={$item.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" >
								<img src="{$myIcon.del}" class="tipS" original-title="Xóa" width="16" height="16" />
							</a>
						</td>{/if}
					</tr>
						{foreach from=$item.extra item=extra}
						<tr class="close{$items.data.id} close{$item.id} hidden">
							<td><input name="selected_ids[]" type="checkbox" value="{$extra.id}" onclick="shop.checkbox.select('checkall','checkall_ctrl',this.checked);" class="checkall" /></td>
							<td>{$extra.weight}</td>
							<td>{$extra.id}</td>
							<td {$items.onclick}>{$extra.parent_id}</td>
							<td style="color:blue"><b class="mLeft30">{$extra.title}</b></td>
							<td>
								{if $extra.image != ''}<img src="{$extra.image}" border="0" height="25" alt="IMG" />{/if}
							</td>
							{if $add_cat}<td>---</td>{/if}
							{if $edit_cat}<td>
								<a href="{$editLink}?id={$extra.id}" >
									<img src="{$myIcon.edit}" class="tipS" original-title="Sửa" width="16" height="16" />
								</a>
							</td>{/if}
							{if $delete_cat}<td>
								<a href="{$delLink}?id={$extra.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" >
									<img src="{$myIcon.del}" class="tipS" original-title="Xóa" width="16" height="16" />
								</a>
							</td>{/if}
						</tr>
						{/foreach}
					{/foreach}
				{/foreach}
			</tbody>
		</table>
	</div>
</div>
