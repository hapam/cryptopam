<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{$base_url}">{$site_name}</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                {foreach from=$menu item=entry}
                    <li{if $entry.active} class="active"{/if}><a href="{$entry.link}">{$entry.title}</a></li>
                {/foreach}
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{$config}">Cấu hình</a></li>
                <li><a href="{$logout}">Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</nav>