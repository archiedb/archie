<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
    <?php echo scrub_out($group->name); ?>
  </td>
	<td><?php echo scrub_out($group->description); ?></td>
  <td>
		<div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/manage/group/roles/<?php echo scrub_out($group->uid); ?>">Modify Roles</a>
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/manage/group/edit/<?php echo scrub_out($group->uid); ?>">Edit</a></li>
        <li><a href="<?php echo Config::get('web_path'); ?>/manage/group/confirmdelete/<?php echo scrub_out($group->uid); ?>">Delete</a></li>
      </ul>
		</div>
  </td>
</tr>
