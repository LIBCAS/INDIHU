jQuery(function() {

    var mobileMenuId = "#mobile-menu";
    var openMobileMenuButtonId = "#mobile-menu-button";
    var closeMobileMenuButtonId = "#close-mobile-menu";

    jQuery(openMobileMenuButtonId).click(function(){
        jQuery( mobileMenuId ).slideToggle( "1000", function() {});
    });

    jQuery(closeMobileMenuButtonId).click(function(){
        jQuery( mobileMenuId ).slideToggle( "1000", function() {});
    })
});
