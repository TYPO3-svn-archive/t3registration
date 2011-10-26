jQuery(document).ready(function() {

  jQuery('.t3registration_pi1_deleteImage').click(function(){
    if(confirm(jQuery(this).attr('alt'))) {
     name = jQuery('input.' + jQuery(this).attr('ref')).attr('name');
     console.log(name);
      jQuery('.' + jQuery(this).attr('ref')).remove();
        jQuery(this).after('<input type="file" name="' + name + '" />');
    jQuery(this).remove();
      /*$(this).removeClass('trash');
      var id = ($(this).attr('id')) ? $(this).attr('id') : '';
      var classe = $(this).attr('class');
      $(this).prev().remove();
      $(this).next().remove();
      $(this).after('<input type="file" '+id+' name="'+classe+'" />');
      $(this).remove();
      var value = $('#hiddenImages').attr('value').split(',');
      value[classe.match(/[0-9]+(?=])/)-1] = '';
      $('#hiddenImages').attr('value',value.join(','))*/
    }
  });
});