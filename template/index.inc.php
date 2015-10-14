<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<table class="table table-striped">
<thead>
  <tr>
    <th>Total records</th>
    <th>Records entered today</th>
    <th>Last Record Entered</th>
    <th>Last Classification</th>
    <th>Todays most common classification</th>
  </tr>
</thead>
<tbody>
<tr>
  <td><?php echo Stats::total_records(); ?></td>
  <td><?php echo Stats::total_records('today'); ?></td>
  <td>
  <?php 
    $record = Record::last_created(); 
    if (!$record->uid) { 
      echo "<strong class=\"text-error\">None</strong>";
    }
    else { 
      echo \UI\record_link($record->uid,'record',$record->record); 
    }
  ?>
  </td>
  <td>
    <?php
      if (!$record->uid) {
        echo "<strong class=\"text-error\">None</strong>";
      }
      else {
        echo $record->classification->name;
      }
    ?>
  <td>
  <?php 
      $info = Stats::classification_records('today'); 
      if ($info['count'] > 0) { 
        echo $info['classification'] . ' with ' . $info['count'] . ' record(s) entered'; 
      }
      else { 
        echo "<strong class=\"text-error\">No Data</strong>";
      }
  ?>
  </td>
</tr>
</tbody>
</table>
<!-- Records -->
<?php if (Access::has('record','read')) { ?>
<div class="row">
  <div>
  <?php if (Access::has('record','create')) { ?>
    <p class="pull-right">
        <a class="btn btn-small btn-success" href="<?php echo Config::get('web_path'); ?>/records/new">New Record</a>
    </p>
  <?php } ?>
    <strong>Your last five records</strong>
  </div>
</div>
<div class="well">
<div class="row">
  <div class="col-sm-2"><strong>Catalog #</strong></div>
  <div class="col-sm-2"><strong>Material</strong></div>
  <div class="col-sm-2"><strong>Classification</strong></div>
  <div class="col-sm-1"><strong>Level</strong></div>
  <div class="col-sm-1"><strong>Feat/Krot</strong></div>
</div>
<?php 
  $records = Record::get_user_last('5');
  foreach ($records as $uid) {
    $record = new Record($uid);
?>
<div class="row">
  <div class="col-sm-2">
    <a href="<?php echo Config::get('web_path'); ?>/records/view/<?php echo scrub_out($record->uid); ?>"><?php echo scrub_out($record->record); ?></a>
  </div>
  <div class="col-sm-2">
    <?php echo scrub_out($record->material->name); ?>
  </div>
  <div class="col-sm-2">
    <?php echo scrub_out($record->classification->name); ?>
  </div>
  <div class="col-sm-1">
    <?php echo \UI\record_link($record->level->uid,'level',$record->level->record); ?>
  </div>
  <div class="col-sm-1">
    <?php 
    if ($record->feature->uid) { 
      echo \UI\record_link($record->feature->uid,'feature',$record->feature->record);
    }
    elseif ($record->krotovina->uid) {
      echo \UI\record_link($record->krotovina->uid,'krotovina',$record->krotovina->record);
    }
    ?>
  </div>
</div>
<?php } ?>
</div>
<?php } ?>
<!-- Levels -->
<?php if (Access::has('level','read')) { ?>
<div class="row">
  <div>
    <?php if (Access::has('level','create')) { ?>
    <p class="pull-right">
        <a class="btn btn-small btn-success" href="<?php echo Config::get('web_path'); ?>/level/new">New Level</a>
    </p>
    <?php } ?>
    <strong>Your Open Levels</strong>
  </div>
</div>
<div class="well">
<div class="row">
  <div class="col-sm-1"><strong>Unit</strong></div>
  <div class="col-sm-1"><strong>Quad</strong></div>
  <div class="col-sm-2"><strong>Level</strong></div>
  <div class="col-sm-1"><strong>L.U.</strong></div>
  <div class="col-sm-2"><strong>Closed</strong></div>
</div>
<?php 
  $levels = Level::get_open_user_levels();
  foreach ($levels as $uid) {
    $level = new Level($uid);
?>
<div class="row">
  <div class="col-sm-1">
    <?php echo scrub_out($level->unit); ?>
  </div>
  <div class="col-sm-1">
    <?php echo scrub_out($level->quad->name); ?>
  </div>
  <div class="col-sm-2">
    <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>"><?php echo scrub_out($level->record); ?></a>
  </div>
  <div class="col-sm-1">
    <?php echo scrub_out($level->lsg_unit->name); ?>
  </div>
  <div class="col-sm-2">
    <?php echo \UI\boolean_word($level->closed); ?>
  </div>
</div>
<?php } ?>
</div>
<?php } ?>
<!-- Krotovina --> 
<?php if (Access::has('krotovina','read')) { ?>
<div class="row">
  <div>
    <?php if (Access::has('krotovina','create')) { ?>
    <p class="pull-right">
       <a class="btn btn-small btn-success" href="<?php echo Config::get('web_path'); ?>/krotovina/new">New Krotovina</a>
    </p>
    <?php } ?>
    <strong>Your last three Krotovina</strong>
  </div>
</div>
<div class="well">
  <div class="row">
    <div class="col-sm-2"><strong>Catalog #</strong></div>
    <div class="col-sm-3"><strong>Entered on</strong></div>
  </div>
<?php 
  $krotovinas = Krotovina::get_user_krotovina();
  foreach ($krotovinas as $uid) {
    $krotovina = new Krotovina($uid);
?>
  <div class="row">
    <div class="col-sm-2">
      <a href="<?php echo Config::get('web_path'); ?>/krotovina/view/<?php $krotovina->_print('uid'); ?>"><?php $krotovina->_print('record'); ?></a>
    </div>
    <div class="col-sm-3">
      <?php echo date('d-M-Y H:i:s',$krotovina->created); ?>
    </div>
  </div>
<?php } ?>
</div>
<?php } ?>
<!-- Features -->
<?php if (Access::has('feature','read')) { ?>
<div class="row">
  <div>
    <?php if (Access::has('feature','create')) { ?>
    <p class="pull-right">
       <a class="btn btn-small btn-success" href="<?php echo Config::get('web_path'); ?>/feature/new">New Feature</a>
    </p>
    <?php } ?>
    <strong>Your last three Features</strong>
  </div>
</div>
<div class="well">
  <div class="row">
    <div class="col-sm-2"><strong>Catalog #</strong></div>
    <div class="col-sm-3"><strong>Entered on</strong></div>
  </div>
<?php 
  $features = Feature::get_user_features();
  foreach ($features as $uid) { 
    $feature = new Feature($uid);
?>
  <div class="row">
    <div class="col-sm-2"><a href="<?php echo Config::get('web_path'); ?>/feature/view/<?php $feature->_print('uid'); ?>"><?php $feature->_print('record'); ?></a></div>
    <div class="col-sm-3"><?php echo date('d-M-Y H:i:s',$feature->created); ?></div>
  </div>
<?php } ?>
</div>
<?php } ?>
