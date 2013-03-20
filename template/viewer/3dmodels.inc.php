<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }

// We need the extension
$info = pathinfo($model->filename); 
$extension = $info['extension'];
$name = strlen($model->notes) ? $model-notes : basename($model->filename); 
?>
<?php require_once 'template/menu.inc.php'; ?>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script> 
  <style> 
   .chromeFrameInstallDefaultStyle { 
     margin-top: 10px; 
     width: 800px; 
     border: 5px solid blue; 
   } 
  </style> 
  <script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/thingiview/javascripts/Three.js"></script> 
  <script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/thingiview/javascripts/plane.js"></script> 
  <script type="text/javascript" src="<?php echo Config::get('web_path'); ?>/lib/thingiview/javascripts/thingiview.js"></script>
  <script>
    window.onload = function() {
      // You may want to place these lines inside an onload handler
      CFInstall.check({
        mode: "inline", // the default
        node: "prompt"
      });

      thingiurlbase = "<?php echo Config::get('web_prefix'); ?>/lib/thingiview/javascripts/";
      thingiview = new Thingiview("viewer");
      thingiview.setObjectColor('#0066FF');
      thingiview.initScene();
<?php if ($extension == 'stl') { ?>
      thingiview.loadSTL('<?php echo Config::get('web_prefix'); ?>/media/media/<?php echo scrub_out($model->uid); ?>');
<?php } elseif ($extension == 'ply') { ?>
      thingiview.loadPLY('<?php echo Config::get('web_prefix'); ?>/media/media/<?php echo scrub_out($model->uid); ?>');
<?php } ?>
      thingiview.setShowPlane(true);
      thingiview.setRotation(false);
    }
    
    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
        });
        return vars;
    }    
  </script>
<p class="pull-right">
  <a href="<?php echo Config::get('web_path'); ?>/records/view/<?php echo scrub_out($model->parentuid); ?>" class="btn">Record</a>
</p>
<div class="page-header">
  <h3>3d Model View - <?php echo scrub_out($name); ?></h3>
</div>
<p class="text-center">
  <input class="btn" onclick="thingiview.setCameraView('top');" type="button" value="Top" />
  <input class="btn" onclick="thingiview.setCameraView('side');" type="button" value="Side" />
  <input class="btn" onclick="thingiview.setCameraView('bottom');" type="button" value="Bottom" />
  <input class="btn" onclick="thingiview.setCameraView('diagonal');" type="button" value="Diagonal" />

  <input class="btn" onclick="thingiview.setCameraZoom(5);" type="button" value="Zoom +" />
  <input class="btn" onclick="thingiview.setCameraZoom(-5);" type="button" value="Zoom -" />

  Rotation: <input class="btn btn-primary" onclick="thingiview.setRotation(true);" type="button" value="on" /> | <input class="btn btn-danger" onclick="thingiview.setRotation(false);" type="button" value="off" />
</p>

<div id="viewer" style="width:100%;height:400px"></div>
<p>
</p>
