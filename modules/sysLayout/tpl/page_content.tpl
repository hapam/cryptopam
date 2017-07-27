<div id="page-edit-top">
	<div style="padding:10px">
		<div style="float:left">
			<b>Page</b>&nbsp;&nbsp;&nbsp;&nbsp;
			<select onchange="shop.change_page(this.value);">{$page_list}</select>
		</div>
		<div style="float:left;padding:3px 0 0 10px">
			<a href="?q={$name}" title="Xem trước" target="_blank">
				<img src="css/images/zoom.png" width="16" height="16" />
			</a>
			&nbsp;&nbsp;
			<a href="?q=page&cmd=edit&id={$id}" title="Sửa thông tin của trang">
				<img src="css/images/edit.png" width="16" height="16" />
			</a>
			&nbsp;&nbsp;
			<a href="?q=page" title="Quản trị PAGES">
				<img src="css/images/page_copy.png" width="16" height="16" />
			</a>
		</div>
		<div class="c"></div>
	</div>
	
	<div style="border-top:2px dotted red;margin:10px 0 0;padding:20px 10px 10px">
		<b>Layout website</b>&nbsp;
		<select name="layout" id="layout" onchange="shop.change_layout(this.value);">{$option_layout}</select>
		<br />
		<b>Themes: </b><font color="red">{$page_content.themes}</font>
	</div>
</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="white"><tr><td align="center" style="border-top:2px solid #00FF00">{$regions}</td></tr></table>
	
{if $option_layout_mobile}
<div id="page-edit-bottom">
	<div style="border-top:2px dotted red;margin:25px 0 0;padding:20px 10px 10px">
		<b>Layout mobile</b>&nbsp;
		<select name="layout_mobile" id="layout_mobile" onchange="shop.change_layout(this.value, true);">{$option_layout_mobile}</select>
		<br />
		<b>Themes: </b><font color="red">{$page_content.themes_mobile}</font>
	</div>
</div>	

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="white"><tr><td align="center" style="border-top:2px solid #00FF00">{$regions_mobile}</td></tr></table>
{/if}


<script type="text/javascript">
	var page_id = {$id};
	shop.change_page = function(page_id){literal}{
		shop.redirect('?q=edit_page&id='+page_id);
	};
	shop.change_layout = function(id, mobile){
		mobile = mobile ? 'change_layout_mobile' : 'change_layout';
		shop.redirect('?q=edit_page&id='+page_id+'&cmd='+mobile+'&new_layout='+id);
	};
</script>{/literal}
