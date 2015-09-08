<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab:
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirmdel_<?php echo scrub_out($level->uid); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h3 id="myModalLabel">Delete Level <?php $level->_print('catalog_id'); ?></h3>
      </div>
      <div class="modal-body">
        <?php $has_records = $level->has_records(); ?>
        <p>
          <div class="row">
              <h4 class="text-center">Has Records: <?php echo \UI\boolean_word($has_records); ?></h4>
          </div>
        <?php if ($has_records) { ?>
          Unable to delete <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo $level->uid; ?>">level</a>, there are still records associated with it. You must re-assign all of the
          <a href="<?php echo Config::get('web_path'); ?>/records/search/level/<?php echo scrub_out($level->uid); ?>">Records</a>
          before deleting this Level.
        <?php } else { ?>
          No records found for this Level. All images associated with this level will be removed when the level is deleted.
        <?php } ?>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/level/delete">
          <?php if (!$has_records) { ?>
          <button type="submit" class="btn btn-danger">Delete</a>
          <input type="hidden" name="level_id" value="<?php echo scrub_out($level->uid); ?>" />
          <?php } ?>
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
