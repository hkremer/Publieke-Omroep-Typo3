/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function(){  
    jQuery("form#submit").submit(function() {  
    // we want to store the values from the form input box, then send via ajax below  
    var email     = jQuery('#email').attr('value');  
    var campaign    = jQuery('#campaign').attr('value');  
    var vink = jQuery('#aanmeldvink').attr('value');  
    if ( (vink == '1') && (email != '')) {
        jQuery.ajax({  
            type: "POST",  
            url: "?eID=eonieuwsbrief_ajax",  
            data: "email="+ email +"&campaign="+ campaign +"&vink="+ vink,  
            success: function(){  
                jQuery('form#submit').hide();
                jQuery('#errordiv').hide();
                jQuery('#succes').html('Bedankt voor de aanmelding!');
            }  
        });
    }
    else {
         jQuery('#errordiv').html('Emailadres vergeten');   
        }
    return false;  
    });  
});  
