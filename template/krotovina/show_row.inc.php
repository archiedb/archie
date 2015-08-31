<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/krotovina/view/<?php $krotovina->_print('uid'); ?>">
      <?php $krotovina->_print('record'); ?></a>
  </td>
	<td><?php $krotovina->_print('keywords'); ?></td>
	<td><?php $krotovina->_print('description'); ?></td>
  <td>
    <div class="btn-group pull-right">
      <a class="btn btn-info" href="<?php echo Config::get('web_path'); ?>/records/search/krotovina/<?php $krotovina->_print('uid'); ?>">Records</a></li>
      <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo Config::get('web_path'); ?>/krotovina/edit/<?php $krotovina->_print('uid'); ?>">Edit</a>
        <?php if (Access::has('krotovina','delete')) { ?>
        <li><a href="#confirmdel_<?php $krotovina->_print('uid'); ?>" role="button" data-toggle="modal">Delete</a></li>
        <?php } ?>
      </ul>
    </div>
    <?php 
      if (Access::has('krotovina','delete')) {
        include \UI\template('/krotovina/modal_delete_confirm');
      }
    ?>
  </td>
</tr> 
