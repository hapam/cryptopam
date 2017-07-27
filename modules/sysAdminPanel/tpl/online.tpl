{foreach from=$online item=entry name=k}
<div class="uOnline">
	<a href="javascript:void(0)" onclick="jQuery('.info{$entry.id}').slideToggle()" title="Xem thêm thông tin">{if $entry.fname}{$entry.fname}{else}{$entry.name}{/if} ({$entry.gender})</a>
</div>
<ul class="info{$entry.id} uInfo"{if $smarty.foreach.k.last} id="noBoder"{/if} style="display:none">
	<li>Account: <b>{$entry.name}</b></li>
	{if $entry.email}<li>Email: <b>{$entry.email}</b></li>{/if}
	{if $entry.phone}<li>Phone: <b>{$entry.phone}</b></li>{/if}
	<li>Thao tác gần đây: <font color="green">{$entry.time|date_format:"%d/%m/%Y - %H:%M:%S"}</font></li>
	{if $entry.roles}<li>Quyền: <font color="red">{$entry.roles}</font></li>{/if}
</ul>
{/foreach}