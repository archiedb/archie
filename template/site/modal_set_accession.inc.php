<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="set_accession_<?php echo $site->_print('uid'); ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Set <?php echo $site->_print('name'); ?> Accession</h3>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/setaccession">
        <em>Set the current Accession # for <?php echo $site->_print('name'); ?>. This will overwrite any existing accession set on this site. 
        Site accessions start and end dates will be kept for historical reference.</em>
        <hr />
        <p>
        <strong>Accession #</strong> <input class="form-control" type="text" name="accession" />
        </p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Set Accession</a>
        <input type="hidden" name="uid" value="<?php echo $site->_print('uid'); ?>">
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
