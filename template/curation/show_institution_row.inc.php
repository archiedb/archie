<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td><?php echo \UI\htmlout($institution->name); ?></td>
	<td><?php echo \UI\htmlout($institution->contact); ?></td>
	<td><?php echo \UI\htmlout($institution->status); ?></td>
  <td>
    <div class="btn-group pull-right">
      <a type="button" class="btn btn-sm btn-primary">Edit</a>
    </div>
  </td>
</tr> 
