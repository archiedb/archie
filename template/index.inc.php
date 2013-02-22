<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<?php require_once 'template/menu.inc.php'; ?>
<img class="img-rounded" src="<?php echo Config::get('web_path'); ?>/images/title.jpg" />
<div class="content-block">
<p class="text-center lead">Hello <?php echo $GLOBALS['user']->username; ?>, you are currently working on <?php echo Config::get('site'); ?></p>
<table class="table table-bordered table-white">
<thead>
  <tr>
    <th>Total records</th>
    <th>Records entered today</th>
    <th>Todays busiest worker</th>
    <th>Todays most common classification</th>
  </tr>
</thead>
<tbody>
<tr>
  <td><?php echo Stats::total_records(); ?></td>
  <td><?php echo Stats::total_records('today'); ?></td>
  <td>
  <?php 
      $info = Stats::worker_records('today'); 
      if ($info['count'] > 0) {  
        echo $info['user'] . ' ('. $info['total'] . ')'; 
      }
      else { 
        echo "<strong class=\"text-error\">Nobody!</strong>";
      }
  ?>
  </td>
  <td>
  <?php 
      $info = Stats::classification_records('today'); 
      if ($info['count'] > 0) { 
        echo $info['classification'] . ' (' . $info['total'] . ')'; 
      }
      else { 
        echo "<strong class=\"text-error\">No Data</strong>";
      }
  ?>
  </td>
</tr>
</tbody>
</table>
</div><!-- End content block -->