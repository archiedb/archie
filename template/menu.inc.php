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
<?php if (Access::has('admin','read')) { ?>
          <li><a href="<?php echo Config::get('web_path'); ?>/reports">Reports</a></li>
          <li class="divider-vertical"></li>
          <li class="dropdown">
            <a href="<?php echo Config::get('web_path'); ?>/admin.php?action=manage" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/users/manage">Users</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/material">Materials</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/classification">Classifications</a></li>
              <li class="divider"></li>
              <li class="nav-header">System</li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/status">Status</a>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/tools">Tools</a>
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
              <?php 
                foreach (View::get_allowed_filters('record') as $filter) { 
                  $selected = ''; 
                  if (isset($_POST['field'])) { 
                    $selected = ($_POST['field'] == $filter) ? ' selected="selected"' : '';
                  }
              ?>
                <option value="<?php echo scrub_out($filter); ?>"<?php echo $selected; ?>><?php echo \UI\field_name($filter); ?></option>
              <?php } ?>
              </select>
              <?php $search_value = isset($_POST['value']) ? scrub_out($_POST['value']) : ''; ?>
              <input name="value" class="span2" type="text" placeholder="Value..." value="<?php echo $search_value; ?>">
              <button type="submit" class="btn">Search</button>
            </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<!-- end Nav bar --> 
<div class="container">
