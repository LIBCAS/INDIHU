$(document).ready(function() {
    $('.document-folder__name').on('click', function() {
        let parentItem = $(this).closest('.document-folder__item');
        if (parentItem.hasClass('document-folder__item_active')) {
            parentItem.find('.document-folder__item').removeClass('document-folder__item_active');
            parentItem.toggleClass('document-folder__item_active');
        } else {
            parentItem.toggleClass('document-folder__item_active');
        }
    });
});