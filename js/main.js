var jp_facebook_album = (function($) {

    var init = function() {

        $('.jp-facebook-grid').masonry({
            //columnWidth: 100,
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true
        });
    };

    return {
        init: init
    };

})(jQuery);

// Wait till document is ready before initializing our code.
jQuery(document).ready(function(){
    jp_facebook_album.init();
});