<div class="jumbotron">
	<div class="container">
		<h1>Theo dõi Coin - Poloniex.com</h1>
		<p>Dành riêng cho <b>{$user.fullname}</b></p>
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
						<th>Purchased Price</th>
						<th>Price</th>
						<th>% L/W</th>
						<th>Money L/W</th>
						<th>Inv</th>
						<th>Num</th>
					</tr>
				</thead>
				<tbody>
					{assign var="totalBuy" value=0}
					{foreach from=$entry.pairs item=p}
					{if $default[$p.id]}
					{insert name=calRate assign=check buy=$default[$p.id].price now=$p.last}
					{insert name=calLoss assign=money buy=$default[$p.id].price now=$p.last quan=$default[$p.id].quantity}
					{assign var="totalMoney" value=$money+$totalMoney}
					{assign var="totalBuy" value=$default[$p.id].quantity+$totalBuy}
					<tr>
						<td><b>{$p.name}</b></td>
						<td>{$default[$p.id].price}</td>
						<td>{$p.last}</td>
						<td class="{if $check >= 0}success{else}danger{/if}" align="right">{if $check >= 0}+{/if}{$check|string_format:"%.2f"}</td>
						<td>{if $default[$p.id].quantity > 0}{if $money >= 0}+{/if}{$money|string_format:"%.4f"}{/if}</td>
						<td>{if $default[$p.id].quantity > 0}{$default[$p.id].quantity|string_format:"%.4f"}{/if}</td>
						<td>{if $default[$p.id].quantity > 0}{$default[$p.id].quantity/$default[$p.id].price|string_format:"%.4f"}{/if}</td>
					</tr>
					{/if}
					{/foreach}
					<tr>
						<td colspan="4" align="right"><b>Total</b></td>
						<td>{$totalMoney|string_format:"%.4f"}</td>
						<td>{$totalBuy|string_format:"%.4f"}</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		{/foreach}
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-6 mTop10">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-bitcoin"></i></span>
				<select id="exchange" class="form-control" name="exchange">
					{foreach from=$default item=entry}
						<option value="{$entry.price}">{$entry.pair_name} - {$entry.price}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="col-md-4 mTop10">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon"><b>%</b></i></span>
				<input type="text" class="form-control" id="percent" name="percent" value="5" />
			</div>
		</div>
		<div class="col-md-2 mTop10">
			<button type="button" class="btn btn-primary" onclick="shop.exchange()">Quy đổi</button>
		</div>
	</div>

	<div id="result-exchange" class="mTop10 hide">
		<div class="alert alert-success">
			
		</div>
	</div>
</div>

<script type="text/javascript">
	var timeLoad = {$timeLoad};
	{if $defJson}shop.ready.add(function(){literal}{{/literal}
		shop.follow.data = {$defJson};
	{literal}}{/literal}, false);{/if}
</script>