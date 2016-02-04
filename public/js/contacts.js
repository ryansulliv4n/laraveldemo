$(document).ready(function(){
  
  $('#contactTable').DataTable({
    "ajax": {
      "url": "/contacts-json",
      "dataSrc": function (json) {
        for (var i=0, len=json.length; i<len; i++) {
          json[i][0] = json[i].firstname;
          json[i][1] = json[i].lastname;
          json[i][2] = json[i].email;
          json[i][3] = json[i].phone;
          json[i][4] = json[i].editLink; 
          json[i][5] = json[i].deleteLink;
        }
      
        return json;
      }
    
    },
    "columns": [
      { "defaultContent": "" },
      { "defaultContent": "" },
      { "defaultContent": "" },
      { "defaultContent": "" },
      { "defaultContent": "" },
      { "defaultContent": "" },
    ]
  });

  $('#dialog').dialog({
    autoOpen: false,
    modal: true,
    width: 800,
    height: 600,
  });
      
  $(document).on('click', 'a.dialog', function(e){
    e.preventDefault();
    $('#dialog').html('');
    $('#dialog').dialog('option', 'title', 'Loading...').dialog('open');
    $('#dialog').load(this.href, function() {
      $(this).dialog('option', 'title', $(this).find('h1').text());
      $(this).find('h1').remove();
    });
  });

  $(document).on('click', '.btn-add', function(e){
    e.preventDefault();
    var controlForm = $('.contactForm #additionalInfo');
    var currentEntry = $(this).parents('.entry:first');
    var newEntry = $(currentEntry.clone()).appendTo(controlForm);
    newEntry.find('input').val('');
    controlForm.find('.entry:not(:last) .btn-add')
      .removeClass('btn-add').addClass('btn-remove')
      .removeClass('btn-success').addClass('btn-danger')
      .html('<span class="glyphicon glyphicon-minus"></span>');
    }).on('click', '.btn-remove', function(e){
      $(this).parents('.entry:first').remove();
      e.preventDefault(e);
      return false;
    });

  $(document).on('click', '.deleteLink', function(e){
    e.preventDefault();
    
    $.ajaxSetup({
        headers: {
          'X-XSRF-Token': $('meta[name="_token"]').attr('content')
        }
      });
    
    $.ajax({
      type: "POST",
      url: $(this).attr('href'),
      data: {_method: 'delete', _token: $(this).data('token')},
      success: function(data) {
        refreshTable();
      },
      error: function(data) {

      }
    });
  });  

});

function refreshTable() {
  $('#contactTable').dataTable().api().ajax.reload();  
}

