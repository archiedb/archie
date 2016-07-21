<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="add_field" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Add Field to <?php $site->_print('name'); ?> site</h3>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo Config::get('web_path'); ?>/manage/site/addfield">
        Add an additional field to this site. Once a field is added and at least once instance of it
        exists it cannot be removed, only disabled. 

        <strong>Show Answer</strong>: any character, less than a sentence<br />
        <strong>Long Answer</strong>: any character, less than 2 paragraphs<br />
        <strong>True/False</strong>: Drop down, TRUE / FALSE<br />

        <hr />
        <div class="row">
          <div class="form-group">
            <label class="col-md-4 control-label" for="inputType">Type</label>
            <div class="col-md-4">
              <input class="form-control" id="inputType" type="text" name="type" value="record" disabled />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label class="col-md-4 control-label" for="fieldName">Field Name</label>
            <div class="col-md-4">
              <input class="form-control" id="fieldName" type="text" name="fieldname" value="" />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label class="col-md-4 control-label" for="fieldType">Field Type</label>
            <div class="col-md-4">
              <select id="fieldType" name="fieldtype">
                <option value="string">Short Answer</option>
                <option value="text">Long Answer</option>
                <option value="boolean">True/False</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label class="col-md-4 control-label" for="fieldValidation">Validation</label>
            <div class="col-md-4">
              <select id="fieldValidation" name="fieldvalidation">
                <option value="string">Words</option>
                <option value="integer">Whole Numbers</option>
                <option value="decimal">Numbers with Decimals</option>
                <option value="boolean">True/False</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group">
            <label class="col-md-4 control-label" for="fieldEnabled">Enabled</label>
            <div class="col-md-4">
              <select id="fieldEnabled" name="enabled">
                <option value="1">True</option>
                <option value="0">False</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Custom Field</a>
        <input type="hidden" name="uid" value="<?php echo $site->_print('uid'); ?>">
        <input type="hidden" name="key" value="fields">
        <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</div>
