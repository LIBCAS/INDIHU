function changeVideoSlider() {

    var selectItem = $(this);
    var id = selectItem.attr('id');

    $('.video-slider__item').each(function() {
        $(this).removeClass('bold');
    });

    // var src = 'https://www.youtube.com/embed/XuErudfWHEA?controls=0';
    var src = 'https://www.youtube.com/embed/aZUWAtPBu8Q';

    if (id == 'index') {
        src = 'https://www.youtube.com/embed/uXlfDqWW_HI';
    } else if (id == 'mind') {
        src = 'https://www.youtube.com/embed/ESyeUuVD3Fg';
    } else if (id == 'ocr') {
        src = 'https://www.youtube.com/embed/ikxc8kV3Scw';
    }

    $('#videoSliderIframe').attr('src', src);
    selectItem.addClass('bold');
}

$(document).ready(function () {
    
    $('.video-slider__item').on('click', changeVideoSlider);
    
});