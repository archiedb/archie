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
  <?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>)
  <small><?php echo scrub_out($user->email); ?></small>
</h4>
<em>Currently working on <?php $user->site->_print('name'); ?></em>
</div>
<?php Event::display(); ?>
<table class="table table-bordered table-hover">
<tbody>
<tr>
  <td><strong>Total Records Entered</strong></td>
  <td><?php echo Stats::total_records('user',$user->uid); ?></td>
</tr>
<tr>
  <td><strong>Favorite Classification</strong></td>
  <td>
    <?php 
      $info = Stats::classification_records('user',$user->uid); 
      echo $info['classification'] . ' (' . $info['count'] . ')';   
    ?>  
  </td>
</tr>
<tr>
  <td><strong>Favorite Material</strong></td>
  <td>
    <?php
      $info = Stats::material_records('user',$user->uid);
      echo $info['material'] . ' (' . $info['count'] . ')';
    ?>
  </td>
</tr>
</tbody>
</table>
</fieldset>
