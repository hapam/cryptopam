<div style="margin-top:10px">
	<h2 class="card-inside-title">Yahoo</h2>
	<div id="list_item_yahoo">
		{foreach item=yahoo from=$yahoo key=k}
			<div class="row clearfix">
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Yahoo ID" name="yahoo{$k}" value="{$yahoo.id}" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">face</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Tên hiển thị" name="yahooName{$k}" value="{$yahoo.name}" />
						</div>
					</div>
				</div>
			</div>
		{foreachelse}
			<div class="row clearfix">
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Yahoo ID" name="yahoo1" value="" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">face</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Tên hiển thị" name="yahooName1" value="" />
						</div>
					</div>
				</div>
			</div>
		{/foreach}
		<input name="list_item_yahoo_num" id="list_item_yahoo_num" value="{$yahooNum|default:1}" type="hidden" />
	</div>
	<div><button type="button" class="btn btn-success btn-circle waves-effect waves-circle waves-float" onclick="shop.admin.province.addMessengerInput('list_item_yahoo','yahoo');" title="Thêm nick hỗ trợ">
        <i class="material-icons">add</i></button></div>
</div>

<div class="mTop20">
	<h2 class="card-inside-title">Skype</h2>
	<div id="list_item_skype">
		{foreach item=skype from=$skype key=k}
			<div class="row clearfix">
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="skype ID" name="skype{$k}" value="{$skype.id}" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">face</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Tên hiển thị" name="skypeName{$k}" value="{$skype.name}" />
						</div>
					</div>
				</div>
			</div>
		{foreachelse}
			<div class="row clearfix">
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">person</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="skype ID" name="skype1" value="" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="material-icons">face</i>
						</span>
						<div class="form-line">
							<input type="text" class="form-control" placeholder="Tên hiển thị" name="skypeName1" value="" />
						</div>
					</div>
				</div>
			</div>
		{/foreach}
		<input name="list_item_skype_num" id="list_item_skype_num" value="{$skypeNum|default:1}" type="hidden" />
	</div>
	<div><button type="button" class="btn btn-success btn-circle waves-effect waves-circle waves-float" onclick="shop.admin.province.addMessengerInput('list_item_skype','skype');" title="Thêm nick hỗ trợ">
        <i class="material-icons">add</i></button></div>
</div>