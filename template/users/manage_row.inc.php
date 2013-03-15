<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
    <a href="<?php echo Config::get('web_path'); ?>/users/view/<?php echo scrub_out($user->uid); ?>"><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</a>
  </td>
  <td><?php echo scrub_out($user->email); ?></td>
	<td><?php echo scrub_out(Access::get_level_name($user->access)); ?></td>
  <td>
		<div class="btn-group">
      <a class="btn" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php echo scrub_out($user->uid); ?>">Edit</a>
			<a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">Disable</a></li>
			</ul>
		</div>
  </td>
</tr>
