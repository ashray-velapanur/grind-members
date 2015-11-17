	<?php
/**
 * Template Name: Conference Room
 */
$CI =& get_instance();
$CI->load->model("spacemodel");
$allSpaces = $CI->spacemodel->getSpaceList();
$account = get_option('ss_account_name');
$password = $password = get_option('ss_password');
$after = $after = get_option('ss_schedule');
global $current_user;
$username = $current_user->user_login;
$checksum = md5("$account$password$username");
$url = 'https://www.supersaas.com/api/login';
$src = "$url?account=$account&after=$after&user[name]=$username&checksum=$checksum";
get_header();
?>
	<script type="text/javascript">
    $(document).ready(function(){
        $(".location-option").click(function(){
            $('.space-select').addClass('active');
            $('.location-option').removeClass('active');
            $(this).addClass('active');
            var location = $(this).data('location');
            
            $('.space-group').each(function(){
                if($(this).data('location') == location)
                    $(this).fadeIn();
                else
                    $(this).hide();
            });
            
        });
        
        $('.space-select').click(function(){
            $('.space-select').removeClass('active');
            $(this).addClass('active');
            var src = $(this).data('schedule');
            $("#ss_iframe").attr('src',src);
            
            $("html, body").animate({ scrollTop: ($("#ss_iframe").offset().top - 100) }, 200);
        });
    });
    </script>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		  
                <a name="top"></a>
				<h1><?php the_title(); ?></h1>
				<hr class="pagehead" />
			

                <div class="locations">
                    
                    <?php foreach($allSpaces as $location => $spaces): ?>

                        <div data-location="<?php echo $location; ?>" class="location-option active"><?php echo $location; ?></div>
                        
                    <?php endforeach; ?>
                    
                </div>
                
                <?php foreach($allSpaces as $location => $spaces): ?>
                    <div class="space-group <?php echo strtolower(substr($location, 0,3)); ?>" data-location="<?php echo $location; ?>">
                        <h2>Select a Room</h2>
                        <?php foreach($spaces as $id => $space): ?>
                            <div class="space-select active <?php echo strtolower(str_replace(' ','-',$space['name'])); ?>" data-schedule="<?php echo htmlspecialchars(str_replace(' ', '_', $space['ss_schedule'])); ?>">
                                <h3>Book the <?php echo $space['name']; ?></h3>
                                <span>
                                    <?php echo $space['description']; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>   
                    </div> 
                <?php endforeach; ?>
                
                <div class="return-link">
                    <a href="#top">Back to top</a>
                </div>
                <hr class="pagehead"/>
                <iframe id="ss_iframe" frameborder="0" width="800px" height="1100px" src=""></iframe>
                <iframe id="ss_iframe_pixel" frameborder="0" width="1px" height="1px" src="<?php echo $src; ?>"></iframe>
				
		<?php endwhile; ?>

<?php get_footer(); ?>

