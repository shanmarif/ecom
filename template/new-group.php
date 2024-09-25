<div class="wrap container-fluid" style="margin-bottom:10px;">
    <h2><?php echo !empty($context['action']) && $context['action'] == "edit_product" ? "Edit Group" : "Add new Group"; ?></h2>
</div>
<?php
    $aGroupDetails = isset($context['groupDetails']) && !empty($context['groupDetails']) ? current($context['groupDetails']) : [];
    $aAction = isset($context['action']) && !empty($context['action']) ? $context['action'] : 'add_new_group';
?>
<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
    <input name="action" value="<?php echo $aAction; ?>" type="hidden" />
    <input name="group_id" value="<?php echo isset($_REQUEST['group']) ? $_REQUEST['group'] : ''; ?>" type="hidden" />
    <input name="parent_id" value="0" type="hidden" />
    <input name="group_level" value="1" type="hidden" />
    <input name="image" value="-" type="hidden" />
    <div class="container-fluid">
        <div class="row">
            <div class="col col-md-12">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="group_name">Group Name</label>
                        <input class="form-control" required="true" type="text" name="name" value="<?php echo isset($aGroupDetails['name']) ? $aGroupDetails['name'] : ""; ?>" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="prod_slug">Group Slug</label>
                        <input class="form-control" required="true" type="text" name="slug" value="<?php echo isset($aGroupDetails['slug']) ? $aGroupDetails['slug'] : ""; ?>" /> 
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Group Description</span>
                            </div>
                            <textarea class="form-control" name="description" aria-label="Group Description"><?php echo isset($aGroupDetails['description']) ? $aGroupDetails['description'] : ""; ?></textarea>
                        </div>
                    </div>
                    <div class="col col-md-12">
                        <input type="submit" class="btn btn-primary" name="submit" value="Save">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>