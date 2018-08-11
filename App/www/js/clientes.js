var customers = function(){

  $.ajax({
      type: 'POST',
      url: SERV+"Productos/list",
      data: {start: 0, limit: 1, search: null},
      dataType: 'JSON',
      success: function(r) {
        console.log(r);
      },
      error: function(xhr) { // if error occured
        console.log(xhr);
      },
      complete: function() {

      }
  });
}

var loadFunctions = function() {
  return {
      init: function() {
          //AL INICIAR MÓDULO
          customers();
      }
  };
}();

$(document).ready(function() {
    loadFunctions.init();
});
