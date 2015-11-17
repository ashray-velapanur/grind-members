$(document).ready(function(){
	$("#submit").click(function(){
	  var formdata = $(form).serialize();
	
	
	
	$.ajax({
	   type: "GET",
	   
	   url: "/ci/request_room.php",
	   data: formdata,
	   success: function(msg){
	  	 $('.success').fadeIn(200).show();
	    // alert( "Data Saved: " + formdata );
	   } //close success
	 });//close ajax
	  
	   return false;
	 }); //close submit
	 
});// end document ready

