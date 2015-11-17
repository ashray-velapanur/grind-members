<?php
/**
 * Template Name: Profile Page
 */

global $current_user, $user_ID, $wp_roles;
get_currentuserinfo();
get_header();

$monthlyPlan = GetMonthlyPlan();
$dailyRate = GetDailyRate();

?>
<input type="hidden" id="pwhash" name="pwhash" value="<?php echo $current_user->user_pass;?>"/>

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
			
			<?php if(isset($_SESSION['membershipsuccess'])) {?>
				<div class="membershipSuccess">
					<?php 
					$page_id = "282"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
					$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

					$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
					echo $content;
					

					?>
				</div>
			<?php } elseif (get_user_meta($user_ID,'admin_init',true)) {?>
				<div class="membershipSuccess">
					<?php 
					$page_id = "282"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
					$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

					$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
					echo $content;
					?>
				</div>
				
			<?php 
						} 
			
			?>
			<div class="instruction"><?php the_content(); ?></div>
			
			<hr class="pagehead">
			
			<?php
			if ($current_user->user_login=="admin"){
				echo "Silly Administrator, you don't have a profile to view.";
			} else {
				$CI =& get_instance();
				$CI->load->helper('date');
				setlocale(LC_MONETARY, 'en_US');
				$CI->load->helper("url");
				$CI->load->helper("form");
				$CI->load->helper("html");
				
				$CI->load->helper('form'); 
				$CI->load->model("members/membermodel", "", true);
				$data = $CI->membermodel->viewProfile($user_ID,UserIdType::WORDPRESSID);
		
				$billing["billing_info"]=$data["member"]->billing_info;
				$billing["member"]=$data["member"];
				if (isset($data["member"]->billing_info)){
					$billing["countries_dropdown"] = form_countries( "billing_info[country]",$billing["billing_info"]->country,array("id"=>"billing_info_country"));
					} else {
					$billing["countries_dropdown"] = form_countries( "billing_info[country]",'US',array("id"=>"billing_info_country"));
				}
		
				$data["membershipsuccess"] = isset($_SESSION['membershipsuccess']);
				$data["billing_form"]= $CI->load->view("members/billing_form",
				$billing,true);		
		
				$CI->load->model("locationmodel", "", true);
				$accesspricing = $CI->locationmodel->getAccessPricing();
				$data["allow_monthly"]=$accesspricing[0]->allow_monthly_memberships;
		
				//#213 - Waitlist
				$data["waitlist"]=get_user_meta($data['member']->wp_users_id,'waitlist', true);
		        
            	echo $CI->load->view("members/profile", $data,true);

				//M+M We've moved the code above in from a CI controller which was access as shown below.
				//$content = file_get_contents(site_url("/ci/members/profile/view/".$user_ID."/".UserIdType::WORDPRESSID)."/".isset($_SESSION['membershipsuccess']));
				//echo $content;
			}
			?>
			
	<hr class="pagehead" />
	<?php get_template_part( 'reservations', 'personal' ); ?>	
	
	<?php endwhile; ?>

