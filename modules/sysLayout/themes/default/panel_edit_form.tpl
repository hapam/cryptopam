{if $formGroup.per}
	<div class="col-lg-{$formGroup.size.lg} col-md-{$formGroup.size.md} col-sm-{$formGroup.size.sm} col-xs-{$formGroup.size.xs}" id="{$formGroup.id}-parent"{foreach from=$formGroup.ext item=entry key=k} {$k}="{$entry}"{/foreach}>
		<div class="card">
			{if $formGroup.header}
			<div class="header{if $formGroup.color_head} bg-{$formGroup.color_head}{/if}">
				<h2>
					{if $formGroup.toggle}
						<a href="#{$formGroup.id}" aria-expanded="false" aria-controls="{$formGroup.id}" data-toggle="collapse">
							{$formGroup.title}</a>
					{else}
						{$formGroup.title}
					{/if}
				</h2>
			</div>{/if}
			<div class="clearfix body{if $formGroup.toggle} collapse{if !$formGroup.hide} in{/if}{/if}{if $formGroup.color_body} bg-{$formGroup.color_body}{/if}"{if $formGroup.toggle} id="{$formGroup.id}"{/if}>
				{foreach from=$formGroup.items item=entry}
					{$entry}
				{/foreach}
				{foreach from=$formGroup.sub item=entry}
					{$entry.html}
				{/foreach}
			</div>
		</div>
	</div>
{/if}