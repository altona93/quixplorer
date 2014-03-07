{include file="header.tpl"}
<FORM name="selform" method="POST" action='.'>
{include file="toolbar.tpl"}
<HR>
<INPUT type="hidden" name="action">
<INPUT type="hidden" name="first" value="y">
<INPUT type="hidden" name="srt" value="{$srt}">
<INPUT type="hidden" name="order" value="{$order}">
<INPUT type="hidden" name="lang" value="{$lang}">
<TABLE cellspacing=0 cellpadding=0 class='files' WIDTH="95%">
	<tr>
	<th WIDTH="2%" class="header">
		<INPUT TYPE="checkbox" name="toggleAllC" onclick="ToggleAll(this);">
	</th>
	{foreach from=$columns item=column}
	<th WIDTH="44%" class="header" id='{$column.id}'>
	<A href="{$url}?action=list?dir={$dir}&order={$column.id}&srt={if $srt != 'yes'}yes{/if}">{$column.name}
	{if $order == $column.id}
		<img src='{$themedir}/images/sort_{if $srt == 'yes'}down{else}up{/if}.png' />
	{/if}
	</a>
	</th>
	{/foreach}
	</tr>
	{foreach from=$files item=file}
	{if $file->type == "dir"}
	<tr class="rowdata" id="dirmarker">
		<td id='name' colspan=7 nowrap>
		{$file->type}
		{if $file->link != ''}
			<a href="{$file->link}">
		{/if}
		{$file->name}
		{if $file->link != ''}
			</a>
		{/if}
		</td>
	</tr>
	{else}
	<TR class="rowdata">
		<TD><INPUT TYPE="checkbox" name="selitems[]" value="{$file->name}"
			onclick="javascript:Toggle(this);">
		</TD>
		<TD id='name' nowrap>
        <i class="icon-{$file->type}"/>
		{if $file->link != ''}
			<A HREF="{$file->link}">
		{/if}
		{$file->name}
		{if $file->link != ''}
			</a>
		{/if}
		</TD>
		<TD id='size' >{$file->size}</TD>
		<TD id='type' >{$file->type}</TD>
		<TD id='modified' >{$file->modified}</td>
		<TD id='permissions'>
		{if $file->permissions.link != ''}
			<A HREF="{$file->permissions.link}">{$file->permissions.text}</a>
		{else}
			{$file->permissions.text}
		{/if}
		</TD>
		<TD id='edit'>
            {if $file->name neq ".."}{button link=$file->download_link content="<i class=\"icon-download\"></i>&nbsp;" title='download' enabled=($file->download_link neq '')}{/if}
            {if $file->edit_link neq ''}{button link=$file->edit_link content="<i class=\"icon-edit\"></i>" title='edit' enabled=1}{/if}
		</TD>
	</TR>
	{/if}
	{/foreach}
</table>
</form>
<hr>
<div class='sum_info'>
	{$totals->file_count} files, {$totals->directory_count} directories, disk usage: {$totals->file_space}
</div>
{include file="footer.tpl"}

