<div id="confirmdel_<?php echo scrub_out($krotovina->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h3 id="myModalLabel">Delete Krotovina</h3>
  </div>
  <div class="modal-body">
    <?php $has_records = $krotovina->has_records(); ?>
    <p>
      <ul><li> Has Records: <?php echo \UI\boolean_word($has_records); ?></li></ul>
    <?php if ($has_records) { ?>
      Unable to delete krotovina, there are still records associated with it. You must re-assign all of the
      <a href="<?php echo Config::get('web_path'); ?>/record/search/krotovina/<?php echo scrub_out($krotovina->catalog_id); ?>">Records</a>
      before deleting this Krotovina.
    <?php } else { ?>
      No records found for this Krotovina. All images associated with this level will be removed when the krotovina is deleted.
    <?php } ?>
  </div>
  <div class="modal-footer">
    <form method="post" action="<?php echo Config::get('web_path'); ?>/krotovina/delete">
    <?php if (!$has_records) { ?>
    <button type="submit" class="btn btn-danger">Delete</a>
    <input type="hidden" name="krotovina_id" value="<?php echo scrub_out($krotovina->uid); ?>" />
    <?php } ?>
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </form>
  </div>
</div>
