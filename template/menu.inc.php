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
          <li><a href="<?php echo Config::get('web_path'); ?>/new.php">New</a></li>
          <li><a href="<?php echo Config::get('web_path'); ?>/view.php">View</a></li>
          <li><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=export&type=csv">CVS</a></li>
<?php if ($GLOBALS['user']->access == '100') { ?>
          <li class="dropdown">
            <a href="<?php echo Config::get('web_path'); ?>/admin.php?action=manage" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/admin.php?action=show_users">Users</a></li>
              <li class="divider"></li>
              <li class="nav-header">Settings</li>
              <li><a href="#">Classification</a></li>
              <li><a href="#">Materials</a></li>
            </ul>
          </li>
<?php } ?>
        </ul>
            <form method="post" action="<?php echo Config::get('web_path'); ?>/view.php?action=search" class="navbar-form pull-right">
              <select name="field" class="span2">
                <option value="item">Item</option>
                <option value="station_index">RN</option>
                <option value="notes">Notes</option> 
                <option value="feature">Feature</option> 
                <option value="unit">Unit</option>
                <option value="weight">Weight</option>
                <option value="height">Height</option>
                <option value="width">Width</option>
                <option value="thickness">Thickness</option>
                <option value="quanity">Quanity</option>
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
