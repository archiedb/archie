<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td><a href="<?php echo Config::get('web_path'); ?>/manage/site/view/<?php echo scrub_out($site->uid); ?>"><?php echo scrub_out($site->name); ?></a></td>
  <td><?php echo scrub_out($site->description); ?></td>
  <td><?php echo scrub_out($site->principal_investigator); ?></td>
  <td><?php echo scrub_out($site->accession); ?></td>
  <td><?php echo scrub_out($site->project); ?></td>
	<td class="text-center"><?php echo \UI\boolean_word($site->enabled); ?></td>
  <td>
		<div class="btn-group pull-right">
      <a class="btn btn-primary" href="<?php echo Config::get('web_path'); ?>/manage/site/edit/<?php echo scrub_out($site->uid); ?>">Edit</a>
      <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="#set_project_<?php echo $site->uid; ?>" role="button" data-toggle="modal">Set Project</a></li>
        <li><a href="#set_accession_<?php echo $site->uid; ?>" role="button" data-toggle="modal">Set Accession</a></li>
      </ul>
		</div>
      <?php include \UI\template('/site/modal_set_project'); ?>
      <?php include \UI\template('/site/modal_set_accession'); ?>
  </td>
</tr>
