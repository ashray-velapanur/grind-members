jQuery.easing['jswing'] = jQuery.easing['swing'];
jQuery.extend(jQuery.easing, {
  def: 'easeOutExpo',
  swing: function (x, t, b, c, d) {
    return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
  },
  easeOutExpo: function (x, t, b, c, d) {
    return (t == d) ? b + c : c * (-Math.pow(2, -10 * t / d) + 1) + b;
  }
});
$(function () {
  if ($.browser.webkit) {
    $("html").addClass("webkit");
  } /* slideshow */
  $(".slideshow").each(function () {
    var that = this;
    var cap = $("li.current img", that).attr("alt");
    $(".caption", that).html(cap);
    var productCnt = $(".slides ul li", that).length;
    var i, ar = [];
    for (i = 1; i <= productCnt; i++) {
      ar.push("." + i);
    }
    $(".slides", that).jCarouselLite({
      btnPrev: $(".prev-slide", that),
      btnNext: $(".next-slide", that),
      btnGo: ar,
      easing: "easeOutExpo",
      afterEnd: function (a, to, btnGo) {
        var cap = $("li.current img", that).attr("alt");
        $(".caption", that).html(cap);
        if (btnGo.length <= to) {
          to = 0;
        }
        $(".circle-nav a", that).removeClass("current");
        $(btnGo[to], that).addClass("current");
        $('#slideshowArticleTitle a').attr('title', $(a).find('.articleTitle:first').val());
        $('#slideshowArticleTitle a').attr('href', $('#homeSlideshow').find('.articleLink:first').val());
        $('#slideshowArticleTitle a').html($(a).find('.articleTitle:first').val());
        $('#slideshowArticleAuthor').html($(a).find('.articleAuthor:first').val());
        $('#slideshowArticleDate').html($(a).find('.creditDate:first').val());
        $('#slideshowArticleSource a').html($(a).find('.creditName:first').val());
        $('#slideshowArticleSource a').attr('href', $(a).find('.creditUrl:first').val());
        $('#slideshowArticleDetails').show();
      }
    });
  });
  $(".slides").hover(function () {
    $(".arrow").fadeIn(300);
  }, function () {
    $(".arrow").fadeOut(300);
  });
  //Initialize the slideshow credits the first time.
  $('#slideshowArticleTitle a').attr('title', $('#homeSlideshow').find('.articleTitle:first').val());
  $('#slideshowArticleTitle a').attr('href', $('#homeSlideshow').find('.articleLink:first').val());
  $('#slideshowArticleTitle a').html($('#homeSlideshow').find('.articleTitle:first').val());
  $('#slideshowArticleAuthor').html($('#homeSlideshow').find('.articleAuthor:first').val());
  $('#slideshowArticleDate').html($('#homeSlideshow').find('.creditDate:first').val());
  $('#slideshowArticleSource a').html($('#homeSlideshow').find('.creditName:first').val());
  $('#slideshowArticleSource a').attr('href', $('#homeSlideshow').find('.creditUrl:first').val());
  $('#slideshowArticleDetails').show(); /* Change password for new member */
  $("#passwordFormNewMember").submit(function () {
    $("#error-message").html("Passwords do not match.").hide();
    if ($("#password").val() == "" && $("#password_confirm").val() == "") {
      $('#error-message').html('Your password must not be empty.').show().css('display', 'inline-block');
      return false;
    }
    if ($("#password").val() == $("#password_confirm").val()) {
      return true;
    } else {
      $('#error-message').html('Passwords do not match.').show().css('display', 'inline-block');
      return false;
    }
  });
  
  $.fn.focusAndBlur = function() {
    $.each(this, function(index, target) {
      enable($(target));
    });
    function enable(target) {
      var initVal = target.val();
      target.focus(
      function() {
        if (target.val() === initVal) {
          $(this).val("");
        }
      }).blur(function() {
        if (!$(this).val()) {
          $(this).val(initVal);
        }
      });
    };
  };
  $(".focus, input:password").focusAndBlur();
  
  function ToggleEdit(itemContainerId, turnOnEdit) {
    if (itemContainerId == "membershipTypeBlock" && billingData == null) {
      alert("You must have a valid credit card on file in order to change your membership.");
      itemContainerId = "billingInfoBlock";
    }
    var roItemContainer = $('#' + itemContainerId + ' .roItemContainer');
    if (turnOnEdit) {
      $(roItemContainer).slideUp().next().slideDown();
      $(roItemContainer).prev().hide();
    } else {
      $(roItemContainer).slideDown().next().slideUp();
      $(roItemContainer).prev().show();
    }
  }
  $('.roItemContainer .roItemNoEdit, .roItemContainer .roItemNoEditBlack').click(function (e) {
    e.preventDefault();
    return false;
  });
  $('div.roItemContainer').click(function () {
    $('.editItemContainer').slideUp();
    $('.roItemContainer').slideDown();
    ToggleEdit($(this).parent().attr('id'), true);
  });
  $('input').keypress(function (e) {
    if (e.which == 13) {
      $(this).closest("form").submit();
    }
  });
  $(".toggleit").click(function () {
    ToggleEdit($(this).attr('rel'), false);
    $('label.error').remove();
    $(".updateItem").removeAttr("disabled"); 
    $(".updateItem, .text-input, textarea").removeClass('error').fadeTo('slow',1);
    $(".loader").remove();
  });
  $("#fullName-form").validate();
  
  $("#waitlist-form").submit(function() {
	var itemContainerId = $(this).attr("rel");
	var itemId = {"item":itemContainerId};
	var dataString = $.param(itemId) + '&' + $.param({"data":$(this).serializeArray()});
	var userID = $("#user_id").val();
	var idType = $("#id_type").val();
	var formAction = $("#editaction").attr("value");
	$.ajax({
          type: "POST",
          url: formAction + userID + "/" + idType,
          data: dataString,
	  beforeSend: function() {
            $(".updateItem").attr("disabled", "true"); 
            $(".updateItem").fadeTo('slow',.5);
            $(".updateWaitlist").prepend('<div class="loader"></div>');
          },
          success: function () {
		$(".loader").remove();
		$(".updateItem").removeAttr("disabled");
		$(".updateItem").fadeTo('slow',1);
		$("#waitlist-form").hide();
		$("#alreadyOnWaitlist").show();
	  },
          error: function (jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
          }
        });
    	return false;
  });
  
  $("#companyDescription-form,#email-form,#phone-form,#twitter-form, #behance-form, #website-form, #companyName-form, #fullName-form").submit(function(){
    var itemContainerId = $(this).attr("rel");
    var itemId = {"item":itemContainerId};
    var dataString = $.param(itemId) + '&' + $.param({"data":$(this).serializeArray()});
    var userID = $("#user_id").val();
    var idType = $("#id_type").val();
    var formAction = $("#editaction").attr("value");
    if ($(this).valid() === true) {
      $.ajax({
        type: "POST",
        url: formAction + userID + "/" + idType,
        data: dataString,
        beforeSend: function() {
          $(".updateItem").attr("disabled", "true"); 
          $(".updateItem, .text-input, textarea").fadeTo('slow',.5);
          $(".update").prepend('<div class="loader"></div>');
        },
        success: function () {
          $("#first_name_ro").text($("#firstName").val());
          $("#last_name_ro").text($("#lastName").val());
          if($("#company_description").val()!='') {
            $("#company_description_ro").text($("#company_description").val());
          } else {
            $("#company_description_ro").html('<span class="placeholder">Company Description</span>');
          }
          if($("#twitter").val()!='') {
            $("#twitter_ro").text($("#twitter").val());
          } else {
            $("#twitter_ro").html('<span class="placeholder">Your Twitter account</span>');
          }
          if($("#phone").val()!='') {
            $("#phone_ro").text($("#phone").val());
          } else {
            $("#phone_ro").html('<span class="placeholder">Your phone number</span>');
          }
          if($("#email").val()!='') {
            $("#email_ro").text($("#email").val());
          } else {
            $("#email_ro").html('<span class="placeholder">Your email address</span>');
          }
          if($("#behance").val()!='') {
            $("#behance_ro").text($("#behance").val());
          } else {
            $("#behance_ro").html('<span class="placeholder">Your Behance account</span>');
          }
          if($("#website").val()!='') {
            $("#website_ro").text($("#website").val());
          } else {
            $("#website_ro").html('<span class="placeholder">Your Website</span>');
          }
          if($("#company_name").val()!='') {
            $("#company_name_ro").text($("#company_name").val());
          } else {
            $("#company_name_ro").html('<span class="placeholder">Your company name</span>');
          }
          ToggleEdit(itemContainerId, false);
          $(".updateItem").removeAttr("disabled"); 
          $(".updateItem, .text-input, textarea").fadeTo('slow',1);
          $(".loader").remove();
          $('.editItemContainer').slideUp();
          $('.roItemContainer').slideDown();
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert(errorThrown);
        }
      });
    }
    return false;
  });
  
  $("#becomeAMember").click(function () {
    var dataString = $("#becomeAMemberForm").serialize();
    var formAction = $("#becomeAMemberForm").attr("action");
    $.ajax({
      type: "POST",
      url: formAction,
      data: dataString,
      dataType: "json",
      success: function (returnData) {
        if (returnData['error'] == 'ALREADY_HAS_SUB') {
          alert("You already have a subscription. This page will refresh to display the most recent information.");
          location.href = "/your-account";
        } else if (returnData['error'] == 'INVALID_CC') {
          alert("The billing information is invalid. Please update your billing information below and try again.");
        } else if (returnData['error'] == 'GATEWAY_ERROR') {
          alert("We were unable to create your membership. Please try again.");
        } else if (returnData['error'] == 'GRIND_EXCEPTION') {
          alert("We were unable to create your membership. Please try again.");
        } else if (returnData['success'] == 1) {
          alert("Your membership was successfully created.");
          location.href = "/your-account";
        } else {
          alert("We were unable to create your membership. Please try again.");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("We were unable to create your membership. Please try again.");
      }
    });
    return false;
  });
  $("#addSubscription").click(function () {
    //var button = $(this);
    //var loader = button.next('.loader');
    //button.show();
    //$(button,loader).fadeTo('slow',.5);
    var dataString = $("#becomeAMemberForm").serialize();
    var formAction = $("#becomeAMemberForm").attr("action");
    $.ajax({
      type: "POST",
      url: formAction,
      data: dataString,
      dataType: "json",
      success: function (returnData) {
        //loader.hide();
        if (returnData['error'] == 'ALREADY_HAS_SUB') {
          alert("You already have a subscription. This page will refresh to display the most recent information.");
          location.href = "/your-account";
        } else if (returnData['error'] == 'INVALID_CC') {
          alert("The billing information is invalid. Please update your billing information below and try again.");
        } else if (returnData['error'] == 'GATEWAY_ERROR') {
          alert("We were unable to create your membership. Please try again.");
        } else if (returnData['error'] == 'GRIND_EXCEPTION') {
          alert("We were unable to create your membership. Please try again.");
        } else if (returnData['success'] == 1) {
          //$(button).fadeTo('slow',1);
          $("#daily-membership_description").hide();
          $("#monthly-membership_description").show();
          $("#monthly-canceled").hide();
          $("#monthly-active").show();
          $("#membership_type_ro").text(returnData['subscription']['plan']['name']);
          $("#monthly-membership_cost").text(returnData['cost']);
          $("#becomeAMemberBlock").hide();
          $("#becomeAMemberOptions").hide();
          $("#cancelMemberBlock").show();
          $("#becomeAMemberToggle").unbind('click', disableLink);
          $("#becomeAMemberToggle").removeClass("disableLink");
        } else {
          alert("We were unable to create your membership. Please try again.");
          //loader.hide();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("We were unable to create your membership. Please try again.");
      }
    });
    return false;
  });
  
  $("#cancelSubscription").click(function () {
    $("#cancelSubscriptionOptions").show();
    $("#cancelSubscription").addClass("disableLink");
    $("#cancelSubscription").bind('click', disableLink);
  });
  function disableLink(e) {
    // cancels the event
    e.preventDefault();	
    return false;
	}
 
  $("#becomeAMemberToggle").click(function () {
    $("#becomeAMemberOptions").show();
    $("#becomeAMemberToggle").addClass("disableLink");
    $("#becomeAMemberToggle").bind('click', disableLink);
  });
  $("#cancelSubNow, #cancelSubLater").click(function () {
    //var answer = confirm("Are you sure you want to cancel your monthly subscription?");
    var url = $("#cancelaction").attr("value");
    var cancelWhen = $(this).attr("id") == "cancelSubNow" ? "now" : "later";
    url = url + cancelWhen;
    $.ajax({
      type: "GET",
      url: url,
      dataType: "json",
      success: function (returnData) {
        if (returnData == null) {
          alert("Failed to cancel your subscription. Please try again.");
        } else if (returnData['success'] == 1) {
          $("#daily-membership_description").hide();
          $("#monthly-membership_description").show();
          $("#monthly-canceled").show();
          $("#monthly-active").hide();
          $("#becomeAMemberBlock").hide();
          $("#cancelMemberBlock").hide();
          $("#cancelSubscriptionOptions").hide();
          $("#membership_type_ro").text("Daily Membership");
          $("#cancelSubscription").removeClass("disableLink");
          
        } else {
          alert(returnData['message']);
          alert("Failed to cancel your subscription. Please try again.");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert(textStatus);
        alert("Failed to cancel. Please try again.");
      }
    });
    return false;
  });
  $("#password-form").submit(function () {
    var dataString = $("#password-form").serialize();
    var itemContainerId = $(this).attr("rel");
    $.post("/grind-update-pw.php", dataString, function (data) {
      alert(data);
    });
    return false;
  }); /* Conference Rooms */
  if ($("#requestRoom").length > 0) {
    $('#calendarLink').popupWindow({ height:600, width:800 });
    Date.firstDayOfWeek = 0;
    Date.format = 'mm/dd/yyyy';
    $('.date-pick').datePicker({
      clickInput: true
    });
    $('#start-date').bind(
			'dateSelected',
			function(e, selectedDate, $td)
			{
				$('#start-date, #end-date').val(selectedDate.asString());
				$('#end-date').dpSetStartDate(selectedDate.asString());
			}
		);
		$("#time1, #time2").timePicker({
		  show24Hours: false,
		  separator:':',
		  step: 15
		});		
		var oldTime = $.timePicker("#time1").getTime();
    $("#time1").change(function() {
      if ($("#time2").val()) {
        var duration = ($.timePicker("#time2").getTime() - oldTime);
        var time = $.timePicker("#time1").getTime();
        $.timePicker("#time2").setTime(new Date(new Date(time.getTime() + duration)));
        oldTime = time;
      }
    });
    $("#time2").change(function() {
      if($.timePicker("#time1").getTime() > $.timePicker(this).getTime()) {
        $(this).addClass("error");
        $("#formSubmit").attr("disabled",true);
      }
      else {
        $(this).removeClass("error");
        $("#formSubmit").removeAttr("disabled");
      }
    });
    var conferenceRooms = location_data;
      if (conferenceRooms.length > 0) {
        var locationsAdded = new Array();
        //$("#form-location").append('<option value="-1">Choose a Grind location</option>');
        $.each(conferenceRooms, function (i, space) {
          if ($.inArray(space.location_id, locationsAdded) === -1) {
            locationsAdded.push(space.location_id);
            $("#form-location").append('<option value="' + space.location_id + '">' + space.location_name + '</option>');
          }
        });
        if (locationsAdded.length == 1) {
          $("#form-location").val(locationsAdded[0]);
          $("#form-location").change();
        } else {
          $("#form-location-wrapper").show();
          $("#space-wrapper").show();
        }
      };
    
    $("#form-location").change(function () {
      var $selectedVal = $(this).val();
      if ($selectedVal === "") {
        $('#space').find('option').remove().end().append('<option value="-1" selected>Choose a conference room</option>');
        $("#calendarLink").hide();
      } else {
        $('#space').find('option').remove().end().append('<option value="-1" selected>Choose a conference room</option>');
        $.each(conferenceRooms, function (i, space) {
          if (space.location_id == $selectedVal) {
            $('#space').append('<option value="' + space.space_id + '">' + space.space_name + '</option>');
          }
        });
        $("#calendarLink").hide();
      }
      $("#space-wrapper").show();
    });
    $("#space").change(function () {
      var $selectedVal = $(this).val();
      if ($selectedVal === "") {
        $("#calendarLink").hide();
      } else {
        $.each(conferenceRooms, function (i, space) {
          if ($selectedVal === space.space_id) {
            $("#calendarLink").attr("href", space.calendar_link);
            $("#calendarLink").show();
            return;
          }
        });
      }
      return false;
    });
    $("#requestRoom").submit(function () {
      var valid = true;
      if ($('#space').val() === "-1") {
        $('#space').addClass('error');
        valid = false;
      }
      if (valid) {
        var dataString = $("#requestRoom").serialize();
        var formAction = $("#requestRoom").attr("action");
        $.ajax({
          type: "POST",
          url: formAction,
          data: dataString,
          dataType: "text",
          beforeSend: function() {
            $("#formSubmit").attr("disabled", true);
            $(".loader").show();
          },
          success: function (returnData) {
            if (returnData == 1) {
              $(".room").text($("#space option:selected").text());
              var startDate = $("#start-date").val();
              var endDate = $("#end-date").val();
              $(".start-date").text(startDate);
              $(".end-date").text(endDate);
              $(".time1").text($('#time1').val());
              $(".time2").text($('#time2').val());
              if (startDate != endDate) {
                $('.msg2').show();
              } else {
                $('.msg1').show();
              }
              location.href = "#";
              $("#conferenceRoomSuccess").slideDown();
              $("#requestRoom").slideUp();
            } else {
              alert("Failed to send your request. Please try again.");
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert("Failed to send your request. Please try again.");
            $(".loader").hide();
            $("#formSubmit").removeAttr("disabled");
          }
        });
      }
      return false;
    });
  } /* Send Help Form */
  if ($("#sendHelpFormBlock").length > 0) {
    $("#sendHelp").click(function (e) {
      e.preventDefault();
      $('#message').val("").removeClass('error');
      $("#sendHelpSuccess").hide();
      $("#sendHelpFormBlock").slideDown(500);
    });
    $("#cancelSendHelpForm").click(function (e) {
      e.preventDefault();
      $('#message').val("").removeClass('error');
      $("#sendHelpFormBlock").slideUp(500);
    });
    $("#sendHelpForm").submit(function () {
      var valid = true;
      $('#message').removeClass('error');
      if ($('#message').val().length == 0) {
        $('#message').addClass('error');
        valid = false;
      }
      if (valid) {
        $("#sendHelpForm").attr("disabled", "true");
        var dataString = $("#sendHelpForm").serialize();
        var formAction = $("#sendHelpForm").attr("action");
        $.ajax({
          type: "POST",
          url: formAction,
          data: dataString,
          dataType: "text",
          beforeSend: function() {
            $("#sendHelpForm .loader").show();
          	$("#sendHelpForm textarea, #sendHelpFormSubmit").fadeTo('slow',.5);
            $("#sendHelpFormSubmit").attr("disabled", true);
          },
          success: function (returnData) {
            $("#sendHelpForm textarea, #sendHelpFormSubmit").fadeTo('slow',1);
            $("#sendHelpForm .loader").hide();
            $("#sendHelpFormSubmit").removeAttr("disabled");
            if (returnData == 1) {
              $("#sendHelpFormBlock").fadeOut(500);
              $("#sendHelpSuccess").fadeIn(500);
            } else {
              alert("Sorry something went wrong, Please try to submit your question again.");
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            alert("Sorry something went wrong, Please try to submit your question again.");
          }
        });
      }
      return false;
    });
  }

	/**
	  * @author:  @mattkosoy
	  *	@descrip:  Rounded corners on the Agora section
	*/

	/* http://www.swedishfika.com/2010/03/19/rounded-corners-on-images-with-css3-2/ */
	function hasBorderRadius() {
	  var d = document.createElement("div").style;
	  if (typeof d.borderRadius !== "undefined") return true;
	  if (typeof d.WebkitBorderRadius !== "undefined") return true;
	  if (typeof d.MozBorderRadius !== "undefined") return true;
	  return false;
	};  
 
	if (hasBorderRadius()) {
	  $("ul.agora li").each(function(){
	  	bRadius(this);
	  });
	  
	  $(".myAvatar").each(function(){
	  	bRadius(this);
	  });
	}

	/* agora "who's here" slideshow on homepage 
	$("#whos_here_now .agora").jCarouselLite({
	  btnPrev: $(".prev-agora"),
	  btnNext: $(".next-agora"),
	  easing: "easeOutExpo"
	});*/
  
  	function bRadius(el){
		var img = $(el).children("img");
		var imgSrc = img.attr("src");
		var imgHeight = img.height();
		var imgWidth = img.width();
		$(el).css("background-image", "url(" + imgSrc + ")").css("background-repeat","no-repeat").css("padding-top", imgHeight + "px");
		img.remove();
  	}	
});