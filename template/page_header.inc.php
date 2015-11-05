<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }

$limit = $view->get_offset(); 
$start = $view->get_start(); 
$total = $view->get_total(); 
$type  = strtolower($view->get_type());
$sides = 6; 


// Next and Prev
$next_offset = (($start+$limit) > $total) ? $start : $start+$limit; 
$prev_offset = (($start - $limit) < 0) ? '0' : $start - $limit;

// How many pages
if ($limit > 0 && $total > $limit) { 
  $pages = ceil($total / $limit); 
}
else { 
  $pages = 0; 
}

// If we have zero pages we don't need to continue
if ($pages > 1) { 
  $page_data = array('up'=>array(),'down'=>array()); 
  
  if ($start > 0) { 
      $current_page = floor($start/$limit); 
  }
  else { 
      $current_page = 0; 
  }

  // Create 'sides' pages in either direction
  $page = $current_page;
  $i = 0; 
  // Down
  while ($page > 0) { 
    if ($i == $sides) { $page_data['down'][1] = '...'; $page_data['down'][0] = '0'; break; }
    $i++;
    $page = $page - 1;
    $page_data['down'][$page] = $page * $limit; 
  } // while page > 0

  // Up
  $page = $current_page + 1; 
  $i = 0;
  while ($page < $pages) {
    if ($page * $limit > $total) { break; }
    if ($i == $sides) { 
      $key = $pages - 1; 
      if (!isset($page_data['up'][$key])) { $page_data['up'][$key] = '...'; }
      $page_data['up'][$pages] = ($pages - 1) * $limit; 
      break;
    }
    $i++; 
    $page_data['up'][$page] = ($page) * $limit; 
    $page = $page + 1; 
  } // while going down

  ksort($page_data['up']); 
  ksort($page_data['down']); 
?>
<div class="text-center">
<nav>
  <ul class="pagination">
  <li><a href="<?php echo Config::get('web_path'); ?>/<?php echo $type; ?>/offset/<?php echo $prev_offset; ?>">&laquo;</a></li>
<?php 
  $current_page++; // Also starts at 1 not zero
  foreach ($page_data['down'] as $page => $offset) { 
    if ($offset === '...') { 
  ?>
  <li class="disabled"><a href="#">...</a></li>
  <?php 
    } else {
    $page++; // This starts on 1 not zero
?> 
    <li><a href="<?php echo Config::get('web_path'); ?>/<?php echo $type; ?>/offset/<?php echo $offset; ?>"><?php echo $page; ?></a></li>
<?php } } ?>
    <li class="active"><a href="#"><?php echo $current_page; ?></a></li>
<?php 
  foreach ($page_data['up'] as $page => $offset) { 
  if ($offset === '...') { 
  ?>
  <li class="disabled"><a href="#">...</a></li>
  <?php
    } else {
  $page++; // We don't do zero
?>  
    <li><a href="<?php echo Config::get('web_path'); ?>/<?php echo $type; ?>/offset/<?php echo $offset; ?>"><?php echo $page; ?></a></li>
<?php } }?>
  <li><a href="<?php echo Config::get('web_path'); ?>/<?php echo $type; ?>/offset/<?php echo $next_offset; ?>">&raquo;</a></li>
  </ul>
</nav>
</div>
<?php } // if we have pages at all ?>
