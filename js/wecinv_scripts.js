(function($) {

/*
 * Document Ready
 */
$(document).ready(function(){

    $( "#tabs" ).tabs();

    $( "#accordion" ).accordion({
        heightStyle: "content",
        collapsible: true,
        active: false,
    });

});

})( jQuery );
