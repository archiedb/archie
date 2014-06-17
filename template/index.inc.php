<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<img class="img-rounded" src="<?php echo Config::get('web_path'); ?>/images/title.jpg" />
<div class="page-header">
  <h3>Hello <?php echo \UI\sess::$user->name; ?>, you are currently working on <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<table class="table table-bordered table-white">
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
