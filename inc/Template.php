<?php

namespace JVH\VcTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Template
{
	private $post_id;

	public $data;

	public function __construct( $post_id )
	{
		$this->post_id = $post_id;
		$this->data = $this->getData();
	}

	public function getData()
	{
		return [
			'name' => $this->getName(),
			'weight' => 0,
			'type' => 'default_templates',
			'category' => 'default_templates',
			'image_path' => $this->getImagePath(),
			'custom_class' => $this->getClass(),
			'content' => $this->getContent(),
		];
	}

	private function getName()
	{
		return get_the_title( $this->post_id );
	}

	private function getContent()
	{
		return get_post_field( 'post_content', $this->post_id );
	}

	private function getImagePath()
	{
		return get_the_post_thumbnail_url( $this->post_id );
	}

	private function getClass()
	{
		$class = 'custom_template_for_vc_custom_template all jvh_template ';
		$class .= implode( ' ', $this->getCategorySlugs() );

		return $class;
	}

	private function getCategorySlugs() 
	{
		$slugs = [];

		$categories = $this->getCategoryTerms();

		if ( ! is_array( $categories ) ) {
			return $slugs;
		}

		foreach ( $categories as $term ) {
			$slugs[] = $term->slug;
		}

		return $slugs;
	}

	private function getCategoryTerms()
	{
		return get_the_terms( $this->post_id, 'category-jvh-template' );
	}

	public function getSlug()
	{
		$slug = esc_html( $this->data['name'] );

		if ( function_exists( 'vc_slugify' ) ) {
			$slug = esc_attr( vc_slugify( $slug ) );
		}

		return $slug;
	}
}
