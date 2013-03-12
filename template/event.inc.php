<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="alert<?php echo scrub_out($size); ?><?php echo scrub_out($css_class); ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $header; ?>
  <?php echo scrub_out($message); ?>
</div>
