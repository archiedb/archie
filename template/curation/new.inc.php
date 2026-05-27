<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
require_once \UI\template('/menu');
?>
<?php $curation = Curation::last_created(); ?>
<?php if ($curation->uid) { ?>
  <h4>
    <a href="<?php echo Config::get('web_path'); ?>/curations/view/<?php echo scrub_out($curation->uid); ?>" class="btn btn-primary" >Last Curation</a>
    <small>Created by <?php echo scrub_out($curation->user->username); ?> on <?php echo scrub_out(date("d-M-y H:i:s",$curation->created)); ?></small>
  </h4>
<?php } ?>
<div class="page-header">
<h4>New Curation</h4>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_curation" method="post" action="<?php echo Config::get('web_path'); ?>/curations/create">
<div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('institution'); ?>">
    <label class="col-md-2 control-label" for="inputInstitution"><abbr title="Institution">Institution</abbr></label>
    <div class="col-md-2">
      <?php 
        $institutions = Curation::get_institutions(); 
      ?>
      <select id="inputLevel" class="form-control" name="institution">
      <?php 
      foreach ($institutions as $institution_uid) {
          $level = new Institution($institution_id);
          $is_selected = '';
          if (isset($_POST['institution'])) {
            if ($_POST['institution'] == $institution_id) { $is_selected=' selected="selected="'; }
          }
      ?>
        <option value="<?php echo scrub_out($institution_id); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($institution->name); ?></option>
      <?php } ?>
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
    <div class="<?php Err::form_class('building'); ?>">
    <label class="col-md-2 control-label" for="inputBuilding"><abbr title="Building">Building</abbr></label>
    <div class="col-md-2">
	    <select class="form-control" name="building">
      <?php
        $buildings = Curation::get_buildings();
        foreach ($buildings as $building_id) {
          $building = new Building($building_id);
        }
        ?>
        <option value="">Waldo Hall</option>        
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('room'); ?>">
    <label class="col-md-2 control-label" for="inputRoom">Room</label>
    <div class="col-md-2">
	    <select class="form-control" name="room">
        <option value="">132</option>        
      </select>
    </div> <!-- ERROR CHECK -->
    </div>
    <div class="<?php Err::form_class('cabinet'); ?>">
    <label class="col-md-2 control-label" for="inputCabinet">Cabinet</label>
    <div class="col-md-2">
	    <select class="form-control" name="room">
        <option value="">14</option>        
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('drawer'); ?>">
    <label class="col-md-2 control-label" for="drawer">Drawer</label>
    <div class="col-md-2">
	    <select class="form-control" name="drawer">
        <option value="">A</option>        
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
    <div class="<?php Err::form_class('status'); ?>">
    <label class="col-md-2 control-label" for="status">Status</label>
    <div class="col-md-2">
      <select class="form-control" id="status" name="status">
      	<option value="">On Loan</option> 
      </select>
    </div>
    </div> <!-- ERROR CHECK -->
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('site'); ?>">
    <label class="col-md-2 control-label" for="inputSite">Site</label>
    <div class="col-md-2">
      <select class="form-control" id="site" name="site">
      	<option value="">Coopers Ferry</option> 
      </select>
    </div>
    </div>
    <div class="<?php Err::form_class('catalogid'); ?>">
    <label class="col-md-2 control-label" for="inputCatalogID">Catalog ID</label>
    <div class="col-md-2">
      <input id="inputCatalogID" class="form-control" type="text" name="catalog_id" value="<?php \UI\form_value('catalog_id'); ?>">
    </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
    <div class="<?php Err::form_class('notes'); ?>">
      <label class="col-md-2 control-label" for="inputNotes">Notes</label>
      <div class="col-md-6">
    	  <textarea placeholder="Notes..." class="form-control" rows="4" name="notes"><?php \UI\form_value('notes'); ?></textarea>
      </div>
    </div>
  </div>
</div><div class="row">
  <div class="form-group">
  <div class="col-md-6 col-md-offset-2">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
  </div>
</div>
</form>
