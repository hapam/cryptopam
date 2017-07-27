<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr class="info">
			{foreach from=$formView item=entry}{if $entry.per}
			<th{foreach from=$entry.head item=e key=k} {$k}="{$e}"{/foreach}>
				{if $entry.id == 'btn-del-check'}
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
							{if $item[$entry.id]}
							<a href="{if $item[$entry.id].link}{$item[$entry.id].link}{else}javascript:void(0){/if}"{if $item[$entry.id].des} data-toggle="tooltip" data-placement="right" data-original-title="{$item[$entry.id].des}"{/if}>
								<i class="material-icons"{if $item[$entry.id].color} style="color:{$item[$entry.id].color}"{/if}>{$item[$entry.id].icon}</i></a>{/if}
						{else}
							{if $entry.id == 'btn-del'}
								{if $item[$entry.id].hide}
								{else}<a href="{$link.del}?id={$item.id}" onclick="return confirm('Bạn có chắc chắn muốn xoá không!')" data-toggle="tooltip" data-placement="right" data-original-title="Xóa">
									<i class="material-icons">delete</i></a>{/if}
							{elseif $entry.id == 'btn-edit'}
								{if $item[$entry.id].hide}
								{else}<a href="{$link.edit}?id={$item.id}" data-toggle="tooltip" data-placement="right" data-original-title="Sửa">
									<i class="material-icons">edit</i></a>{/if}
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
</table>
{if $data.pagging}<p>Tổng cộng: <b>{$data.pagging.total_item}</b> bản ghi / <b>{$data.pagging.total_page}</b> trang</p>{/if}
{if $data.pagging}<div class="row" align="center">{$data.pagging.pager}</div>{/if}