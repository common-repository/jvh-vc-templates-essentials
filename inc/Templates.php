<?php

namespace JVH\VcTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Templates
{
	public function getTemplates()
	{
		$templates = [];

		foreach ( $this->getTemplatePosts() as $post ) {
			$templates[] = new \JVH\VcTemplates\Template( $post->ID );
		}

		return $templates;
	}

	public function getImages()
	{
		$images = [];

		foreach ( $this->getTemplates() as $template ) {
			$images[$template->getSlug()] = $template->data['image_path'];
		}

		return $images;
	}

	private function getTemplatePosts()
	{
		$the_query = new \WP_Query([
			'posts_per_page' => -1,
			'post_type' => 'jvh-vc-template',
		]);

		return $the_query->posts;
	}
}
