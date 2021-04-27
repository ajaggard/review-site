// Constructor for slideshow object
function Slideshow(banner) {
    var this_slideshow = this;
    this_slideshow.banner_element = banner;
    this_slideshow.num_banners = $(this_slideshow.banner_element).find('.feature-banner').length;
    this_slideshow.cur_idx = 0;
    
    // Set the slideshow on a timer to automatically switch to the next banner (or the first if at the last) 
    this_slideshow.auto_switch = setInterval(function() {
        var new_idx = this_slideshow.cur_idx == this_slideshow.num_banners - 1 ? 0 : this_slideshow.cur_idx + 1;
        this_slideshow.set_active(new_idx);
    }, 10000);
    
    // Set the first banner as active and add functionality to switcher buttons
	$(this_slideshow.banner_element).find('.switcher').children().eq(this_slideshow.cur_idx).addClass('active')
        .end().on('click', function() {
            // End the timer once the user has manually switched the banner
            clearInterval(this_slideshow.auto_switch);
            this_slideshow.set_active($(this).attr('data-slide_idx'));
        });
        
    return;
}

// Change the active banner to the one at the passed index
Slideshow.prototype.set_active = function(active_idx) {
    var this_slideshow = this;
    
    $(this_slideshow.banner_element).find('.feature-banner').eq(this_slideshow.cur_idx).fadeOut(100, function() {
        $(this_slideshow.banner_element).find('.feature-banner').eq(active_idx).fadeIn(100).css('display', 'block');
    });
    $(this_slideshow.banner_element).find('.switcher').children().eq(this_slideshow.cur_idx).removeClass('active')
        .end().eq(active_idx).addClass('active');
    
    this_slideshow.cur_idx = active_idx;
    
    return;
}

$(document).ready(function(){
	// Instanstiate the slideshow object for the "featured" banner
	var banner_slideshow = new Slideshow($('#feature_slider'));
});