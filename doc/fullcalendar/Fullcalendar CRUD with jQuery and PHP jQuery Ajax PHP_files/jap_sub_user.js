// JavaScript Document

$(document).ready(function() {
	$("#jap_news_sub_button").click(function() {
		if (checkEmail($('#jap_news_sub_email').val()))
		{
		var user_email = $("#jap_news_sub_email").val();
	var datastring = "email=" + user_email;
        $('.img_loader').show();
        $.ajax({
            url: "http://jqueryajaxphp.com/download/subscribe/",
            type: "GET",
            data: datastring,
			dataType: "html",
            success: function(data) {
              if (data == "success") {
                    $('#jap_error').fadeOut(100);					
					$('#jap_error_email').fadeOut(100);
                    $('#jap_response').fadeIn(200);
					$('.img_loader').hide();
					$('#subscription_form').hide(100);
                } else if (data == "subscribed") {
                    $('#jap_error').fadeOut(100);
					$('#jap_error_email').fadeOut(100);
					$('#subscription_form').hide(100);
					$('.img_loader').hide();
                    $('#jap_response').html('You are already subscribed. Go to <a class="dnld_highlight" href="javascript:firewall()" target="_blank">Downloads</a></span> section.').fadeIn(200);
                } else {
					$('.img_loader').hide();
                    $('#jap_error').fadeOut(100);
                }
            },
            error: function() {
			$('.img_loader').hide();
				$('#jap_error').fadeOut(100);
            }
        });
		return false;
		}
		else {
        $('#jap_error_email').fadeIn(100);
    }
	});
	
	setTimeout( "jQuery('.blink').hide(), jQuery('.unblink').fadeIn(200);",4000 );

$('.blink').each(function() {
    var elem = $(this);
    setInterval(function() {
        if (elem.css('visibility') == 'hidden') {
            elem.css('visibility', 'visible');
        } else {
            elem.css('visibility', 'hidden');
        }    
    }, 500);	
});
	
	startCounterAnimation();
});





    function startCounterAnimation() {
        setTimeout(function() {
            $('.feedstats').each(function() {
                var target = $(this).text();
                var node = $(this);
                var duration = 3500;
                $({someValue: 0}).animate({someValue: target}, {
                    duration: duration,
                    step: function() {
                        $(node).show();
                        $('.download_counter').find('label').fadeIn(500);
                        $(node).text(Math.floor(this.someValue + 1));
                    },
                    complete: function() {
                        $(node).text(target);
                    }
                });
            });
        }, 500);
    }
	
function firewall(email) {
var get_email = $("#jap_news_sub_email").val();
location.href = "http://download.jqueryajaxphp.com/dashboard/bypass.php?check="+get_email;
}

function checkEmail(email) {
    var filter = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
    return filter.test(email);
}