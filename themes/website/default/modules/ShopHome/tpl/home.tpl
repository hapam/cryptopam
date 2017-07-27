<div class="jumbotron">
	<div class="container">
		<h1>Theo dõi Coin - Poloniex.com</h1>
		<p>Xin chào, <b>{$user.fullname}</b>. Chúc bạn một ngày tốt lành!</p>
		<div><b>{$time}</b></div>
	</div>
</div>

<div class="container" id="home-ctrl">
	<ul class="nav nav-tabs">
	{foreach from=$tickers item=entry name=i}
		<li{if $smarty.foreach.i.first} class="active"{/if}><a data-toggle="tab" href="#{$entry.data.name}">{$entry.data.name}</a></li>
	{/foreach}
	</ul>
	<div class="mTop15 tab-content" id="coinTable">
		{foreach from=$tickers item=entry name=i}
		<div id="{$entry.data.name}" class="tab-pane fade{if $smarty.foreach.i.first} in active{/if}">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Coin</th>
						<th>Price</th>
						{*<th>Volumn</th>*}
						<th>Change</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$entry.pairs item=p}
					<tr>
						<td><b>{$p.name}</b></td>
						<td>{$p.last}</td>
						{*<td>{$p.baseVolume}</td>*}
						<td class="{if $p.percentChange > 0}success{else}danger{/if}">{if $p.percentChange > 0}+{/if}{$p.percentChange*100|string_format:"%.2f"}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{/foreach}
	</div>
</div>

<script type="text/javascript">
	var timeLoad = {$timeLoad};
</script>