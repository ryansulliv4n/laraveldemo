$(document).ready(function(){
    
    $('.contactForm').on('submit', function(e){
      
      e.preventDefault();
      
      $.ajaxSetup({
        headers: {
          'X-XSRF-Token': $('meta[name="_token"]').attr('content')
        }
      });

      $.ajax({
        type: "POST",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        cache: false,
        success: function(data) {
          $('.contactForm').closest('.ui-dialog-content').dialog('close');
          refreshTable();
        },
        error: function(data) {
	  var error = JSON.parse(data.responseText);
	  var errorIdx = Object.keys(error)[0];
	  alert('ERROR: ' + error[errorIdx]);
	}
      });

      return false;

    });

});
