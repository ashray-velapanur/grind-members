<?php
/**
 * Template Name: Help
 * Used for the FAQ/Member Help Page
 */
?>
<?php get_header(); session_start(); ?>
	<?php query_posts('category_name=help&posts_per_page=10'); ?>
		<?php 
		$page_id = "249"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
		$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

		$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
		$title = $page_data->post_title; // Get title
		?>
			<h1><?= $title ?></h1>

			<div id="instructions">
		  		<?= $content ?>
		    </div>		
		  <hr class="pagehead">
		
  		<div id="sendHelpSuccess">
  				<?php 
				$page_id = "279"; // 123 should be replaced with a specific Page's id from your site, which you can find by mousing over the link to edit that Page on the Manage Pages admin page. The id will be embedded in the query string of the URL, e.g. page.php?action=edit&post=123.
				$page_data = get_page( $page_id ); // You must pass in a variable to the get_page function. If you pass in a value (e.g. get_page ( 123 ); ), WordPress will generate an error. 

				$content = apply_filters('the_content', $page_data->post_content); // Get Content and retain Wordpress filters such as paragraph tags. Origin from: http://wordpress.org/support/topic/get_pagepost-and-no-paragraphs-problem
				echo $content;
				?>
  		</div>
			
  		<div id="sendHelpFormBlock">
  			<form id="sendHelpForm" class="grey-fields" method="post" >
  				<div class="comments">
  					<textarea id="message" name="message"></textarea>
  				</div>
  				<div class="clearfix">
  					<div class="col">
  						<a id="cancelSendHelpForm">Cancel</a>
  					</div>
  					<div class="col colright">
  					  <div class="loader"></div>
  						<input type="submit" class="btn" value="Send" id="sendHelpFormSubmit"/>
  					</div>
  				</div>
  			</form>
  		</div>
  		
  		<div id="section">
  			<div id="topics">
  				<?php $term = get_term_by('name', 'Help', 'category');?>
				<h3>Topics</h3>
				<ul>
				  <?php wp_list_categories('title_li=&child_of='.$term->term_id); ?>
				</ul>
  			</div>
  			<div id="qa">
  				<h3>Questions</h3>
  					<ul>
  						<?php while (have_posts()) : the_post(); ?>
  							<li class="question"><a href="#<?php echo $post->ID; ?>"><?php the_title(); ?></a></li>
  						<?php endwhile;?>
  					</ul>
  				
  				
  				<!--ANSWERS BELOW -->
  				<h3>Answers</h3>
  				<?php rewind_posts(); ?>
  				<?php while (have_posts()) : the_post(); ?>
  					<div class="faq">
  						<a id="<?php echo $post->ID; ?>"></a><h5 class="question"><?php the_title(); ?></h5>
  						<div class="answer">
  							<?php the_content(); ?>
  							<div class="backToTop"><a href="#" title="Back to Top">Back to Questions</a></div>
  						</div>
  					</div>
  				<?php endwhile;?>
  			</div>
  		</div>
        
<?php get_footer(); ?>
