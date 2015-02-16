<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$group->set_roles();
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<p class="pull-right">
  <a href="#add_role" role="button" data-toggle="modal" class="btn btn-success">Add Role</a>
</p>
<?php include \UI\template('/group/modal_add_role'); ?>
<h4>
  Effective Access for <em><?php echo scrub_out($group->name); ?></em> group
</h4>
</div>
<?php Event::display('errors'); ?>
<table class="table table-bordered table-hover">
<tbody>
<tr>
  <th><strong>Role</strong></th>
  <th><strong>Description</strong></th>
  <th><strong>&nbsp;</strong></th>
</tr>
<?php if (!count($group->roles)) { ?>
<tr><td colspan="3">No Permissions</td></tr>
<?php } else { ?>
<?php foreach ($group->roles as $access) { 
  $role = $access['role'];
  $action = $access['action'];
?>
<tr>
  <td><?php echo scrub_out($role->description); ?></td>
  <td><?php echo scrub_out($action->description); ?></td>
  <td><a href="<?php echo Config::get('web_path'); ?>/manage/group/deleterole/<?php echo $access['uid']; ?>/<?php echo $group->uid; ?>" class="btn btn-small btn-danger">Remove</a></td>
</tr>
<?php } } ?>
</tbody>
</table>

