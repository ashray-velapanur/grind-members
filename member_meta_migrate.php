<?	

/*

#  this script was used to migrate data from the 'user' table into 'wp_usermeta'.   this is/was for the Agora project 




require_once('wp-load.php' ); 
global $wpdb;

$get_user_data_sql = "select
        user.id as id, user.first_name, user.last_name, user.rfid, user.wp_users_id,user.twitter,user.behance, user.membership_status_luid,
        wpmember_users.user_login,
        wpmember_users.user_url as website,
    	email.id as 'email_id',
        email.address as 'email',
    	phone.id as phone_id,
    	phone.number as phone,
    	company.id as 'company_id',
        company.name as 'company_name',
    	company.description as 'company_description',
    	subscription_sync.plan_code as 'plan_code',
    	subscription_sync.state as 'subscription_state',
    	subscription_sync.total_amount_in_cents as 'total_amount_in_cents',
    	subscription_sync.current_period_ends_at as 'current_period_ends_at'
    	
    	from 
    	        user 
    	        left outer join wpmember_users on wpmember_users.ID = user.wp_users_id
    	        left outer join email on email.user_id = user.id and email.is_primary = 1
    	        left outer join phone on phone.user_id = user.id and phone.is_primary = 1
    	        left outer join company on company.id = user.company_id
    	        left outer join subscription_sync on subscription_sync.user_id = user.id ";
    	        

$users = $wpdb->get_results($get_user_data_sql);



foreach($users as $u){

	echo "<pre>";
	print_r($u);
	echo "</pre>";
	echo "<hr/>";

	update_user_meta( $u->wp_users_id, 'first_name', $u->first_name );
	update_user_meta( $u->wp_users_id, 'last_name', $u->last_name );
	update_user_meta( $u->wp_users_id, 'company_name', $u->company_name );
	update_user_meta( $u->wp_users_id, 'company_desc', $u->company_description );
	update_user_meta( $u->wp_users_id, 'email', $u->email );
	update_user_meta( $u->wp_users_id, 'phone', $u->phone );
	update_user_meta( $u->wp_users_id, 'twitter', $u->twitter );
	update_user_meta( $u->wp_users_id, 'behance', $u->behance );
	update_user_meta( $u->wp_users_id, 'URL', $u->website );


}
*/



exit();
?>
