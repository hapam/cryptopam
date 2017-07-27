<div class="jumbotron">
	<div class="container">
		<h1>Cấu hình</h1>
		<p>Chỉnh sửa các thông số vận hành</p>
	</div>
</div>
<div class="container">
	
	{if $msg}
	<div class="col-md-0">
		<div class="alert alert-danger alert-dismissable">
			<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			{$msg}
		</div>
	</div>	
	{/if}
	
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
						<th>Current</th>
						<th width="20%">Bought</th>
						<th width="20%">Money</th>
						<th width="20%">% Alert</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$entry.pairs item=p}
					<tr{if $default[$p.id]} class="{if $default[$p.id].quantity==0}danger{else}success{/if}"{/if}>
						<td><b>{$p.name}</b></td>
						<td>{$p.last}</td>
						<td><input class="form-control" type="text" name="buy{$p.id}" value="{$default[$p.id].price}" /></td>
						<td><input class="form-control" type="text" name="quan{$p.id}" value="{$default[$p.id].quantity}" /></td>
						<td>
							<input class="form-control" type="text" name="alert{$p.id}" value="{$default[$p.id].alert}" />
							<input type="hidden" name="last{$p.id}" value="{$p.last}" />
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{/foreach}
	</div>
	
	<div class="col-md-0">
		<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
		<button type="button" class="btn btn-danger" onclick="history.go(-1)">Hủy bỏ</button>
	</div>
</div>