var customers = function(s = 0, l = 4, c = null){
  $.ajax({
      type: 'POST',
      url: SERV+"Clientes/list",
      data: {start: s, limit: l, search: c},
      dataType: 'JSON',
      success: function(r) {
        console.log(r);
        if(r.status=='success'){
          for(i=0;i<r.data.length;i++){
            if(i==0){
              if(s == 0){
                var html = '<li class="media border-bottom">';
              } else {
                var html = '<li class="media border-bottom mt-3">';
              }
            } else {
              html += '<li class="media border-bottom mt-3">';
            }
            if( Number(r.data[i].tipo) == 2 ){
              html += '<figure class="figure">'+
                        '<img class="mr-3" src="img/c64_1.png" alt="">'+
                        '<figcaption class="figure-caption text-left">'+
                          '<small>'+
                            r.data[i].nit+'-'+r.data[i].digito+
                          '</small>'+
                        '</figcaption>'+
                      '</figure>'+
                      '<div class="media-body text-left">'+
                        '<h5 class="mt-0">'+
                          r.data[i].razon_social+
                        '</h5>'+
                        '<small>Descripción de producto maximo dos lineas</small>'+
                      '</div>';
            } else if( Number(r.data[i].tipo) == 1 ){
              html += '<figure class="figure">'+
                        '<img class="mr-3" src="img/c64_2.png" alt="">'+
                        '<figcaption class="figure-caption text-left">'+
                          '<small>'+
                            r.data[i].documento+
                          '</small>'+
                        '</figcaption>'+
                      '</figure>'+
                      '<div class="media-body text-left">'+
                        '<h5 class="mt-0">'+
                          r.data[i].apellido+", "+r.data[i].nombre+
                        '</h5>'+
                        '<p>Descripción de producto maximo dos lineas</p>'+
                      '</div>';
            } else {
              // NO SE RECONOCE TIPO DE CLIENTE
            }
            html += '</li>';
          }
        } else {

        }
        if(s==0){
          $('#loadCustomers').html(html);
        } else {
          $('#loadCustomers').append(html);
        }
      },
      error: function(xhr) { // if error occured
        console.log(xhr);
      },
      complete: function() {

      }
  });
}

var scrollDown = function(){
   let $win = $(window);

   $win.scroll(function () {
       if ($win.scrollTop() == 0){
           //alert('Scrolled to Page Top');
       } else if ($win.height() + $win.scrollTop() == $(document).height()) {
          customers($('#loadCustomers li').length);
       }
   });
}

var loadFunctions = function() {
  return {
      init: function() {
          //AL INICIAR MÓDULO
          customers();
          scrollDown();
      }
  };
}();

$(document).ready(function() {
    loadFunctions.init();
});
