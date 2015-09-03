<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php $user->_print('uid'); ?>">Edit</a>
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/users/permissions/view/<?php $user->_print('uid'); ?>">Permissions</a>
  <?php if (Access::has('user','delete',$user->uid) AND !$user->disabled) { ?>
  <button type="button" class="btn btn-danger" data-target="#confirm_disable_user_<?php $user->_print('uid'); ?>" data-toggle="modal">Disable</button>
  <?php require \UI\template('/users/modal_disable'); ?>
  <?php } ?>
  <?php if (Access::has('user','delete',$user->uid) AND $user->disabled) { ?>
  <button type="button" class="btn btn-success" data-target="#confirm_enable_user_<?php $user->_print('uid'); ?>" data-toggle="modal">Enable</button>
  <?php require \UI\template('/users/modal_enable'); ?>
  <?php } ?>
</p>
<h4>
  Effective permissions for <?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>) @ <?php echo \UI\sess::$user->site->name; ?> 
  <small><?php echo scrub_out($user->email); ?></small>
</h4>
</div>
<?php Event::display(); ?>
<table class="table table-hover">
<tbody>
<tr>
  <th><strong>Role</strong></th>
  <th><strong>Access</strong></th>
  <th><strong>Description</strong></th>
</tr>
<?php if (!count($user->roles)) { ?>
<tr><td colspan="3">No Permissions</td></tr>
<?php } else { ?>
<?php foreach ($user->roles as $role=>$access) { ?>
<tr>
  <td><?php echo ucfirst($role); ?></td>
  <td>
    <?php 
    $output = null;
    foreach ($access as $action=>$true) { 
      $output .= ucfirst($action) . ',';
    }
    echo rtrim($output,',');
    ?>
  </td>
  <td>&nbsp;</td>
</tr>
<?php } } ?>
</tbody>
</table>
<div class="page-header">
<p class="pull-right">
  <button type="button" data-target="#add_group" data-toggle="modal" class="btn btn-success">Add Group</button>
</p>
<h4>
  Assigned Groups 
</h4>
</div>
<table class="table table-hover">
<tbody>
<tr>
  <th><strong>Name</strong></th>
  <th><strong>Description</strong></th>
  <th><strong>&nbsp;</strong></th>
</tr>
<?php
  $groups = $user->get_groups();
  foreach ($groups as $group) { 
?>
<tr>
  <td><?php echo scrub_out($group->name); ?></td>
  <td><?php echo scrub_out($group->description); ?></td>
  <td>
    <div class="pull-right">
      <button type="button" data-target="#del_group<?php $group->_print('uid'); ?>" data-toggle="modal" class="btn btn-danger">Remove</button>
      <?php include \UI\template('/users/permissions/modal_del_group'); ?>
    </div>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php include \UI\template('/users/permissions/modal_add_group'); ?>
