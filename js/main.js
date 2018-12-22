var jp_facebook_album = (function($) {

    var grid = document.querySelector(".jp-facebook-grid");
    var rowSize = parseInt(getComputedStyle(grid).getPropertyValue("grid-auto-rows"), 10);
    var rowGap = parseInt(getComputedStyle(grid).getPropertyValue("grid-gap"), 10);
    var gridItems = [];

    var init = function() {
        initGridItems();
        // Window load is later than jQuery's document ready;
        // it waits till assets like images are downloaded.
        window.addEventListener("load", positionGridItems);
        window.addEventListener("resize", _.debounce(positionGridItems, 100));
    };

    // Creates our array of DOM elements for later manipulation.
    var initGridItems = function() {
        var items =  document.getElementsByClassName('grid__item');
        for(var i = 0; i < items.length; i++){
            gridItems.push({ item: items[i], content: items[i].getElementsByTagName('IMG')[0] })
        }
    };

    // Arrange/rearrange our grid items into the masonry layout.
    var positionGridItems = function() {
        gridItems.forEach(function (_ref) {
            var item = _ref.item;
            var content = _ref.content;
            content.classList.remove("cover");
            var rowSpan = Math.ceil((content.offsetHeight + rowGap) / (rowSize + rowGap));
            item.style.setProperty("--row-span", rowSpan);
            content.classList.add("cover");
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