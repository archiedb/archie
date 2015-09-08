<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
if (!isset($message)) { $message=''; }
?>
<div class="alert<?php echo scrub_out($size); ?><?php echo scrub_out($css_class); ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <div class="row">
    <div class="col-md-12"><?php echo $header; ?></div>
  </div><div class="row">
    <div class="col-md-12"><?php echo $message; ?></div>
  </div>
</div>
