<!-- User Info -->
<div class="user-info">
	<div class="image">
		<img src="{$admin_user.image}" width="48" height="48" alt="avatar" />
	</div>
	<div class="info-container">
		<div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"{if $admin_user.login_as == 1} title="Đang đăng nhập dưới tên = {$admin_user.name} | Account gốc = {$admin_user.login_user}"{/if}>
			{if $admin_user.login_as == 1}Login as {/if}<b>{$admin_user.name}</b>
		</div>
		<div class="email">{$admin_user.email}</div>
		<div class="btn-group user-helper-dropdown">
			<i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
			<ul class="dropdown-menu pull-right">
				{if $admin_user.login_as != 1}
				<li><a href="javascript:void(0);" onclick="shop.admin.user.changePassword()"><i class="material-icons">person</i>Đổi mật khẩu</a></li>
				<li role="seperator" class="divider"></li>{/if}
				{if $admin_user.login_as == 1}
				<li><a href="javascript:void(0);" onclick="shop.delLoginAs()"><i class="material-icons">replay</i>Account gốc</a></li>
				<li role="seperator" class="divider"></li>{/if}
				<li><a href="{$admin_user.logout}"><i class="material-icons">input</i>Sign Out</a></li>
			</ul>
		</div>
	</div>
</div>
<!-- #User Info -->

<!-- Menu -->
<div class="menu">
	<ul class="list">
		<li class="header">MAIN NAVIGATION</li>
		{foreach from=$admin_menu item=entry name=i key=k}
			<li{if $entry.active} class="active"{/if}>
				<a href="{$entry.link}" class="menu-toggle">
					<i class="material-icons">{$entry.icon}</i>
					<span>{$entry.title}</span>
				</a>
				{if $entry.sub}<ul class="ml-menu">
					{foreach from=$entry.sub item=e name=i}
					<li{if $e.active} class="active"{/if}>
						<a href="{$e.link}"{if $e.new_page} target=_blank"{/if}>{$e.title}</a>
					</li>
					{/foreach}
				</ul>{/if}
			</li>
		{/foreach}
	</ul>
</div>
<!-- #Menu -->
<!-- Footer -->
<div class="legal">
	<div class="copyright">
		&copy; 2017 <a href="javascript:void(0);">Pam</a> - Theme by <a href="javascript:void(0);">AdminBSB</a> v1.0.4
	</div>
	<div class="version">
		<b>Version: </b> 4.0.1
	</div>
</div>
<!-- #Footer -->