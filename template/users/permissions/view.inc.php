<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a class="btn" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php echo scrub_out($user->uid); ?>">Edit</a>
  <a class="btn" href="<?php echo Config::get('web_path'); ?>/users/permissions/view/<?php echo scrub_out($user->uid); ?>">Permissions</a>
  <?php if (Access::has('user','delete',$user->uid) AND !$user->disabled) { ?>
  <a class="btn btn-danger" href="#confirm_disable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Disable</a>
  <?php require \UI\template('/users/modal_disable'); ?>
  <?php } ?>
  <?php if (Access::has('user','delete',$user->uid) AND $user->disabled) { ?>
  <a class="btn btn-success" href="#confirm_enable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Enable</a>
  <?php require \UI\template('/users/modal_enable'); ?>
  <?php } ?>
</p>
<div class="page-header">
<h4>
  Effective permissions for <?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>) @ <?php echo \UI\sess::$user->site->name; ?> 
  <small><?php echo scrub_out($user->email); ?></small>
</h4>
</div>
<?php Event::display(); ?>
<table class="table table-bordered table-hover">
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
  <td><?php echo $role; ?></td>
  <td><?php echo $access; ?></td>
  <td>Description of what this really means here</td>
</tr>
<?php } } ?>
</tbody>
</table>
<div class="page-header">
<p class="pull-right">
  <a href="#add_group" role="button" data-toggle="modal" class="btn btn-success">Add Group</a>
</p>
<h4>
  Assigned Groups 
</h4>
</div>
<?php include \UI\template('/users/permissions/modal_add_group'); ?>
