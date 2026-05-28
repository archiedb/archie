// vim: set softtabstop=2 ts=2 sw=2 expandtab:
$(document).ready(function () {

  // Picking Institution allows you to pick buildings, which lets you pick a room, which allows you to pick a cabinet, which... you 
  // get the idea
  $('#inputInstitution').change(function() {
    var button = $(this)
    var formData = new FormData($(this).parents('form')[0]);
    $.ajax({
      url: '/api/curation/institutionBuildings',
      type: 'POST',
      data: formData,
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr();
        return myXhr;
      },
      success: function (data) {
        var $select = $('#inputBuilding');
        if (data.status == 'OK') {
          $('#inputBuilding option:gt(0)').remove()
          $.each(data.levels,function(key,value) {
            $level_select.append($("<option></option>").attr("value",value['uid']).text(value['name']));
          });
        }
        else {
          $('#inputBuilding option:gt(0)').remove()
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
  })

  $('#inputBuilding').change(function() {
    var button = $(this)
  })

  $('#inputRoom').change(function() {

  })

  $('#inputCabinet').change(function() {

  }

}
