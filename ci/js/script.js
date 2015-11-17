$(function() {

  if ($.browser.webkit) {
    $("html").addClass("webkit");
  }
  
  $('#q').smartSuggest({
    src: 'ci/admin/usermanagement/searchUsers'
  });
  
  /* focus + blur treatment on input boxes */
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
  
  var showText='Show Answer';
  var hideText='Hide Answer';
  var is_visible = false;
  $('.showAnswer').append(' <a href="#" class="toggleLink">'+showText+'</a>');

	// hide all of the elements with a class of 'toggle'
	$('.toggle').hide();
	
	// capture clicks on the toggle links
	$('a.toggleLink').click(function() {
	
	// switch visibility
	is_visible = !is_visible;
	
	// change the link depending on whether the element is shown or hidden
	$(this).html( (!is_visible) ? showText : hideText);
	if(is_visible){
		$(this).parent().parent().addClass("open");
	} else {
		$(this).parent().parent().removeClass("open");
	}
	
	var detailrow = '#detail'+$(this).parent().parent().attr('id');
	
	// toggle the display - 
	$(detailrow).slideToggle('0.5');
	
	// return false so any link destination is not followed
	return false;
	
	});
  /*
  function showdetails(rowid){
  	var detailrow = '#detail'+rowid;
    $(detailrow).show();
  }
 */
 /* $("#showdetails").click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    var detailrow = $(this).attr('rel');
    alert(detailrow);
//    $(this).addClass("open");
//    $("#location-menu").slideDown(840, 'easeOutExpo');
  });
  */
  /* Password Reset control */
  
  $("#pwdchange").click(function() {
     	$("#pre-pw-reset,#post-pw-reset").hide();
     	$("#during-pw-reset").show();
      return false;
  });
  
  $("#btn-pw-cancel").click(function() {
     	$("#pre-pw-reset").show();
     	$("#during-pw-reset,#post-pw-reset").hide();
     	
      return false;
  });
  
  $("#btn-pw-reset").click(function() {
  	  var userID = $("#user_id").val();
  	  var dataString = "";
  	  var formAction = $("#resetaction").attr("value");
	  $.ajax({
	    type: "POST",
	    url: formAction+userID,
	    data: dataString,
	    dataType: "json",
	    success: function (returnData) {
	    if (returnData['success'] == 1) {
	        $("#pre-pw-reset,#post-pw-reset").show();
	        $("#during-pw-reset").hide();
	         return false;
	      } else {
	        alert("We were unable to reset the password, please try again.");
	         return false;
	      }
	    },
	    error: function (jqXHR, textStatus, errorThrown) {
	      alert("We were unable to reset the password, please try again.");
	    }
	  });
     return false;
  });
  
  /* focus & blur */
  
  $(".focus, input:password, #q").focusAndBlur();
  
  /* location nav */
  $("#locations a.selected").click(function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).addClass("open");
    $("#location-menu").slideDown(840, 'easeOutExpo');
  });
  $("body").click(function() {
    $("#locations a.selected").removeClass("open");
    $("#location-menu").slideUp(840, 'easeOutExpo');
  });
  
  $('.applicantConfirm').click(function(){
      var r=confirm("Are you sure?")
      if (r==false)
      {
          return false;
      }
      return true;
  });
  
  $('#applicantStatuses').change(function() {
      window.location.href =  $("#changeaction").attr("value") + $("#applicantStatuses").val();
  });
  
  $('.action a').click(function(e){
	  e.preventDefault();
	  var response_txt;
	  var $this = $(this);
	  $this.parent().append('<div class="loader"></div>');
	  $.ajax({
		  dataType: 'json',
		  url: $this.attr('href'),
		  success:function(data){
			  console.log(data.success);
			  if(data.success){
				  if(data.type == "approve"){
					  response_txt = "Approved";
				  }
				  if(data.type == "delete"){
					  response_txt = "Deleted";
				  }
				  if(data.type == "deny"){
					  response_txt = "Denied";
				  }
				  $this.parent().parent().delay(1000).fadeOut();
				  $this.parent().html(response_txt);
				  
			  }
		  }
	  });
  });

});