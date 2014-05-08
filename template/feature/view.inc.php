<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/feature/edit/<?php echo scrub_out($feature->uid); ?>" class="btn">Edit Feature</a>
</p>
<h3><?php echo $feature->site->name . ' F-' . $feature->catalog_id; ?>
  <small>Entered by <?php echo $feature->user->username; ?> on <?php echo date("d-M-Y H:i:s",$feature->created); ?></small>
</h3>
<?php Event::display(); ?>
<?php Event::display('errors'); ?>
<table class="table table-hover table-bordered table-white">
<tr>
  <th>Keywords</th><td><?php echo scrub_out($feature->keywords); ?></em></td>
  <th>User</th><td><?php echo scrub_out($feature->user->name); ?></td>
</tr>
</table>
