<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirmdel_<?php $krotovina->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h3 id="myModalLabel">Delete Krotovina</h3>
      </div>
      <div class="modal-body">
        <?php $has_records = $krotovina->has_records(); ?>
        <ul><li> Has Records: <?php echo \UI\boolean_word($has_records); ?></li></ul>
        <?php if ($has_records) { ?>
          Unable to delete krotovina, there are still records associated with it. You must re-assign all of the
          <a href="<?php echo Config::get('web_path'); ?>/record/search/krotovina/<?php $krotovina->_print('catalog_id'); ?>">Records</a>
          before deleting this Krotovina.
        <?php } else { ?>
          No records found for this Krotovina. All images associated with this level will be removed when the krotovina is deleted.
        <?php } ?>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/delete">
        <?php if (!$has_records) { ?>
        <button type="submit" class="btn btn-danger">Delete</a>
        <input type="hidden" name="krotovina_id" value="<?php $krotovina->_print('uid'); ?>" />
        <?php } ?>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </form>
      </div>
    </div>
  </div>
</div>
