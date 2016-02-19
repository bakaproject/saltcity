var $j = jQuery.noConflict(); 

console.log('working?', $j);

$j(document).ready(function() {
  $j('.main_slider').tinycarousel({
    interval: true
  });

  $j('.product-slider-container').tinycarousel({
    interval: true
  });
});