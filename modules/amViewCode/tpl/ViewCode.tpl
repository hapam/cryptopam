{if $arrFolder}
	<table class="table" style="margin:-21px 0 0">
		{foreach from=$arrFolder item=folder}
			<tr>
				<td width="50">
					<i class="material-icons">folder_open</i>
				</td>
				<td><a class="vcdir" href="{$urlViewCode}?cd={$folder.folder}">{$folder.name}</a></td>
			</tr>
		{/foreach}
	</table>
{/if}

{if $arrFile}
	<table class="table" style="margin:-{if $arrFolder}1{else}21{/if}px 0 0">
		{foreach from=$arrFile item=folder}
			<tr>
				<td width="50">
					<i class="material-icons">
						{if $folder.ext == "png" || $folder.ext == "jpg" || $folder.ext == "gif"}image
						{elseif $folder.ext == "rar" || $folder.ext == "zip"}storage
						{elseif $folder.ext == "css"}format_paint
						{elseif $folder.ext == "js"}flag
						{elseif $folder.ext == "htm"}web
						{elseif $folder.ext == "php"}library_books
						{else}filter_none{/if}
					</i>
				</td>
				<td><a title="{$folder.last_modified}" href="{$urlViewCode}?f={$folder.file}">{$folder.name}</a></td>
			</tr>
		{/foreach}
	</table>
{/if}

{if $fileData}
	<div>
		<b>File:</b> <span style="color:blue">{$fileData.file}</span> |
		<b>Last Modified:</b> <span style="color:red">{$fileData.last_modified}</span> |
		<a href="{$urlViewCode}?dl&f={$fileData.file}" style="color:green"><b>DOWNLOAD</b></a>
	</div>
	<div>
		<pre class="prettyprint linenums" style="margin:20px 0;background:#000;color:#fff;padding:20px;color:#fff;font-size:16px;border:0">{$fileData.content|htmlspecialchars}</pre>
	</div>
{else}
	{if !$arrFile && !$arrFolder}
		KHÔNG CÓ FILE HAY THƯ MỤC NÀO CẢ
	{/if}
{/if}