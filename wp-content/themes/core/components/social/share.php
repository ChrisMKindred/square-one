<aside class="social-share">

	<h6>Share This</h6>

	<?php
	$social = new \Tribe\Project\Theme\Social_Links( [ 'facebook', 'twitter', 'google', 'linkedin', 'email' ], false );
	echo $social->format_links( $social->get_links() );
	?>

</aside>
