var customers = function(s = 0, c = null){
  $.ajax({
      type: 'POST',
      url: SERV+"Clientes/list",
      data: {start: s, limit: LIMIT_LISTS, search: c},
      dataType: 'JSON',
      beforeSend: function(xhr){
          if(s==0){
            let html = '<li id="loadingCustomers" class="media">'+
                          '<div class="media-body text-center">'+
                            '<div class="mt-5">'+
                              '<i class="fa fa-plus fa-spinner fa-pulse fa-5x fa-fw"></i>'+
                              '<p>Cargando...</p>'+
                            '</div>'+
                          '</div>'+
                        '</li>';
            $('#loadCustomers').html(html);
          } else {
            let html = '<li id="loadingCustomers" class="media">'+
                          '<div class="media-body text-center">'+
                            '<div class="mt-5">'+
                              '<p>Cargando...</p>'+
                            '</div>'+
                          '</div>'+
                        '</li>';
            $('#loadCustomers').append(html);
          }
      },
      success: function(r) {
        $('#loadingCustomers').remove();
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
                          r.data[i].id+
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
                          r.data[i].id+
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

var search = function(){
   let $win = $(window);
   $win.scroll(function () {
      if ($win.scrollTop() == 0){
           //alert('Scrolled to Page Top');
      } else if ($win.height() + $win.scrollTop() == $(document).height()) {
         let valor = $('#stringCustomer').val();
         if(valor==undefined || valor==""){
           valor = null;
         }
         customers($('#loadCustomers li').length, valor);
      }
   });

   $('#searchCustomer').on('click', function(){
     let valor = $('#stringCustomer').val();
     if(valor==undefined || valor==""){
       valor = null;
     }
     if(valor!=null){
       customers(0, valor);
     }
   });

   $('#stringCustomer').on('keydown', function(e){
     if(e.keyCode == 13){
       let valor = $('#stringCustomer').val();
       if(valor==undefined || valor==""){
         valor = null;
       }
       if(valor!=null){
         customers(0, valor);
       }
     }
   });

}

var locations = function(p = 0){
  $.ajax({
    type: 'POST',
    url: SERV+"Ubicaciones/childrens",
    data: {parent: p},
    dataType: 'JSON',
    success: function(r) {
      if(r.status=='success'){
        html = '<select class="recursiveLocate custom-select mb-2"><option disabled selected>Seleccione Ubicación</option>';
        for(i=0;i<r.cities.length;i++){
          html += '<option value="'+r.cities[i].id+'">'+r.cities[i].loc+'</option>';
        }
        html += '</select>';
        $('#locateCustomer').append(html);
      } else {
        if(p==0){
          html = '<div class="alert alert-'+r.status+' alert-dismissible fade show" role="alert">'+
                    r.statusMessage+
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                      '<span aria-hidden="true">&times;</span>'+
                    '</button>'+
                  '</div>';
          $('#locateCustomer').append(html);
        }
      }
    },
    error: function(xhr) { // if error occured
      console.log(xhr);
    },
    complete: function() {

    }
  });
}

var insertUpdate = function(action, frm){
  let dataFrm = $(frm).serializeArray();
  dataFrm.push({name: 'action', value: action});
  $.ajax({
      type: 'POST',
      url: SERV+"Clientes/insertUpdate",
      data: dataFrm,
      dataType: 'JSON',
      beforeSend: function(xhr){

      },
      success: function(r) {

      },
      error: function(xhr) { // if error occured
        console.log(xhr);
      },
      complete: function() {

      }
  });

  /*switch (action) {
    case 1:

      break;
    case 2:

      break;
    default:

  }*/
}

var events = function(){
  $('#frmNewCustomer select[name="type"]').on('change', function(){
    let type = Number($(this).val());
    if(type == 1){
      $('.typePerson').removeClass('d-none');
      $('.typeCompany').addClass('d-none');
    } else if(type == 2){
      $('.typePerson').addClass('d-none');
      $('.typeCompany').removeClass('d-none');
    } else {

    }
  });

  $('#locateCustomer').on('change', '.recursiveLocate', function(){
    let value = $(this).val();
    let i = 0;
    $('.recursiveLocate').each(function(){
      if(i==1){
        $(this).remove();
      }
      if($(this).val()==value){
        i = 1;
      }
    });
    $('#frmNewCustomer input[name="city"]').val(value);
    locations(value);
  });

  $('#newCustomer').on('hide.bs.modal', function(){
    resetFrm('#frmNewCustomer');
  });

  $('#newCustomer').on('click', '.modal-footer .btn-success', function(){
    insertUpdate(1, '#frmNewCustomer');
    //resetFrm('#frmNewCustomer');
  });
}

var loadFunctions = function() {
  return {
      init: function() {
          //AL INICIAR MÓDULO
          customers();
          search();
          events();
          locations();
      }
  };
}();

$(document).ready(function() {
    loadFunctions.init();
});
