jQuery(document).ready(function() {

  jQuery('.t3registration_pi1_deleteImage').click(function(){
    if(confirm(jQuery(this).attr('alt'))) {
     name = jQuery('input.' + jQuery(this).attr('ref')).attr('name');
     console.log(name);
      jQuery('.' + jQuery(this).attr('ref')).remove();
        jQuery(this).after('<input type="file" name="' + name + '" />');
    jQuery(this).remove();
    }
  });
});