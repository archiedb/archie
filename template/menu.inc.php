<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<nav class="navbar-fixed-top navbar navbar-inverse navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo Config::get('web_path'); ?>/">Archie</a>
      <!-- END BRAND -->
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">New <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/records/new">Record</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/feature/new">Feature</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/krotovina/new">Krotovina</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/level/new">Level</a></li>
            </ul>
          </li>
          <li class="divider-vertical"></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/records">Record</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/feature">Feature</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/krotovina">Krotovina</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/level">Level</a></li>
            </ul>
<?php if (Access::has('report')) { ?>
          <li><a href="<?php echo Config::get('web_path'); ?>/reports">Report</a></li>
<?php } ?>
<?php if (Access::has('manage') OR Access::has('user')) { ?>
          <li class="divider-vertical"></li>
          <li class="dropdown">
            <a href="<?php echo Config::get('web_path'); ?>/admin.php?action=manage" class="dropdown-toggle" data-toggle="dropdown">Manage <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/users/manage">Users</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/group">Groups</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/material">Materials</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/classification">Classifications</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/site">Sites</a>
              <li role="separator" class="divider"></li>
              <li class="dropdown-header">System</li>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/import">Import</a>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/tools">Tools</a>
              <li><a href="<?php echo Config::get('web_path'); ?>/manage/status">Status</a>
            </ul>
          </li>
          <li class="divider-vertical"></li>
<?php } ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Profile <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo Config::get('web_path'); ?>/users/view/<?php echo scrub_out(\UI\sess::$user->uid); ?>">My Account</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/users/site">Change Site</a></li>
              <li><a href="<?php echo Config::get('web_path'); ?>/logout">Logout</a></li>
            </ul>
          </li>
        </ul>
            <form method="post" action="<?php echo Config::get('web_path'); ?>/records/search" class="navbar-form navbar-right" role="search">
              <div class="form-group">
                <select name="field" class="form-control">
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
                <input name="value" class="form-control" type="text" placeholder="Record Value..." value="<?php echo $search_value; ?>">
              </div>
              <button type="submit" class="btn btn-inverse"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </nav>
<!-- end Nav bar --> 
<div class="container">
