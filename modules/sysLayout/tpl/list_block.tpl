<table border="0" cellpadding="3" bgcolor="#FFFFFF" >
    <tr valign="top">
        <td>
            <div class="body">
                <div class="main">
                    <fieldset>
						<legend><b>{$name}</b></legend>
						<div class="p10">
							<table cellpadding="5" cellspacing="0">
								<tr valign="top">
									<td valign="top" bgcolor="#FFFFFF">
										<table width="100%" border="0" cellpadding="3" cellspacing="0">
											{foreach from=$items item=items}
											<tr valign="top" {$hover}>
												<td align="left" valign="top" nowrap width="10">
													[ <strong>{$items.name}</strong> {if $items.themes != '' || $items.themes_mobile != ''} - <font color="red">{$items.themes_mobile}{$items.themes}</font>{/if} ]
												</td>
												<td align="left" valign="top" nowrap>
													<a href="?q=edit_page&cmd=delete_block&block_id={$items.id}&id={$id}&mobile={$mobile}">
														<img src="css/images/delete.png" width="16" height="16" border="0" /></a>
													{if $name != 'undefined_regions'}
														&nbsp;&nbsp;&nbsp;&nbsp;{$items.move_up}
														&nbsp;&nbsp;&nbsp;&nbsp;{$items.move_down}
														&nbsp;&nbsp;&nbsp;&nbsp;{$items.move_top}
														&nbsp;&nbsp;&nbsp;&nbsp;{$items.move_bottom}
													{/if}
												</td>
											</tr>
											{/foreach}
										</table>
									</td>
								</tr>
								<tr>
									<td nowrap>
										{if $name != 'undefined_regions'}
											<a href="{$moduleLink}?page_id={$id}&region={$name}&mobile={$mobile}" title="Thêm modules vào [ {$name} ]">Thêm Module</a>
										{else}
											<a href="{$delFromUndefined}" title="Xóa toàn bộ module thừa">Xóa tất cả</a>
										{/if}
									</td>
								</tr>
							</table>
							<div class="c"></div>
						</div>
                    </fieldset>
            	</div>
            </div>
        </td>
    </tr>
</table>
