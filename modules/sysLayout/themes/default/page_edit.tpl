{$msg}

<div class="row clearfix">
{foreach from=$formGroupItems item=entry}
	{$entry.html}
{/foreach}
</div>

<div style="margin:10px 0 30px">
	{if $formData.html_button_submit}
		{$formData.html_button_submit}
	{else}
	<button type="button" class="btn btn-lg btn-success waves-effect waves-light" onclick="{if $formConf.onsubmit}{$formConf.onsubmit}(document.{$formConf.name}){else}document.{$formConf.name}.submit(){/if}">{if $formData.label_button_submit}{$formData.label_button_submit}{else}<i class="material-icons" style="font-size:16px">done</i> Lưu thay đổi{/if}</button>{/if}
	{if $formData.html_button_cancel}
		{$formData.html_button_cancel}
	{else}
	<button type="button" class="btn btn-lg btn-danger waves-effect waves-light m-l-20" onclick="{$formConf.onback}">{if $formData.label_button_cancel}{$formData.label_button_cancel}{else}<i class="material-icons" style="font-size:16px">clear</i> Hủy bỏ{/if}</button>{/if}
</div>