{if $tab.per && $tab.items}
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
			<div class="body">
				<ul class="nav nav-tabs" role="tablist">
					{foreach from=$tab.items item=entry}
					<li role="presentation"{if $entry.active} class="active"{/if}>
						<a href="#{$entry.id}" data-toggle="tab"{if $entry.active} aria-expanded="true"{/if} onclick="shop.openTab('{$entry.id}')">
							{if $entry.icon}<i class="material-icons">{$entry.icon}</i>{/if} <span style="text-transform:uppercase">{$entry.title}</span>
						</a>
					</li>{/foreach}
				</ul>
				
				<div class="tab-content clearfix">
					{foreach from=$tab.items item=entry}
					<div role="tabpanel" class="tab-pane fade{if $entry.active} active in{/if}" id="{$entry.id}">
						{$entry.html}
					</div>{/foreach}
				</div>
			</div>
		</div>
	</div>
</div>
{/if}