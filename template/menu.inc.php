<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="<?php echo Config::get('web_path'); ?>/">Archie</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li><a href="<?php echo Config::get('web_path'); ?>/records/new">New</a></li>
          <li class="divider-vertical"></li>
          <li><a href="<?php echo Config::get('web_path'); ?>/records">View</a></li>
          <li class="divider-vertical"></li>
          <li><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=export&type=csv">CSV</a></li>
          <li class="divider-vertical"></li>
<?php if (Access::has('admin','read')) { ?>
          <li class="dropdown">
            <a href="<?php echo Config::get('web_path'); ?>/admin.php?action=manage" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/users/manage">Users</a></li>
              <li class="divider"></li>
              <li class="nav-header">Settings</li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/tools">System Tools</a>
            </ul>
          </li>
          <li class="divider-vertical"></li>
<?php } ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Profile <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/users/view/<?php echo scrub_out(\UI\sess::$user->uid); ?>">My Account</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/logout">Logout</a></li>
            </ul>
          </li>
        </ul>
            <form method="post" action="<?php echo Config::get('web_path'); ?>/records/search" class="navbar-form pull-right">
              <select name="field" class="span2">
              <?php $name = scrub_in($_POST['field']) . '_active'; ${$name}=' selected="selected"'; ?>
                <option value="catalog_id"<?php echo $catalog_id_active; ?>>Catalog #</option>
                <option value="item"<?php echo $item_active; ?>>Item</option>
                <option value="station_index"<?php echo $station_index_active; ?>>RN</option>
                <option value="notes"<?php echo $notes_active; ?>>Notes</option> 
                <option value="feature"<?php echo $feature_active; ?>>Feature</option> 
                <option value="unit"<?php echo $unit_active; ?>>Unit</option>
                <option value="weight"<?php echo $weight_active; ?>>Weight</option>
                <option value="height"<?php echo $height_active; ?>>Height</option>
                <option value="width"<?php echo $width_active; ?>>Width</option>
                <option value="thickness"<?php echo $thickness_active; ?>>Thickness</option>
                <option value="quanity"<?php echo $quanity_active; ?>>Quanity</option>
                <option value="quad"<?php echo $quad_active; ?>>Quad</option>
              </select>
              <input name="value" class="span2" type="text" placeholder="Value..." value="<?php echo scrub_out($_POST['value']); ?>">
              <button type="submit" class="btn">Search</button>
            </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<!-- end Nav bar --> 
<div class="container">
