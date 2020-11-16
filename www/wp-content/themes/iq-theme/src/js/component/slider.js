var timer;
var timerSlide = 8000;
var sliderItemCount = 0;
var current = 0;
var itemWidth;

function sliderRight() {

    if (! $(':animated').length) {
        var sliderItemList = $('.slider__item');
        var mainWidth = $('.slider__list').width();
        var firstSliderItem = sliderItemList[0];
        var firstSlider = $(firstSliderItem);
        var w = firstSlider.width();
        var wMargin = w+43;
        if (mainWidth < (w*sliderItemList.length)) {
            firstSlider.animate({
                marginLeft: -wMargin
            }, 2000, function () {
                firstSlider.appendTo(firstSlider.parent()).removeAttr('style');
            });
        }
    }
}

function sliderClickRight() {
    
    clearInterval(timer);
    if (! $(':animated').length) {
        var sliderItemList = $('.slider__item');
        var mainWidth = $('.slider__list').width();
        var firstSliderItem = sliderItemList[0];
        var firstSlider = $(firstSliderItem);
        var w = firstSlider.width();
        var wMargin = w+43;
        if (mainWidth < (w*sliderItemList.length)) {
            firstSlider.animate({
                marginLeft: -wMargin
            }, 2000, function () {
                firstSlider.appendTo(firstSlider.parent()).removeAttr('style');
            });
        }
    }
}

function sliderClickLeft() {

    clearInterval(timer);
    if (! $(':animated').length) {
        var sliderItemList = $('.slider__item');
        var mainWidth = $('.slider__list').width();
        var lastSliderItem = sliderItemList[sliderItemList.length-1];
        var lastSlider = $(lastSliderItem);
        var w = lastSlider.width();
        var wMargin = w+43;
        lastSlider.prependTo(lastSlider.parent());
        lastSlider.css('margin-left', '-' + wMargin + 'px');
        if (mainWidth < (w*sliderItemList.length)) {
            lastSlider.animate({
                marginLeft: '+=' + wMargin + 'px'
            }, 2000, function () {
                lastSlider.removeAttr('style');
            });
        }
    }
}

$(document).ready(function() {

    timer = setInterval(sliderRight, timerSlide);

    $('.slider__left').on('click', sliderClickLeft);
    $('.slider__right').on('click', sliderClickRight);
    $('.slider').hover(function(){
        clearInterval(timer);
    },function(){
        timer = setInterval(sliderRight, timerSlide);
    });
});
