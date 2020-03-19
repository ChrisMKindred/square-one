<?php
declare( strict_types=1 );

namespace Tribe\Project\Templates\Controllers\Content\Loop;

use Tribe\Project\Templates\Abstract_Template;
use Tribe\Project\Theme\Image_Sizes;

class Item extends Abstract_Template {
	protected $path = 'content/loop/item.twig';

	protected $time_formats = [
		'c',
		'F j, Y',
	];

	public function get_data(): array {
		$data['post'] = [
			'post_type'      => get_post_type(),
			'title'          => get_the_title(),
			'content'        => apply_filters( 'the_content', get_the_content() ),
			'excerpt'        => apply_filters( 'the_excerpt', get_the_excerpt() ),
			'permalink'      => get_the_permalink(),
			'featured_image' => $this->get_featured_image(),
			'time'           => $this->get_time(),
			'author'         => $this->get_author(),
		];

		return $data;
	}

	protected function get_featured_image() {
		$options = [
			'as_bg'         => true,
			'wrapper_class' => 'item__image',
			'shim'          => trailingslashit( get_stylesheet_directory_uri() ) . 'img/theme/shims/16x9.png',
			'src_size'      => Image_Sizes::CORE_FULL,
			'srcset_sizes'  => [
				Image_Sizes::CORE_FULL,
				Image_Sizes::CORE_MOBILE,
				Image_Sizes::SOCIAL_SHARE,
			],
		];

		return the_tribe_image( get_post_thumbnail_id(), $options );
	}

	protected function get_time() {
		$times = [];
		foreach ( $this->time_formats as $format ) {
			$times[ $format ] = get_the_time( $format );
		}

		return $times;
	}

	protected function get_author() {
		return [
			'name' => get_the_author(),
			'url'  => get_author_posts_url( get_the_author_meta( 'ID' ) ),
		];
	}
}
