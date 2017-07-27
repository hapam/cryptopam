{if $formItem.per}
{if $formItem.type == 'hidden'}
	<input type="hidden" name="{$formItem.id}" id="{$formItem.id}" value="{$formItem.value}"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach} />
{elseif $formItem.type == 'html'}
	{$formItem.html}
{elseif $formItem.type == 'button'}
<div class="m-t-10 m-b-30" id="{$formItem.id}-parent">
	<button type="button" class="btn {$formItem.color}{if $formItem.style==1} btn-circle{if $formItem.size>0}-{$formItem.size_tit}{/if}{elseif $formItem.size>0} btn-{$formItem.size_tit}{/if} waves-effect waves-light{if $formItem.style == 1} waves-circle waves-light waves-float{/if}"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach}>
{if $formItem.icon}<i class="material-icons"{if $formItem.title} style="font-size:16px"{/if}>{$formItem.icon}</i>{/if}{if $formItem.title} {$formItem.title}{/if}</button>
</div>
{else}
<div class="clearfix" id="{$formItem.id}-parent">
	{if $formItem.title}<h2 class="card-inside-title">{$formItem.title}</h2>{/if}
	{if $formItem.type == 'select'}
		<div class="input-group">
			<select name="{$formItem.id}" id="{$formItem.id}"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach} class="form-control show-tick" data-live-search="true">{$formItem.options}</select>
			<div style="line-height:7px;height:7px">&nbsp;</div>
		</div>
	{elseif $formItem.type == 'textarea'}
		{if $formItem.editor}
		<div class="m-b-30">
			<textarea name="{$formItem.id}" id="{$formItem.id}" {foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach} data-editor="ckeditor"{if $formItem.width} data-width="{$formItem.width}"{/if}{if $formItem.height} data-height="{$formItem.height}"{/if}>{$formItem.value}</textarea>
			{if $formItem.image}
			<div class="m-t-20">
				<div class="pull-left m-t-5"><label>Chèn nhiều ảnh</label></div>
				<div class="pull-left imgContainer m-l-20">
					<div id="queue"></div>
					<input id="uploadify" name="uploadify" type="file" multiple="true" />
				</div>
				<div class="clearfix"></div>
			</div>{/if}
		</div>
		{else}
			<div class="input-group">
				<div {if $formItem.line}class="form-line"{/if}>
					<textarea name="{$formItem.id}" id="{$formItem.id}" class="form-control no-resize"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach}>{$formItem.value}</textarea>
				</div>
				{if $formItem.caption}<div class="help-info">{$formItem.caption}</div>{/if}
			</div>
		{/if}
	{elseif $formItem.type == 'file'}
	<div class="input-group">
		<div {if $formItem.line}class="form-line"{/if}>
			<input type="file" name="{$formItem.id}" id="{$formItem.id}" class="inputfile inputfile-1" data-multiple-caption="[count] files selected"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach} />
			<label for="{$formItem.id}">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg>
				<span>Chọn file&hellip;</span>
			</label>
			{if $formItem.old}
				<input type="hidden" name="{$formItem.old.id}" value="{$formItem.old.value}" />
				{if $formItem.old.src}<div class="m-t-5 pull-right">
					<img src="{$formItem.old.src}" alt="{$formItem.old.src}"{foreach from=$formItem.old.ext item=entry key=k} {$k}="{$entry}"{/foreach} /></div>
				<div class="clearfix m-t-10">&nbsp;</div>{/if}
			{/if}
		</div>
	</div>
	{elseif $formItem.type == 'checkbox'}
		<div class="input-group">
			<div {if $formItem.line}class="form-line"{/if}>
				{if $formItem.style == 'onoff'}
					{if $formItem.label}
						<div class="pull-{if $formItem.label_pos == 'left'}left{else}right{/if}"><label for="{$formItem.id}" class="card-inside-title">{$formItem.label}</label></div>
						<div class="switch{if $formItem.label_pos == 'left'} pull-right{/if}">
							<label><input type="checkbox" name="{$formItem.id}" id="{$formItem.id}"{if $formItem.checked} checked="checked"{/if} value="{$formItem.value}"{foreach from=$formItem.ext item=e key=i} {$i}="{$e}"{/foreach} /><span class="lever"></span></label></div>
						<div class="clearfix"></div>
					{else}
						<div class="switch">
							<label>OFF<input type="checkbox" name="{$formItem.id}" id="{$formItem.id}"{if $formItem.checked} checked="checked"{/if} value="{$formItem.value}"{foreach from=$formItem.ext item=e key=i} {$i}="{$e}"{/foreach} /><span class="lever"></span>ON</label></div>
					{/if}
					<div>&nbsp;</div>
				{else}
					<input type="checkbox" name="{$formItem.id}" id="{$formItem.id}"{if $formItem.checked} checked="checked"{/if} value="{$formItem.value}" class="filled-in chk-col-blue"{foreach from=$formItem.ext item=e key=i} {$i}="{$e}"{/foreach} />
					<label for="{$formItem.id}">{$formItem.label}</label>
				{/if}
			</div>
			{if $formItem.caption}<div class="help-info">{$formItem.caption}</div>{/if}
		</div>
	{elseif $formItem.type == 'checkbox-group'}
		<div class="input-group">
			<div {if $formItem.line}class="form-line"{/if}>
				{foreach from=$formItem.options item=entry key=k}
					{insert name=inarray assign=check i=$k a=$formItem.value}
					<div class="m-t-5">
						<input type="checkbox" name="{$formItem.id}[]" id="{$formItem.id}-{$k}"{if $check} checked="checked"{/if} value="{$k}" class="filled-in chk-col-blue" />
						&nbsp;<label for="{$formItem.id}-{$k}">{$entry}</label>
					</div>
				{/foreach}
			</div>
		</div>
	{elseif $formItem.type == 'radio'}
		<div class="input-group">
			<div {if $formItem.line}class="form-line"{/if}>
				<input type="radio" name="{$formItem.id}" id="{$formItem.id}"{if $formItem.checked} checked="checked"{/if} value="{$formItem.value}" class="with-gap radio-col-blue" />
				<label for="{$formItem.id}"></label>
			</div>
			{if $formItem.caption}<div class="help-info">{$formItem.caption}</div>{/if}
		</div>
	{elseif $formItem.type == 'radio-group'}
		<div class="input-group">
			<div {if $formItem.line}class="form-line"{/if}>
			{if $formItem.style == 1}
				{foreach from=$formItem.options item=entry key=k}
				<div class="m-t-10">
					<input type="radio" name="{$formItem.id}" id="{$formItem.id}-{$k}"{if $formItem.value == $k} checked="checked"{/if} value="{$k}" class="with-gap radio-col-blue"{foreach from=$formItem.ext[$k] item=e key=i} {$i}="{$e}"{/foreach} />
					&nbsp;<label for="{$formItem.id}-{$k}">{$entry}</label>
				</div>
				{/foreach}
			{else}
				{foreach from=$formItem.options item=entry key=k}
					<input type="radio" name="{$formItem.id}" id="{$formItem.id}-{$k}"{if $formItem.value == $k} checked="checked"{/if} value="{$k}" class="with-gap radio-col-blue"{foreach from=$formItem.ext[$k] item=e key=i} {$i}="{$e}"{/foreach} />
					&nbsp;<label for="{$formItem.id}-{$k}">{$entry}</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				{/foreach}{/if}
			</div>
			{if $formItem.caption}<div class="help-info">{$formItem.caption}</div>{/if}
		</div>
	{else}
		<div class="input-group">
			<div {if $formItem.line}class="form-line"{/if}>
				<input type="{$formItem.type}" name="{$formItem.id}" id="{$formItem.id}" value="{$formItem.value}"{foreach from=$formItem.ext item=entry key=k} {$k}="{$entry}"{/foreach} class="form-control{if $formItem.time} form-date-picker{/if}"{if $formItem.required} required{/if} />
			</div>
			{if $formItem.caption}<div class="help-info">{$formItem.caption}</div>{/if}
		</div>
	{/if}
</div>{/if}{/if}