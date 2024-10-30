/**
 * Hello Star jQuery script
 */
jQuery(document).ready(function($) {
    //Color Picker
    $('.text-color').wpColorPicker({
        change: function(event, ui) {
           $("#hello_star").css('color', ui.color.toString());
        },
        palettes: true
    });
    $('.text-bg-color').wpColorPicker({
       change: function(event, ui) {
          $("#hello_star").css('backgroundColor', ui.color.toString());
       },
        palettes: true
    });
    // --------------------------------------------------
    //Replace DEMO_KEY in this URL with your new API key.
    var url = "https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY";
    //Ajax call for Nasa apod. This code was taken from https://api.nasa.gov/.
    $.ajax({
        url: url,
        success: function(result){
            if("copyright" in result) {
                $("#copyright").text("Image Credits: " + result.copyright);
            }
            else {
                $("#copyright").text("Image Credits: " + "Public Domain");
            }
            if(result.media_type == "video") {
                $("#apod_img_id").css("display", "none");
                $("#apod_vid_id").attr("src", result.url);
            }
            else {
                $("#apod_vid_id").css("display", "none");
                $("#apod_img_id").attr("src", result.url);
            }
            $("#apod_title").text(result.title);
        }
    });
});
