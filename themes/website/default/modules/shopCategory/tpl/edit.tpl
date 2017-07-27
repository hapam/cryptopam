<div class="contentTop">
    <span class="pageTitle">{$title_module}</span>
    <div class="clear"></div>
</div>

<div class="breadLine">
    <div class="bc">
        {$breadcum}
    </div>
</div>

<div class="wrapper">
    <div class="mTop10">{$msg}</div>

    <div class="widget fluid">
        <div class="whead"><h6>Thuộc tính</h6><div class="clear"></div></div>

        <div class="formRow">
            <div class="grid2"><label>Tên danh mục</label></div>
            <div class="grid9">
                <input name="title" type="text" id=title value="{$title}" size="40" />
                <input type="hidden" name="id" value="{$id}" />
            </div>
            <div class="clear"></div>
        </div>

        <div class="formRow">
            <div class="grid2"><label>Loại danh mục</label></div>
            <div class="grid9 noSearch">
                <select name="type" id="type" onchange="shop.admin.category.loadParentCat(this.value)" class="select" style="width:120px">{$type}</select>
            </div>
            <div class="clear"></div>
        </div>

        <div class="formRow">
            <div class="grid2"><label>Danh mục cha</label></div>
            <div class="grid9">
                <select name="parent_id" id="parent_id">{$parent_id}</select>
            </div>
            <div class="clear"></div>
        </div>

        <div class="formRow">
            <div class="grid2"><label>Sắp xếp</label></div>
            <div class="grid2 onlyNums">
                <input name="weight" type="text" id="weight" value="{$weight}" maxlength="8" />
            </div>
            <div class="clear"></div>
        </div>

        <div class="formRow">
            <div class="grid2"><label>Trạng thái</label></div>
            <div class="grid9 noSearch">
                <select name="status" class="select" style="width:70px">{$status}</select>
            </div>
            <div class="clear"></div>
        </div>

        <div class="formRow">
            <div class="grid2"><label>Đánh dấu đặc biệt</label></div>
            <div class="grid2 onlyNums">
                <input type="checkbox" value="1" name="active" id="active"{if $active} checked="checked"{/if} />
            </div>
            <div class="clear"></div>
        </div>
        <div class="formRow">
            <div class="grid2"><label>Hình ảnh<em>( Cỡ ảnh 300x355px)</em></label></div>
            <div class="grid9 noSearch">
                <input type="file" name="path" size="60" />
                <input type="hidden" name="old_file" value="{$path}" /><br/><br/>
                {if $image}<img src="{$image}" width="50" class="fl mLeft10" />{/if}
            </div>
            <div class="clear"></div>
        </div> 
    </div>

    <div class="mTop20">
        <input type="button" value="Lưu thay đổi" class="buttonS bGreen" onclick="shop.admin.category.onSubmit(document.EditCategoryForm);" />
        <input type="button" value="Hủy bỏ" class="buttonS bRed mLeft10" onclick="history.go(-1)" />
    </div>
</div>