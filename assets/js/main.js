var $ = require('jquery');
$(document).ready(function() {
    $('.top-container').height($(window).height());
    if($(window).height()>500)
    {
        $('.top-container-innerMargin').css('padding-top', $(window).height()/3);
    }

    $( window ).resize(function() {
        $('.top-container').height($(window).height());
        if($(window).height()>500)
        {
            $('.top-container-innerMargin').css('padding-top', $(window).height()/3);
        }
    });
});