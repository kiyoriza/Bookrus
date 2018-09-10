$(function() {
  $(".open-btn").on("click", function() {
    // $(".hiderank").nextAll().toggle();
    $(".hiderank").toggle();
    $(this).hide();
    $(this).next().show();
  });
  $(".close-btn").on("click", function() {
    $(".hiderank").toggle();
    $(this).hide();
    $(this).prev().show();
  });
});

$(function() {
  $(".open-btn-all").on("click", function() {
    // $(".hiderank").nextAll().toggle();
    $(".hiderank1").toggle();
    $(this).hide();
    $(this).next().show();
  });
  $(".close-btn-all").on("click", function() {
    $(".hiderank1").toggle();
    $(this).hide();
    $(this).prev().show();
  });
});