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
        <?php if ($user->disabled) { ?>
				<li><a href="#confirm_enable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Enable</a></li>
        <?php } else { ?>
				<li><a href="#confirm_disable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Disable</a></li>
        <?php } ?>
			</ul>
		</div>
      <?php 
        if ($user->disabled) { 
          require \UI\template('/users/modal_enable'); 
        } else { 
          require \UI\template('/users/modal_disable'); 
        }
      ?>
  </td>
</tr>
