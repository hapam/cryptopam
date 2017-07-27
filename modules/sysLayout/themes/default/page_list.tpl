{if $data.html_extra_head}{$data.html_extra_head}{/if}
<div class="row clearfix">
{if $data.html_search}
	{$data.html_search}
{else}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			{if $data.html_search_header}
				{$data.html_search_header}
			{else}
			<div class="header">
				<h2>{if $data.html_search_label}{$data.html_search_label}{else}
					TÌM KIẾM
					<small>Bộ lọc tìm kiếm dữ liệu</small>{/if}
				</h2>
			</div>{/if}
			<div class="body table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr class="info">
							{foreach from=$formSearch item=entry}
							<th width="{$searchWidth}%">{$entry.title}</th>
							{/foreach}
						</tr>
					</thead>
					<tbody>
						<tr>
							{foreach from=$formSearch item=entry}
							<td>
								{foreach from=$entry.items item=item}
									{$item}
								{/foreach}
							</td>
							{/foreach}
						</tr>
					</tbody>
				</table>
				<div align="right">
					{if $data.html_search_button}
						{$data.html_search_button}
					{else}
					<button type="submit" class="btn bg-purple btn-circle-lg waves-effect waves-circle waves-light waves-float">
						<i class="material-icons">search</i>
					</button>{/if}
				</div>
			</div>
		</div>
	</div>
{/if}
</div>

{$msg}
<div class="row clearfix">
{if $data.html_view}
	{$data.html_view}
{else}
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			{if $data.html_view_header}
				{$data.html_view_header}
			{else}
			<div class="header">
				{if $data.html_view_buttons}
					<div class="pull-right">{$data.html_view_buttons}</div>
				{else}
					{if ($per.add && $link.add) || ($formConf.del && $per.del)}<div class="pull-right">
						{if $per.add && $link.add}
							<a class="btn btn-success btn-circle-lg waves-effect waves-circle waves-light waves-float" href="{$link.add}" title="Thêm mới">
								<i class="material-icons">add</i></a>{/if}
						{if $formConf.del && $per.del}
							<a class="btn bg-red btn-circle-lg waves-effect waves-circle waves-light waves-float m-l-20" href="javascript:void(0)" onclick="if (confirm('Bạn muốn xóa ?')) shop.admin.system.postMethod('{$formConf.name}')" title="Xóa nhiều">
								<i class="material-icons">clear</i></a>{/if}
					</div>{/if}
				{/if}
				<h2>
					{if $data.html_view_label}{$data.html_view_label}{else}KẾT QUẢ TÌM KIẾM{/if}
					{if $data.pagging}<small><b>{$data.pagging.total_item}</b> bản ghi / <b>{$data.pagging.total_page}</b> trang</small>{/if}
				</h2>
			</div>{/if}
			<div class="body table-responsive">
				{if $data.html_view_table}
					{$data.html_view_table}
				{else}
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr class="info">
							{foreach from=$formView item=entry}{if $entry.per}
							<th{foreach from=$entry.head item=e key=k} {$k}="{$e}"{/foreach}>
								{if $entry.type == 'del'}
									{if $per.del && $formConf.del}
										<input type="checkbox" id="checkall_ctrl{$formConf.id}" class="filled-in chk-col-green checkall_ctrl{$formConf.id}" onclick="shop.checkbox.selectAll('checkall{$formConf.id}', 'checkall_ctrl{$formConf.id}');" />
										<label for="checkall_ctrl{$formConf.id}"></label>
									{/if}
								{else}
									{$entry.title}
								{/if}
							</th>
							{/if}{/foreach}
						</tr>
					</thead>
					<tbody>
						{if $data.html_view_table_content}
							{$data.html_view_table_content}
						{else}
						{foreach from=$data.items item=item name=i}
						<tr>
							{foreach from=$formView item=entry}{if $entry.per}
							<td{foreach from=$entry.ext item=e key=k} {$k}="{$e}"{/foreach}>
								{if $entry.type == 'del'}
									{if $item[$entry.id].hide}
									{elseif $formConf.del}
										<input name="{if $entry.name}{$entry.name}{else}selected_ids{/if}[]" type="checkbox" value="{$item.id}" onclick="shop.checkbox.select('checkall{$formConf.id}', 'checkall_ctrl{$formConf.id}', this);" class="checkall{$formConf.id} filled-in chk-col-green" id="checker-{$item.id}" />
										<label for="checker-{$item.id}"></label>
									{/if}
								{elseif $entry.type == 'index'}
									{$data.pagging.start_page+$smarty.foreach.i.iteration}
								{elseif $entry.type == 'icon'}
									<div class="table-icon">
										{if $entry.only}
											{if $item[$entry.id].hide}
											{elseif $item[$entry.id]}
											<a href="{if $item[$entry.id].link}{$item[$entry.id].link}{else}javascript:void(0){/if}"{if $item[$entry.id].des} data-toggle="tooltip" data-placement="right" data-original-title="{$item[$entry.id].des}"{/if}>
												<i class="material-icons"{if $item[$entry.id].color} style="color:{$item[$entry.id].color}"{/if}>{$item[$entry.id].icon}</i></a>{/if}
										{else}
											{if $entry.id == 'btn-del'}
												{if $item[$entry.id].hide}
												{elseif $link.del}<a href="{$link.del}?id={$item.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" data-toggle="tooltip" data-placement="right" data-original-title="Xóa">
													<i class="material-icons">{if $entry.icon}{$entry.icon}{else}delete{/if}</i></a>{/if}
											{elseif $entry.id == 'btn-edit'}
												{if $item[$entry.id].hide}
												{elseif $link.edit}<a href="{$link.edit}?id={$item.id}" data-toggle="tooltip" data-placement="right" data-original-title="Sửa">
													<i class="material-icons">{if $entry.icon}{$entry.icon}{else}edit{/if}</i></a>{/if}
											{else}
												{if $item[$entry.id]}
													<a href="{$item[$entry.id]}" data-toggle="tooltip" data-placement="right" data-original-title="{$entry.title}">
														<i class="material-icons">{$entry.icon}</i></a>{/if}
											{/if}
										{/if}
									</div>
								{elseif $entry.html != ''}
									{$entry.html}
								{else}
									{$item[$entry.id]}
								{/if}
							</td>
							{/if}{/foreach}
						</tr>
						{/foreach}{/if}
					</tbody>
				</table>{/if}
				{if $data.pagging}<p>Tổng cộng: <b>{$data.pagging.total_item}</b> bản ghi / <b>{$data.pagging.total_page}</b> trang</p>{/if}
			</div>
			{if $data.pagging}<div class="row" align="center">{$data.pagging.pager}</div>{/if}
			{if $data.html_extra_view}{$data.html_extra_view}{/if}
		</div>
	</div>
{/if}
</div>
{if $data.html_extra_foot}{$data.html_extra_foot}{/if}