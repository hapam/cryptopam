{if $panel.per}<div class="col-lg-{$panel.size.lg} col-md-{$panel.size.md} col-sm-{$panel.size.sm} col-xs-{$panel.size.xs}" id="p{$panel.id}">
	<div class="card">
		{if $panel.header}
		<div class="header{if $panel.color_head} bg-{$panel.color_head}{/if}">
			<h2>
				{if $panel.toggle}
					<a href="#{$panel.id}" aria-expanded="false" aria-controls="{$panel.id}" data-toggle="collapse">
						{$panel.title}</a>
				{else}
					{$panel.title}
				{/if}
			</h2>
			{if $panel.menu}
			<ul class="header-dropdown m-r--5">
				<li class="dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
						<i class="material-icons">more_vert</i></a>
					<ul class="dropdown-menu pull-right">
						{foreach from=$panel.menu item=entry}
						<li><a href="{$entry.link}" class="waves-effect waves-light waves-block"{foreach from=$entry.ext item=e key=k} {$k}="{$e}"{/foreach}>
							{if $entry.icon}<i class="material-icons" style="font-size:16px;margin-top:4px">{$entry.icon}</i> {/if}{$entry.title}
						</a></li>
						{/foreach}
					</ul>
				</li>
			</ul>{/if}
		</div>{/if}
		<div class="clearfix body{if $panel.toggle} collapse{if !$panel.hide} in{/if}{/if}{if $panel.color_body} bg-{$panel.color_body}{/if}"{if $panel.toggle} id="{$panel.id}"{/if}>
			{$panel.html}
		</div>
	</div>
</div>{/if}