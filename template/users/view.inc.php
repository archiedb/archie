<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="text-right">
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php echo scrub_out($user->uid); ?>">Edit</a>
  <?php if (Access::has('user','delete',$user->uid) AND !$user->disabled) { ?>
  <a class="btn btn-danger" href="#confirm_disable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Disable</a>
  <?php require \UI\template('/users/modal_disable'); ?>
  <?php } ?>
  <?php if (Access::has('user','delete',$user->uid) AND $user->disabled) { ?>
  <a class="btn btn-success" href="#confirm_enable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Enable</a>
  <?php require \UI\template('/users/modal_enable'); ?>
  <?php } ?>
</p>
<div class="content-block">
<fieldset>
<legend><?php echo scrub_out($user->name); ?> (<?php echo scrub_out($user->username); ?>) - <?php echo scrub_out($user->email); ?></legend>
<table class="table table-white">
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
</tbody>
</table>
</div><!-- End content block -->
