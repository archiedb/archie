<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<fieldset class="record"><legend><?php echo $record->site . '-' . $record->catalog_id; ?> - <?php echo $GLOBALS['user']->username; ?> - <?php echo date("r",$record->created); ?></legend>

<table class="record_view" cellspacing="0" cellpadding="4">
<tr>
<th>UNIT</th><td><?php echo scrub_out($record->unit); ?></em></td>
<th>CATALOG ID</th><td><?php echo scrub_out($record->site . '-' . $record->catalog_id); ?></td>
</tr>
<tr>
<th>LEVEL</th><td><?php echo scrub_out($record->level); ?></td>
<th>L. U.</th><td><?php echo scrub_out(lsgunit::$values[$record->lsg_unit]); ?></td>
</tr>
<tr>
<th>MATRIX XRF #</th><td><?php echo scrub_out($record->xrf_matrix_index); ?></td>
<th>RN</th><td><?php echo scrub_out($record->station_index); ?></td>
</tr>
<tr>
<th>QUAD</th><td><?php echo scrub_out(quad::$values[$record->quad]); ?></td>
<th>FEATURE</th><td><?php echo scrub_out($record->feature); ?></td>
</tr>
<tr>
<th>WEIGHT (GRAMS)</th><td><?php echo scrub_out($record->weight); ?></td>
<th>THICKNESS(mm)</th><td><?php echo scrub_out($record->thickness); ?></td>
</tr>
<tr>
<th>LENGTH(mm)</th><td><?php echo scrub_out($record->height); ?></td>
<th>WIDTH(mm)</th><td><?php echo scrub_out($record->width); ?></td>
<tr>
<th>QUANTITY</th><td><?php echo scrub_out($record->quanity); ?></td>
<th>MATERIAL</th><td><?php echo scrub_out($record->material->name); ?></td>
</tr>
<tr>
<th>CLASSIFICATION</th><td><?php echo scrub_out($record->classification->name); ?></td>
<th>ARTIFACT XRF #</th><td><?php echo scrub_out($record->xrf_artifact_index); ?></td>
</tr>
<tr>
<th valign="top">NOTES</th><td colspan="3"><?php echo scrub_out($record->notes); ?></td>
</tr>
</table>
<fieldset class="attachment">
<legend>Images</legend>
<?php
        $images = $record->get_images();
        foreach ($images as $image) {
        $i++;
?>

<div class="image-block">
        <a target="_blank" href="<?php echo Config::get('web_path'); ?>/image.php?content_id=<?php echo scrub_out($image['uid']); ?>">
        <img src="<?php echo Config::get('web_path'); ?>/image.php?content_id=<?php echo scrub_out($image['uid']);?>&thumb=true" alt="Image <?php echo $i; ?>" />
        </a>
</div>
<?php } ?>
<?php Error::display('upload'); ?>
</fieldset>

<input type="button" value="Edit This Record" onclick="parent.location.href='<?php echo Config::get('web_path'); ?>/new.php?action=edit&amp;record_id=<?php echo intval($record->uid); ?>';" />
</fieldset> 

