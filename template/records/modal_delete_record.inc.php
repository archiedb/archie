 <?php
 // vim: set softtabstop=2 ts=2 sw=2 expandtab: 
 if (INIT_LOADED != '1') { exit; }
 ?>
<div id="confirmdel_<?php echo scrub_out($record->uid); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Confirm Delete Request</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <?php echo $record->site->name . '-' . $record->catalog_id; ?></p>
      </div>
      <div class="modal-footer">
      <form method="post" action="<?php echo Config::get('web_path'); ?>/records/delete">
        <button type="submit" class="btn btn-danger">Delete</a>
        <input type="hidden" name="record_id" value="<?php echo scrub_out($record->uid); ?>" />
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
