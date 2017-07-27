<div class="block-header{if $label.class} {$label.class}{/if}"{foreach from=$label.ext item=e key=k} {$k}="{$e}"{/foreach}>
	<h2>{$label.title}{if $label.des}<small>{$label.des}</small>{/if}</h2>
</div>