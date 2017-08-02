<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			
			<div class="container" >
			    <ul class="chatbox" id="chatbox">
			      	<?php 
			      	    if(get_current_user_id()=="0"){
			      	    	echo "<h2><center>Login to join chat room</center></h2>";
			      	    }
			      	?>
			    </ul>
			    <br/>
			    <div id="name-group" class="form-group">
			        <textarea class="form-control msg_box" id="msg_box" placeholder="Type here and check the Title in Tab"></textarea>
			    </div>
			</div>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
