<?php
/**
 * The template for displaying all pages.
 */

get_header(); ?>
	
<?php get_sidebar(); ?>
<!--
function(data){
  var arr = new Array();
  $.each(data.people, function(i, person){
    if (jQuery.inArray(person.birthDate, arr) === -1) {
      alert(person.birthDate);
      arr.push(person.birthDate);
    }
  });
}
-->

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<h2><?php the_title(); ?></h2>

<?php the_content(); ?>

<?php endwhile; ?>


<?php get_footer(); ?>

