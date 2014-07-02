<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirmdel_<?php echo scrub_out($feature->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/feature/delete">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Delete Feature</h3>
  </div>
  <div class="modal-body">
    <?php $has_records = $feature->has_records(); ?>
    <ul>
      <li> Has Records: <?php echo \UI\boolean_word($has_records); ?></li>
    </ul>
    <?php if ($has_records) { ?>
      Unable to delete feature, there are still records associated with it. You must re-assign all of the 
      <a href="<?php echo Config::get('web_path'); ?>/record/search/feature/<?php echo scrub_out($feature->catalog_id); ?>">Records</a>
      before deleting the Feature. 
    <?php } else { ?>
      No records found for this Feature. All spatial points associated with this feature will be removed when the feature is deleted.
    <?php } ?>
  </div>
  <div class="modal-footer">
    <?php if (!$has_records) { ?>
    <button type="submit" class="btn btn-danger">Delete Feature</a>
    <input type="hidden" name="feature_id" value="<?php echo scrub_out($feature->uid); ?>">
    <?php } ?>
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
  </form>
</div>
