<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/curation/view/<?php echo scrub_out($curation->uid); ?>">
      <?php echo \UI\htmlout($curation->curation); ?></a>
  </td>
  <td><?php echo \UI\htmlout($curation->institution->name); ?></td>
	<td><?php echo \UI\htmlout($curation->building->name); ?></td>
	<td><?php echo \UI\htmlout($curation->room->name); ?></td>
	<td><?php echo \UI\htmlout($curation->cabinet->name); ?></td>
	<td><?php echo \UI\htmlout($curation->drawer->name); ?></td>
	<td><?php echo \UI\htmlout($curation->status); ?></td>
  <td>
    <div class="btn-group pull-right">
      <a type="button" class="btn btn-sm btn-primary">View</a>
      <?php if ($curation->is_on_loan() { ?>
        <a type="button" class="btn btn-sm btn-primary">Contact Lender</a>
      <?php } ?>
    </div>
  </td>
</tr> 
