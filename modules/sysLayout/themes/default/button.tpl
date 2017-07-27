{if $button.type == 2}
<a class="btn {$button.color}{if $button.style==1} btn-circle{if $button.size>0}-{$button.size_tit}{/if}{elseif $button.size>0} btn-{$button.size_tit}{/if} waves-effect waves-light{if $button.style == 1} waves-circle waves-float{/if}"{foreach from=$button.ext item=entry key=k} {$k}="{$entry}"{/foreach}>
	{if $button.icon}<i class="material-icons"{if $button.title} style="font-size:16px"{/if}>{$button.icon}</i>{/if}{if $button.title} {$button.title}{/if}</a>
{else}
<button type="{if $button.type==0}submit{else}button{/if}" class="btn {$button.color}{if $button.style==1} btn-circle{if $button.size>0}-{$button.size_tit}{/if}{elseif $button.size>0} btn-{$button.size_tit}{/if} waves-effect waves-light{if $button.style == 1} waves-circle waves-float{/if}"{foreach from=$button.ext item=entry key=k} {$k}="{$entry}"{/foreach}>
{if $button.icon}<i class="material-icons"{if $button.title} style="font-size:16px"{/if}>{$button.icon}</i>{/if}{if $button.title} {$button.title}{/if}</button>
{/if}