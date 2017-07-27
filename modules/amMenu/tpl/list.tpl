{$msg}

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<div class="pull-right">
					<a class="btn btn-success btn-circle-lg waves-effect waves-circle waves-float" href="{$addUrl}" title="Thêm mới">
						<i class="material-icons">add</i></a>
				</div>
				<h2>
					HỆ THỐNG MENU
					<small>Được lưu trữ trong cơ sở dữ liệu</small>
				</h2>
			</div>
			<div class="body table-responsive">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr class="info">
							<th width="300">Tiêu đề</th>
							<th>URL</th>
							<th>Quyền hạn</th>
							<th width="80">Sắp xếp</th>
							<th width="50">Sửa</th>
							<th width="50">Xóa</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$all_menu item=menu name=k}
						<tr class="success">
							<td colspan="6" style="text-transform:uppercase;padding-left:10px"><b>{$menu.t}</b></td>
						</tr>
						{foreach from=$menu.items item=entry name=i}
							<tr>
								<td>
									{if $entry.icon != ''}<i class="material-icons">{$entry.icon}</i>{/if}
									<a href="{$entry.link}" style="text-transform:uppercase;padding-left:10px" target="_blank">
										<b>{$entry.title}</b></a>
								</td>
								<td><a href="{$entry.link}" target="_blank">{$entry.link}</a></td>
								<td align="center">{if $entry.per}{$entry.per}{else}---{/if}</td>
								<td align="center"><b>{$entry.weight}</b></td>
								<td align="center">
									<a href="{$editURL}?id={$entry.id}" data-toggle="tooltip" data-placement="top" data-original-title="Sửa">
										<i class="material-icons">edit</i></a>
								</td>
								<td align="center">
									<a href="{$delURL}?id={$entry.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" data-toggle="tooltip" data-placement="top" data-original-title="Xóa">
										<i class="material-icons">delete</i></a>
								</td>
							</tr>
							{foreach from=$entry.items item=item}
								<tr>
									<td style="padding-left:50px">
										{if $item.icon != ''}<i class="material-icons">{$item.icon}</i>{/if}
										<a href="{$item.link}" target="_blank">{$item.title}</a>
									</td>
									<td><a href="{$item.link}" target="_blank">{$item.link}</a></td>
									<td align="center">{if $item.per}{$item.per}{else}---{/if}</td>
									<td align="center">{$item.weight}</td>
									<td align="center">
										<div class="table-icon">
											<a href="{$editURL}?id={$item.id}" data-toggle="tooltip" data-placement="top" data-original-title="Sửa">
												<i class="material-icons">edit</i></a>
										</div>
									</td>
									<td align="center">
										<div class="table-icon">
											<a href="{$delURL}?id={$item.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" data-toggle="tooltip" data-placement="top" data-original-title="Xóa">
												<i class="material-icons">delete</i></a>
										</div>
									</td>
								</tr>
							{/foreach}
						{/foreach}
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>