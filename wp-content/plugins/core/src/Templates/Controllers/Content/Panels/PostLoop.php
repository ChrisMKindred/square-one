<?php

namespace Tribe\Project\Templates\Controllers\Content\Panels;

use Tribe\Project\Panels\Types\PostLoop as PostLoopPanel;
use Tribe\Project\Templates\Components\Button;
use Tribe\Project\Templates\Components\Card;
use Tribe\Project\Templates\Components\Image;
use Tribe\Project\Templates\Components\Title;
use Tribe\Project\Theme\Image_Sizes;

class PostLoop extends Panel {
	protected $path = 'content/panels/postloop.twig';

	public function get_data(): array {
		$data       = parent::get_data();
		$panel_data = $this->get_mapped_panel_data();
		$data       = array_merge( $data, $panel_data );

		return $data;
	}

	public function get_mapped_panel_data(): array {
		$data = [
			'title' => $this->get_title( $this->panel_vars[ PostLoopPanel::FIELD_TITLE ], [ 's-title', 'h2' ] ),
			'posts' => $this->get_posts(),
			'attrs' => $this->get_postloop_attributes(),
		];

		return $data;
	}

	protected function get_postloop_attributes() {
		$attrs = '';

		if ( is_panel_preview() ) {
			$attrs = 'data-depth=' . $this->panel->get_depth() . ' data-name="' . PostLoopPanel::FIELD_POSTS . '" data-index="0" data-livetext="true"';
		}

		if ( empty( $attrs ) ) {
			return '';
		}

		return $attrs;
	}

	protected function get_posts(): array {
		$posts = [];

		if ( ! empty( $this->panel_vars[ PostLoopPanel::FIELD_POSTS ] ) ) {
			for ( $i = 0; $i < count( $this->panel_vars[ PostLoopPanel::FIELD_POSTS ] ); $i ++ ) {

				$post = $this->panel_vars[ PostLoopPanel::FIELD_POSTS ][ $i ];

				$options = [
					Card::POST_TITLE => $this->get_post_title( esc_html( $post['title'] ), $i ),
					Card::IMAGE      => $this->get_post_image( $post['image'] ),
					Card::PRE_TITLE  => get_the_category_list( '', '', $post['post_id'] ),
					Card::BUTTON     => $this->get_post_button( $post['link'] ),
				];

				$posts[] = $this->factory->get( Card::class, $options )->render();
			}
		}

		return $posts;
	}

	protected function get_post_title( $title, $index ) {
		$attrs = [];

		if ( is_panel_preview() ) {
			$attrs = [
				'data-depth'    => $this->panel->get_depth(),
				'data-name'     => $title,
				'data-index'    => $index,
				'data-livetext' => true,
			];
		}

		$options = [
			Title::TITLE   => $title,
			Title::CLASSES => [],
			Title::ATTRS   => $attrs,
			Title::TAG     => 'h5',
		];

		return $this->factory->get( Title::class, $options )->render();
	}

	protected function get_post_image( $image_id ) {
		if ( empty( $image_id ) ) {
			return '';
		}

		try {
			$image = \Tribe\Project\Templates\Models\Image::factory( $image_id );
		} catch ( \Exception $e ) {
			return '';
		}

		$options = [
			Image::ATTACHMENT   => $image,
			Image::AS_BG        => false,
			Image::USE_LAZYLOAD => false,
			Image::SRC_SIZE     => Image_Sizes::COMPONENT_CARD,
		];

		return $this->factory->get( Image::class, $options )->render();
	}

	protected function get_post_button( $post_link ) {
		$options = [
			Button::URL         => esc_url( $post_link['url'] ),
			Button::LABEL       => __( 'View Post', 'tribe' ),
			Button::TARGET      => '_self',
			Button::BTN_AS_LINK => true,
			Button::CLASSES     => [ 'c-cta' ],
		];

		return $this->factory->get( Button::class, $options )->render();
	}
}
