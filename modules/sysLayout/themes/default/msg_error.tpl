{foreach from=$msg_data item=entry}
<div class="alert bg-pink alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	{$entry}
</div>
{/foreach}