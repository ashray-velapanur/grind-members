$(function() {

  /**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie = function (key, value, options) {

    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        value = String(value);

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

 /* error handling */
  
	function ajaxError(xhr, textStatus, errorThrown) {
	  alert("Error on your request: " + errorThrown + " " + xhr.status);
	}
	function writeErrorMessage(msg) {
		msg = "<div id='errormsg'>"+msg+"</div>";
		if ($("#errormsg").length>0) {
	  		$("#errormsg").replaceWith(msg);
	  	} else {
	  		$("#loginform").before(msg);
	  	};
	}

/* form handling */
	
	$("#loginform").submit(function(e) {
		e.preventDefault();
	
			if (!$("#terms").prop("checked")){
				// terms and conditions not checked
					writeErrorMessage("<strong>ERROR</strong>: You need to accept the terms and conditions in order to continue");
					$("label[for~='terms']").addClass("errtext")
			} else {
				// terms and conditions was checked
			  $("label[for~='terms']").removeClass("errtext");
			  var formAction = $("#loginform").attr("action");   
			  var dataString = $("#loginform").serialize();
			  $.ajax({
			    type: "post",
			    
			    url: formAction,
			    data: dataString,
			    success: function(returnData) {
			        if (returnData == null) {
			        	writeErrorMessage("<strong>ERROR</strong>: Incorrect Username/Password combination");
			        	return false;	
			        } else if (returnData == 1) {
			        	//delete the cookie so next time we autosubmit]
			        	//alert($.cookie("grindwifi"));
			        	$.cookie('grindwifi', null); 
			        	//alert($.cookie("grindwifi"));
			        	$("#wifi_user").attr("value",$("#username").val());
			        	$("#wifi_pass").attr("value",$("#user_pass").val());
			        	$("#wifilogin").submit();
			    		return true;
			        } else {
			            writeErrorMessage("<strong>ERROR</strong>: Incorrect Username/Password combination");
			            return false;
			            
			        }
			    },
			    error: ajaxError
			  });
		};//end if
	});
	
	
	$(".pop-terms").click(function(e) {
    e.preventDefault();
    $(".overlay-bg, #pop-terms").fadeIn();
  });
  
  $("#pop-terms .close a, .overlay-bg").click(function(e) {
    e.preventDefault();
    $(".overlay-bg, #pop-terms").fadeOut();
  });
	

});


$(document).ready(function () {
	if(!testval){
		//alert("testval false");
		$("#wifilogin").submit();
	} else {
		// don't auto submit
		//alert("testval true");
	}
});
