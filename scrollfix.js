$(document).ready(function() {

    // If cookie is set, scroll to the position saved in the cookie.
    if ( $.cookie("scroll") !== null ) {
        $(document).scrollTop( $.cookie("scroll") );
    }

});

function save_scroll(){
    $.cookie("scroll", $(document).scrollTop() );
}