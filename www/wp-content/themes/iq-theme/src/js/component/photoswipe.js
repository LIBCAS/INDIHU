import PhotoSwipe from 'photoswipe';
import PhotoSwipeUI_Default from 'photoswipe/dist/photoswipe-ui-default';

$(document).ready(function() {
    var getSrcsetLargestSrc = function(srcset) {
        if (srcset === undefined) {
            return "";
        }

        srcset = srcset.split(", ");

        var largestIndex = 0, largestSize = 0;

        srcset.forEach(function(e, i) {
            var currentSize = e.split(" ")[1];
            currentSize = parseInt(currentSize.substring(0, currentSize.length-1));

            if (currentSize > largestSize) {
                largestSize = currentSize;
                largestIndex = i;
            }
        });

        return srcset[largestIndex].split(" ")[0];
    }

    var openLightbox = function(items, index = 0) {
        var options = {
            bgOpacity: 0.9,
            fullscreenEl: false,
            history: false,
            index: index,
        }

        var gallery = new PhotoSwipe( $(".pswp")[0], PhotoSwipeUI_Default, items, options);

        gallery.listen('imageLoadComplete', function (index, item) {
            if (item.h < 1 || item.w < 1) {
                let img = new Image()
                img.onload = () => {
                    item.w = img.width
                    item.h = img.height
                    gallery.invalidateCurrItems()
                    gallery.updateSize(true)
                }
                img.src = item.src
            }
        });

        gallery.init();
    }

    $('.wp-block-gallery a').removeAttr('href');
    $('.wp-block-image a:not(.open)').removeAttr('href');

    $(".wp-block-gallery img").on("click", function() {
        var galleryEl = $(this).closest(".wp-block-gallery");
        var figures = $("figure", galleryEl);

        var items = [];

        $(figures).each(function(i) {
            var img = $("img", this);
            var src = getSrcsetLargestSrc($(img).attr("srcset"));

            if (src == "") {
                src = $(img).attr("src");
            }

            var item = {
                src: src,
                w: 0,
                h: 0
            }

            var caption = $("figcaption", this);
            if (caption.length > 0) {
                item["title"] = $(caption).html();
            }

            items.push(item);
            $(this).data("data-pswp-uid", i+1);
        });

        var index = $(this).closest("figure").data("data-pswp-uid")-1;

        openLightbox(items, index);
    });

    $(".wp-block-image a:not(.open) img").on("click", function() {
        var src = getSrcsetLargestSrc($(this).attr("srcset"));
        if (!src) {
            src = $(this).attr("src");
        }

        var item = {
            src: src,
            w: 0,
            h: 0
        }

        var caption = $("figcaption", this.closest(".wp-block-image"));
        if (caption.length > 0) {
            item["title"] = $(caption).html();
        }

        var items = [item];
        openLightbox(items);
    });
});