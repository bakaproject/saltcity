var $j = jQuery.noConflict();

$j(document).ready(function() {
  $j('.main_slider').tinycarousel({
    interval: true,
    intervalTime: 5000
  });

  $j('.product-slider-container').tinycarousel({
    interval: true
  });
});