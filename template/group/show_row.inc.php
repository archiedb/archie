<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
    <?php $group->_print('name'); ?>
  </td>
	<td><?php $group->_print('description'); ?></td>
  <td>
		<div class="pull-right btn-group">
      <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/manage/group/roles/<?php $group->_print('uid'); ?>">Modify Roles</a>
      <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/manage/group/edit/<?php $group->_print('uid'); ?>">Edit</a></li>
        <li><a href="#delete_group_<?php $group->_print('uid'); ?>" role="button" data-toggle="modal">Delete</a></li>
      </ul>
		</div>
    <?php include \UI\template('/group/modal_delete_group'); ?>
  </td>
</tr>
