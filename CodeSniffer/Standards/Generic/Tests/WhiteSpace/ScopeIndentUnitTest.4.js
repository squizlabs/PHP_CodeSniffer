jQuery(document).ready(function () {

    if(jQuery('.imgGallery').length > 0 || jQuery('.featuresBox').length > 0) {var controller = jQuery.superscrollorama();}

         var a2 = jQuery('.imgGallery .rowImages a');
    for (var i = 0; i < a2.length; i++) {
    controller.addTween(jQuery(a2[i]),TweenMax.fromTo(
        jQuery(a2[i]),.5,
        {css:{'opacity':'0','-webkit-transform':'scale(0)','-moz-transform':'scale(0)',
        '-ms-transform':'scale(0)','-o-transform':'scale(0)','transform':'scale(0)'},
        immediateRender:true,ease:Quad.easeInOut},{css:{'opacity':'1','-webkit-transform':'scale(1)',
        '-moz-transform':'scale(1)','-ms-transform':'scale(1)','-o-transform':'scale(1)','transform':'scale(1)'},
        ease:Quad.easeInOut}));
    }

        var a3 = jQuery('.featuresBox ul li');
    for (var i = 0; i < a3.length; i++) {
    controller.addTween(jQuery(a3[i]),TweenMax.fromTo(
        jQuery(a3[i]),.7,
        {css:{'opacity':'0','-webkit-transform':'scale(0)','-moz-transform':'scale(0)',
        '-ms-transform':'scale(0)','-o-transform':'scale(0)','transform':'scale(0)'},
        immediateRender:true,ease:Quad.easeInOut},{css:{'opacity':'1','-webkit-transform':'scale(1)',
        '-moz-transform':'scale(1)','-ms-transform':'scale(1)','-o-transform':'scale(1)','transform':'scale(1)'},
        ease:Quad.easeInOut}));
    }

 });