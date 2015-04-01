<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
$sites = $user->get_sites();
?>
<?php require_once 'template/menu.inc.php'; ?>
<div class="page-header">
<h3>You are currently working on <strong><?php echo scrub_out($user->site->name); ?></strong></h3>
</div>
<?php Event::display(); ?>
<h4>You have access to the following Sites</h4>
<table class="table table-bordered table-hover">
<tbody>
<tr>
  <th><strong>Site Name</strong></th>
  <th><strong>Description</strong></th>
  <th><strong>P.I.</strong></th>
  <th>&nbsp;</th>
</tr>
<?php if (!count($sites)) { ?>
<tr><td colspan="4">You do not have access to any sites</td></tr>
<?php } else { ?>
<?php foreach ($sites as $site) { ?>
<tr>
  <td><?php echo scrub_out($site->name); ?></td>
  <td><?php echo scrub_out($site->description); ?></td>
  <td><?php echo scrub_out($site->principal_investigator); ?></td>
  <td>
    <?php if ($user->site->uid != $site->uid) { ?>
    <a class="btn btn-success" href="<?php echo Config::get('web_path'); ?>/users/siteupdate/<?php echo $site->uid; ?>">Activate</a>
    <?php } else { ?>
    <a class="btn btn-danger disabled" href="#">Active Site</a>
    <?php } ?>
  </td>
</tr>
<?php } } ?>
</tbody>
</table>
