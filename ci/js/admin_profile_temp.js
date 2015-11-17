$(function() {
	function ToggleEdit(itemContainerId, turnOnEdit) {
	  
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
	
	$("#membership-form").submit(function(){
	  var itemContainerId = $(this).attr("rel");
	  var dataString = $("#membership-form").serialize();;
	  var userID = $("#user_id").val();
	  var idType = $("#id_type").val();
      var formAction = $("#membership-form").attr("action");
	  if ($(this).valid() === true) {
	    $.ajax({
	      type: "POST",
	      url: formAction + userID,
	      data: dataString,
	      dataType: "json",
	      beforeSend: function() {
	        $(".updateItem").attr("disabled", "true"); 
	        $(".updateItem, .text-input, textarea").fadeTo('slow',.5);
	        $(".update").prepend('<div class="loader"></div>');
	      },
	      success: function (returnData) {
	      	
	        if (returnData['error'] == 'ALREADY_HAS_SUB') {
         	 alert("duplicate membership, refresh the page");
        	} else if (returnData['error'] == 'INVALID_CC') {
         	 alert("The billing information is invalid. Please update your billing information first and try again.");
      		} else if (returnData['error'] == 'GATEWAY_ERROR') {
         	 alert("We were unable to create the membership. Please try again.");
        	} else if (returnData['error'] == 'GRIND_EXCEPTION') {
        		alert("We were unable to create the membership. Please try again.");
        	} else if (returnData['nochange'] == 1) {
          		alert("This is the same membership the member already has.");  	 
       		} else if (returnData['success'] == 1) {
       			// we are reloading the page because of the
       			// potential for pending changes
       			window.location.reload();
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
    	
	$("#waitlist-form,#companyDescription-form,#rfid-form,#email-form,#phone-form,#twitter-form, #behance-form, #location_id-form, #website-form, #companyName-form, #fullName-form").submit(function(){
	  var itemContainerId = $(this).attr("rel");
	  var itemId = {"item":itemContainerId};
	  var dataString = $.param(itemId) + '&' + $.param({"data":$(this).serializeArray()});
	  console.log(dataString);
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
	          $("#twitter_ro").html('<span class="placeholder">Twitter account</span>');
	        }
	        if($("#phone").val()!='') {
	          $("#phone_ro").text($("#phone").val());
	        } else {
	          $("#phone_ro").html('<span class="placeholder">Phone number</span>');
	        }
	        if($("#email").val()!='') {
            	$("#email_ro").text($("#email").val());
        	} else {
            	$("#email_ro").html('<span class="placeholder">Email address</span>');
          	}
          	if($("#rfid").val()!='') {
            	$("#rfid_ro").text( "RFID Card #: "+ $("#rfid").val());
        	} else {
            	$("#email_ro").html('<span class="placeholder">Member RFID/PIN</span>');
          	}
	        if($("#behance").val()!='') {
	          $("#behance_ro").text($("#behance").val());
	        } else {
	          $("#behance_ro").html('<span class="placeholder">Behance account</span>');
	        }
	        if($("#location_id").val()!='') {
              $("#location_id_ro").text($("#location_id option:selected").text());
            } else {
              $("#location_id_ro").html('<span class="placeholder">Member location</span>');
            }
	        if($("#website").val()!='') {
	          $("#website_ro").text($("#website").val());
	        } else {
	          $("#website_ro").html('<span class="placeholder">Website</span>');
	        }
	        if($("#company_name").val()!='') {
	          $("#company_name_ro").text($("#company_name").val());
	        } else {
	          $("#company_name_ro").html('<span class="placeholder">Company name</span>');
	        }
			if($("#waitlist").is(":checked")) {
	          $("#waitlist_ro").html("<span>Member is on waitlist</span>");
	        } else {
	          $("#waitlist_ro").html('<span class="placeholder">Memberships on hold, add member to waitlist?</span>');
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
});