<?php get_footer(); ?>
<script type="text/javascript">
 $(document).ready(function(){
 	 	
 	
 /*
    $("#password-form").validate({
  		rules: {
	   		password: {
	    		required:true,
	    		minlength:8
	    	},
	   		password_confirm: {
	      		equalTo: "#password"
	    	}
 		 }
	});
	*/
	$("#email-form").validate();
	$("#email-form #email").rules("add", {
		   remote: 	{
			    url: $("#emailcheck").attr("value"),
			    type: "post",
			    data: {
			    	uid: $("#user_id").val()
			    	}				      
			  },
		   messages: {
		     remote: jQuery.format("This email is already taken")
		}
	});
	function updateMembership() {
//		console.log("updatecalled");
	     $.ajax({
	            type: "POST",
	            url: "<?=site_url('/ci/billing/account/plancheck/')?>"+$("#user_id").val(),
	            data: "check membership",
	            dataType: "json",
	            success: function(response) {
	             	console.log(response);
	              $("#monthly-pending").hide();
	              $("#monthly-membership_cost").text(response['cost']);
	   			  $("#monthly_sub_end_date").text(response['date']);
	     		  $("#monthly-active").show();	                	
	            },
	            error: function(jqXHR, textStatus, errorThrown) {
	            	console.log(errorThrown);
	              $("#monthly-pending").hide();
	              $('#monthly-error').show();
	     		  $("#monthly-active").hide();	
	   	        }
	        });
	 }
//	console.log("before");
	updateMembership();
	 
	function resetForm() {
	      $(".loader").hide();
	      $("#submit-billing").removeAttr("disabled")
	      $("#billingInfoBlock, #submit-billing").fadeTo('slow',1);
		  $('.editItemContainer').slideUp();
	      $('.roItemContainer').slideDown();
	 }
	 function reopenForm() {
	      $(".loader").hide();
	      $("#billingInfoBlock, #submit-billing").fadeTo('slow',1);
	      $("#submit-billing").removeAttr("disabled")
	     
	 }
	 //
	    $.validator.addMethod('CCExp', function(value, element, params) {
	      var minMonth = new Date().getMonth() + 1;
	      var minYear = new Date().getFullYear();
	      var month = parseInt($(params.month).val(), 10);
	      var year = parseInt($(params.year).val(), 10);
	      return (year > minYear || (year === minYear && month >= minMonth));
	    }, 'Your Credit Card Expiration date is invalid.');
	    $("#billingInfo-form").validate({
	    	submitHandler: function(form) {
	    		$("#errorMessage").text("");
	    		$("#submit-billing").attr("disabled", "true"); 
	    		var userID = $("#user_id").val();
	    		var formAction = $("#billingInfo-form").attr("action")+ userID;
	       		var str = $("#billingInfo-form").serialize();
	        $.ajax({
	            type: "POST",
	            url: formAction,
	            data: str,
	            dataType: "json",
	            beforeSend: function() {
	              $("#billingInfoBlock, #submit-billing").fadeTo('slow', 0.5);
	              $(".loader").show();
	            },
	            success: function(response) {
	            	console.log(response['error']);
	            	if(response['error']){
	            		console.log("hello");
	              		if (response['error']=='ALREADY_HAS_SUB') {
	              		  window.location.href = "/your-account";
	              		} else if (response['error']=='INVALID_CC') {
	              		  $("#errorMessage").text(response['message']);
	              		  reopenForm();
	              		} else if (response['error']=='GATEWAY_ERROR') {
	              		  $("#errorMessage").text(response['message'] );
	              		  reopenForm();
	              		} else if (response['error']=='GRIND_EXCEPTION') {
	              		  $("#errorMessage").text("We were unable to save your billing information. Please try again.");
	              		  reopenForm();
	              		}
	              } else if (response['account_code'] != null) {
	                 billingData = response;
	   				 $("#credit_card_number").val("");
	   				 $("#credit_card_verification_value").val("");
	   				 $("#cc_type_last_four_ro").text(response['cc_name']+" ending in "+response['credit_card']['last_four']);
	   				$("#billingInfoSection").show();
	                 resetForm();
	     		     
	              } else {
	                $("#errorMessage").text("We were unable to save your billing information. Please try again.");
	                resetForm();
	              }
	            },
	            error: function(jqXHR, textStatus, errorThrown) {
	              alert("We were unable to save your billing information. Please try again");
	              resetForm();
	            }
	        });
	        return false;            
	    	}
	    });
	    $("#credit_card_number").rules("add", { required: true, creditcard: true, 
	      messages: { creditcard: "Enter a valid credit card #." }
	    });
	    $("#credit_card_verification_value").rules("add", { required: true, number: true, minlength: 3 });
	    $("#billing_info_zip").rules("add", { required: true, number: true, minlength: 5 });
	    $("#credit_card_year").rules("add", { CCExp: { month: '#credit_card_month', year: '#credit_card_year' } });
	

});

/* new profile page by @mattkosoy */

/*jQuery Clear-Input plugin v1.0 Author: Aidan Feldman */
(function( $ ){
  // define the initialValue() function
  $.fn.initialValue = function(value) {
    if (value) {
      return this.attr('initial-value', value);
    } else {
      return this.attr('initial-value');
    }
  };
  
  $.fn.clearInput = function() {
    return this
      .focus(function(){
        if (this.value == $(this).initialValue()) {
          this.value = '';
        }
      })
      .blur(function(){
        if (this.value == '') {
          this.value = $(this).initialValue();
        }
      })
      .each(function(index, elt) {
        $(this).initialValue(this.value);
      });
  };
  
 $.fn.animateHighlight = function (highlightColor, duration) {
        var highlightBg = highlightColor || "#FFFF9C";
        var animateMs = duration || 1000;
        var originalBg = this.css("background-color");

        if (!originalBg || originalBg == highlightBg)
            originalBg = "#FFFFFF"; // default to white

        jQuery(this)
            .css("backgroundColor", highlightBg)
            .animate({ backgroundColor: originalBg }, animateMs, null, function () {
                jQuery(this).css("backgroundColor", originalBg); 
            });
    };
  
  
})( jQuery );


var grind;
grind = grind || {
	wpID: '',
	profileInit:function(){
		grind.wpID = $('#wp_id').val();
	$('#memberInfoSection input, #memberInfoSection textarea, #memberSkillSetSection input, #memberSkillSetSection textarea, #memberSocialNetsSection input').each(function(){
		if($(this).hasClass('default')){
			$(this).clearInput();
		}
	});

		$('.chooseImage a.btn').click(function() {
			
			$('input[type=file]#simple-local-avatar').focus().trigger('click').blur().change(function(){
				var label = $('#new_image_filename');
				var new_txt = "You have selected a new avatar.  Press the update button to continue.  If you don't see your image after the page reloads then please press \"refresh\"";
				label.empty().html(new_txt);
				if( label.is(":hidden") ) {
				  label.fadeIn();										
				}
			});
			return false;
		});

		$('#about_me_btn, #skillset_btn, #social_btn').click(function(){
			event.preventDefault();
			$('form#user_profile_information').submit();
		});
		
		
	},

};
/* initialize the usermeta ajax stuff*/
$(document).ready(function(){
	grind.profileInit();
});


</script>
<? unset($_SESSION['membershipsuccess']);?>