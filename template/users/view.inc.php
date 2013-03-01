<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="text-right">
  <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/users/edit/<?php echo scrub_out($user->uid); ?>">Edit</a>
  <?php if (Access::has('user','delete',$user->uid) AND !$user->disabled) { ?>
  <a class="btn btn-danger" href="#confirmdisable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Disable</a>
  <div id="confirmdisable_user_<?php echo scrub_out($user->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Confirm Disable User</h3>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to disable the user <?php echo scrub_out($user->username); ?>. They will be unable to login.</p>
    </div>
    <div class="modal-footer">
    <form method="post" action="<?php echo Config::get('web_path'); ?>/users/disable">
      <button type="submit" class="btn btn-danger">Disable</a>
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>" />
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
  <?php } ?>
  <?php if (Access::has('user','delete',$user->uid) AND $user->disabled) { ?>
  <a class="btn btn-success" href="#confirmenable_user_<?php echo scrub_out($user->uid); ?>" role="button" data-toggle="modal">Enable</a>
  <div id="confirmenable_user_<?php echo scrub_out($user->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="myModalLabel">Confirm Enable User</h3>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to enable <?php echo scrub_out($user->username); ?>?</p>
    </div>
    <div class="modal-footer">
    <form method="post" action="<?php echo Config::get('web_path'); ?>/users/enable">
      <button type="submit" class="btn btn-success">Enable</a>
      <input type="hidden" name="uid" value="<?php echo scrub_out($user->uid); ?>" />
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    </form>
    </div>
  </div>
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
