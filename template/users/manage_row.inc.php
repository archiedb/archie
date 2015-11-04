<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
    <a href="<?php echo Config::get('web_path'); ?>/users/view/<?php $user->_print('uid'); ?>"><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)</a>
  </td>
  <td><?php echo scrub_out($user->email); ?></td>
	<td><?php $date = ($user->last_login > 0) ? date('m-d-Y H:s',$user->last_login) : 'Never';echo $date; ?></td>
  <td>
		<div class="btn-group pull-right">
      <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php $user->_print('uid'); ?>">Edit</a>
			<a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
			<ul class="dropdown-menu">
        <?php if ($user->disabled) { ?>
				<li><a href="#confirm_enable_user_<?php $user->_print('uid'); ?>" role="button" data-toggle="modal">Enable</a></li>
        <?php } else { ?>
				<li><a href="#confirm_disable_user_<?php $user->_print('uid'); ?>" role="button" data-toggle="modal">Disable</a></li>
        <?php } ?>
        <li><a href="<?php echo Config::get('web_path'); ?>/users/permissions/view/<?php echo $user->uid; ?>">Permissions</a></li>
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
