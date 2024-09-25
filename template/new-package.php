<div class="wrap container-fluid" style="margin-bottom:10px;">
    <h2><?php echo !empty($context['action']) && $context['action'] == "edit_package" ? "Edit Package" : "Add new Package"; ?></h2>
</div>
<?php
    $aPackageDetails = isset($context['packageDetails']) && !empty($context['packageDetails']) ? current($context['packageDetails']) : [];
    $aAction = isset($context['action']) && !empty($context['action']) ? $context['action'] : 'add_new_package';
?>
<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
    <input name="action" value="<?php echo $aAction; ?>" type="hidden" />
    <input name="package_id" value="<?php echo isset($_REQUEST['package']) ? $_REQUEST['package'] : ''; ?>" type="hidden" />
    <div class="container-fluid">
        <div class="row">
            <div class="col col-md-12">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="group_name">Package Name</label>
                        <input class="form-control" required="true" type="text" name="name" value="<?php echo isset($aPackageDetails['name']) ? $aPackageDetails['name'] : ""; ?>" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="prod_slug">Package Slug</label>
                        <input class="form-control" required="true" type="text" name="slug" value="<?php echo isset($aPackageDetails['slug']) ? $aPackageDetails['slug'] : ""; ?>" /> 
                    </div>
                    <div class="form-group col-md-6">
                        <label for="prod_slug">Package Type</label>
                        <input class="form-control" required="true" type="text" name="type" value="<?php echo isset($aPackageDetails['type']) ? $aPackageDetails['type'] : ""; ?>" /> 
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Package Description</span>
                            </div>
                            <textarea class="form-control" name="description" aria-label="Group Description"><?php echo isset($aPackageDetails['description']) ? $aPackageDetails['description'] : ""; ?></textarea>
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