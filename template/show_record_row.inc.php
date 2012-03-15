<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<tr>
	<td>
		<span class="action" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/new.php?action=edit&record_id=<?php echo scrub_out($record->uid); ?>'" />
		<img alt="Edit" title="Edit" src="<?php echo Config::get('web_path'); ?>/images/icons/application_form_edit.png" />
		</span>
	</td>
	<td class="rn"><?php echo scrub_out($record->station_index); ?></td>
	<td class="item"><?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></td>
	<td class="unit"><?php echo scrub_out($record->unit); ?></td>
	<td><?php echo scrub_out(quad::$values[$record->quad]); ?></td>
	<td><?php echo scrub_out($record->level); ?></td>
	<td><?php echo scrub_out($record->feature); ?></td>
	<td class="lu"><?php echo scrub_out(lsgunit::$values[$record->lsg_unit]); ?></td>
	<td><?php echo scrub_out($record->material->name); ?></td>
	<td class="class"><?php echo scrub_out($record->classification->name); ?></td>
	<td class="date" ><?php echo scrub_out(date("m/d/y",$record->created)); ?></td>
	<td class="notes" title="<?php echo addslashes(scrub_out($record->notes)); ?>"><?php echo scrub_out(substr($record->notes,0,20)); ?></td>
	<td><?php echo scrub_out($record->user->username); ?></td>
		<?php if ($GLOBALS['user']->access >= 100) { ?>
	<td>
		<span class="action" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/admin.php?action=confirm_delete&record_id=<?php echo scrub_out($record->uid); ?>';" />
		<img alt="Delete" title="Delete" src="<?php echo Config::get('web_path'); ?>/images/icons/delete.png" /></span>
	</td>
		<?php } ?>

</tr>

<!--
<tr class="divider">
</tr>
<tr>
        <th>Item</th>
        <th>Unit</th>
        <th>Level</th>
        <th>Litho Unit</th>
</tr>
<tr>
	<td><?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></td>
	<td><?php echo scrub_out($record->unit); ?></td>
	<td><?php echo scrub_out($record->level); ?></td>
	<td><?php echo scrub_out($record->lsg_unit); ?></td>
</tr>
<tr>
        <th>Station</th>
        <th>XRF Matrix</th>
        <th>Weight</th>
        <th>Quanity</th>
</tr>
<tr>
	<td><?php echo scrub_out($record->station_index); ?></td>
	<td><?php echo scrub_out($record->xrf_matrix_index); ?></td>
	<td><?php echo scrub_out($record->weight); ?>g</td>
	<td><?php echo scrub_out($record->quanity); ?></td>
</tr>
<tr>
	<th>Width</th>
	<th>Length</th>
	<th>Thickness</th>
        <th>XRF Artifact Index</th>
</tr>
<tr>
	<td><?php echo scrub_out($record->width); ?></td>
	<td><?php echo scrub_out($record->height); ?></td>
	<td><?php echo scrub_out($record->thickness); ?></td>
	<td><?php echo scrub_out($record->xrf_artifact_index); ?></td>
</tr>
<tr>
        <th coltd="2">Material</th>
        <th coltd="2">Classification</th>
</tr>
<tr>
	<td coltd="2"><?php echo scrub_out($record->material->name); ?></td>
	<td coltd="2"><?php echo scrub_out($record->classification->name); ?></td>
</tr>
<tr>
        <th coltd="4">Notes</th>
</tr>
<tr>
	<td coltd="4"><?php echo scrub_out($record->notes); ?></td>
</tr>
-->

