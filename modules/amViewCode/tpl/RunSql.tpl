{if $items}
<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr class="info">
			{foreach from=$listcolumn item=cl name=i}
				<th>{$cl}</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		{foreach from=$items item=item name=i}
			<tr {$hover}>
				{foreach from=$listcolumn item=cl_build name=i_build}
					<td>{$item[$cl_build]}</td>
				{/foreach}
			</tr>
		{/foreach}
	</tbody>
</table>
{/if}
