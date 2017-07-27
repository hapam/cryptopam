<ul class="nav nav-tabs tab-nav-right" role="tablist">
	<li role="presentation"><a href="#skins" data-toggle="tab">GIAO DIỆN</a></li>
	<li role="presentation" class="active"><a href="#settings" data-toggle="tab">CẤU HÌNH</a></li>
</ul>
<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade" id="skins">
		<ul class="demo-choose-skin">
			{foreach from=$themes item=entry key=k}
			<li data-theme="{$k}"{if $k==$theme_default} class="active"{/if}>
				<div class="{$k}"></div>
				<span>{$entry}</span>
			</li>
			{/foreach}
		</ul>
	</div>
	<div role="tabpanel" class="tab-pane fade in active" id="settings">
		<div class="demo-settings">
			<p>CẤU HÌNH CHUNG</p>
			<ul class="setting-list">
				<li>
					<span>Thời tiết</span>
					<div class="switch">
						<label><input type="checkbox"{if $admin_config.weather} checked="checked"{/if} onchange="shop.admin.updateAdminConfig('weather', this.checked ? 1 : 0)"><span class="lever"></span></label>
					</div>
				</li>
				<li>
					<span>Tỉ giá ngoại tệ</span>
					<div class="switch">
						<label><input type="checkbox"{if $admin_config.money} checked="checked"{/if} onchange="shop.admin.updateAdminConfig('money', this.checked ? 1 : 0)"><span class="lever"></span></label>
					</div>
				</li>
			</ul>
			{if $is_root}
			<p>CẤU HÌNH HỆ THỐNG</p>
			<ul class="setting-list">
				<li>
					<span>Chế độ Debug</span>
					<div class="switch">
						<label><input type="checkbox"{if $debug_mode} checked="checked"{/if} onchange="shop.rootPanel.onOffMode('debug', this.checked ? 1 : 0)"><span class="lever"></span></label>
					</div>
				</li>
				<li>
					<span>Sửa Layout</span>
					<div class="switch">
						<label><input type="checkbox"{if $edit_mode} checked="checked"{/if} onchange="shop.rootPanel.onOffMode('editMode', this.checked ? 1 : 0)"><span class="lever"></span></label>
					</div>
				</li>
				{*<li{if !$debug_mode} style="display:none"{/if}>
					<button type="button" class="btn bg-deep-orange waves-effect" onclick="shop.viewDebug()"><i style="font-size:16px" class="material-icons">visibility</i> XEM DEBUG</button>
				</li>*}
			</ul>
			{/if}
		</div>
	</div>
</div>