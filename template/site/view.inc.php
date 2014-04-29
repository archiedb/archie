<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="pull-left">
<h4>Sites</h4>
</div>
<p class="pull-right text-right">
  <a class="btn btn-success" href="<?php echo Config::get('web_path'); ?>/manage/site/add">Add Site</a>
</p>
<table class="table table-bordered table-hover">
<thead>
<tr>
  <th>Name</th>
  <th>Description</th>
  <th>P.I.</th>
  <th>Enabled</th>
  <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php foreach ($sites as $site) { require \UI\template('/site/view_row'); } ?>
</tbody>
</table>
</div><!-- End table container --> 
