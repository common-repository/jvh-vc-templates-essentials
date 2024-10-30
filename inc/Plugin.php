<?php

namespace JVH\VcTemplates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin
{
	public function setup()
	{
		$this->addCpt();
		$this->addCategories();
		$this->addAcfFields();
		$this->setupOverviewPage();
		$this->addNoindex();

		$this->addTemplates();

		$this->enqueueScripts();
		$this->enqueueStyles();
	}

	private function enqueueScripts()
	{
		add_action( 'vc_backend_editor_render', function() {
			wp_enqueue_script( 'sort-vc-templates', plugin_dir_url( __DIR__ ) . '/assets/js/sort-vc-templates.js', ['jquery'], '1.0.0', true );
			wp_enqueue_script( 'add-thumbnails-jvh-vc-templates', plugin_dir_url( __DIR__ ) . '/assets/js/add-thumbnails-jvh-vc-templates.js', ['jquery'], '1.0.0', true );

			$templates = new \JVH\VcTemplates\Templates();

			wp_localize_script( 'add-thumbnails-jvh-vc-templates', 'jvhTemplateThumbs', $templates->getImages() );
		}, 11 );
	}

	private function enqueueStyles()
	{
		add_action( 'admin_enqueue_scripts', function() {
			wp_enqueue_style( 'admin-style', plugin_dir_url( __DIR__ ) . '/assets/css/admin-style.css' );
		} );

		add_action( 'vc_backend_editor_render', function() {
			if ( $this->shouldHideDefaultTemplates() ) {
				wp_enqueue_style( 'hide-essentials-vc-templates', plugin_dir_url( __DIR__ ) . '/assets/css/hide-essentials-vc-template.css' );
			}
		}, 11 );
	}

	private function addCpt()
	{
		add_action( 'init', function() {
			register_post_type('jvh-vc-template', [
				'public'    => true,
				'label'     => 'VC Template Essentials',
				'supports'  => [
					'title',
					'editor',
					'thumbnail',
				],
				'exclude_from_search' => true,
				'labels' => [
					'name'                  => 'VC Templates',
					'singular_name'         => 'VC Template',
					'menu_name'             => 'VC Templates',
					'name_admin_bar'        => 'VC Template',
					'add_new'               => 'Add VC Template',
					'add_new_item'          => 'Add new VC Template',
					'new_item'              => 'New VC Template',
					'edit_item'             => 'Edit VC Template',
					'view_item'             => 'View VC Template',
					'all_items'             => 'All VC Templates',
				],
			]);
		} );
	}

	private function addCategories()
	{
		$this->addCategoryTaxonomy();
		$this->addCategoryTermChoices();
	}

	private function setupOverviewPage()
	{
		$this->addFeaturedImage();
		$this->removeUnnecessaryColumns();
		$this->addCategoryFilter();
	}

	private function addNoindex()
	{
		add_action( 'wp_head', function() {
			if ( get_post_type() === 'jvh-vc-template' ) {
				echo '<meta name="robots" content="noindex">';
			}
		} );
	}

	private function addCategoryFilter()
	{
		add_action( 'restrict_manage_posts', function( $post_type, $which ) {
			if ( 'jvh-vc-template' !== $post_type ) {
				return;
			}

			$terms = get_terms( 'category-jvh-template' );

			echo '<input type="hidden" name="taxonomy" value="category-jvh-template" />';

			echo '<select name="term" class="postform">';
				echo '<option value="">All categories</option>';

				foreach ( $terms as $term ) { 
					$selected = '';

					if ( isset( $_GET['term'] ) && $_GET['term'] === $term->slug ) {
						$selected = 'selected="selected"';
					}

					echo "<option value=\"$term->slug\" $selected>$term->name</option>";
				}
			echo '</select>';
		}, 10, 2);
	}

	private function addAcfFields()
	{
		add_action( 'admin_init', function() {
			if( function_exists('acf_add_local_field_group') ):

				acf_add_local_field_group(array(
					'key' => 'group_61bb6d3d245e3',
					'title' => 'Essentials VC elements',
					'fields' => array(
						array(
							'key' => 'field_61bb6d672726b',
							'label' => 'Show Essentials default VC elements',
							'name' => 'show_essentials_vc_elements',
							'type' => 'true_false',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'dsv_formatting' => array(
								'format' => 'display',
								'disable' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'user_form',
								'operator' => '==',
								'value' => 'all',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				));

			endif;
		} );
	}

	private function addTemplates()
	{
		add_action( 'vc_load_default_templates_action', function() {
			if ( ! function_exists( 'vc_add_default_templates' ) ) {
				return;
			}

			$templates = new \JVH\VcTemplates\Templates();

			foreach ( $templates->getTemplates() as $template ) {
				vc_add_default_templates( $template->data );
			}
		} );
	}

	private function addCategoryTaxonomy()
	{
		add_action( 'init', function() {
			register_taxonomy( 'category-jvh-template', 'jvh-vc-template', [
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
				'show_in_rest'      => false,
				'capabilities'      => [
					'edit_terms'   => 'noone',
					'manage_terms' => 'noone',
				],
				'labels'            => [
					'name'              => 'Categories',
					'singular_name'     => 'Category',
					'view_item'         => 'View category',
					'edit_item'         => 'Edit category',
					'update_item'       => 'Update category',
					'add_new_item'      => 'Add new category',
					'new_item_name'     => 'New category',
					'menu_name'         => 'Category',
				],
			] );
		} );
	}

	private function addCategoryTermChoices()
	{
		add_action( 'init', function() {
			if ( $this->hasCategories() ) {
				return;
			}

			foreach ( $this->getCategories() as $slug => $name ) {
				wp_insert_term( $name, 'category-jvh-template', [ 'slug' => $slug ] );
			}
		} );
	}

	private function addFeaturedImage()
	{
		$this->addFeaturedImageColumn();
		$this->showFeaturedImage();
	}

	private function addFeaturedImageColumn()
	{
		add_filter( 'manage_jvh-vc-template_posts_columns', function( $columns ) {
			$columns['featured_image'] = 'Preview';

			return $columns;
		} );
	}

	private function showFeaturedImage()
	{
		add_action( 'manage_jvh-vc-template_posts_custom_column', function( $column, $post_id ) {
			switch ( $column ) {
				case 'featured_image' :
					the_post_thumbnail( 'full', $post_id );
					break;
			}
		}, 10, 2 );
	}

	private function removeUnnecessaryColumns()
	{
		add_filter( 'manage_jvh-vc-template_posts_columns', function( $columns ) {
			unset( $columns['date'] );
			unset( $columns['cptemplate'] );

			return $columns;
		}, 11 );
	}

	private function hasCategories()
	{
		$categories = get_terms( [
			'taxonomy' => 'category-jvh-template',
			'hide_empty' => false,
		] );

		return count( $categories ) > 0;
	}

	private function getCategories()
	{
		return [
			'intros' => 'Intros',
			'features' => 'Features',
			'content' => 'Content',
			'headings' => 'Headings',
			'tabs' => 'Tabs',
			'sliders' => 'Sliders',
			'blog' => 'Blog',
			'portfolio' => 'Portfolio',
			'shop' => 'Shop',
			'pricing' => 'Pricing',
			'cta' => 'Call to Action',
			'forms' => 'Forms',
			'clients' => 'Clients',
			'accordion' => 'Accordion',
			'video' => 'Video',
			'testimonials' => 'Testimonials',
			'Reviews' => 'reviews',
			'gallery' => 'Gallery',
			'links' => 'Links & Social',
			'faq' => 'FAQ',
			'maps' => 'Maps',
			'contact' => 'Contact information',
			'countdown' => 'Countdown',
			'numbers' => 'Numbers',
			'custom_404' => '404 Pages',
			'stories' => 'Stories',
			'team' => 'Team',
			'image_carousel' => 'Image carousel',
			'charts' => 'Charts',
			'slides' => 'Slides',
			'miscellaneous' => 'Miscellaneous',
			'pages' => 'Pages',
			'footers' => 'Footers',
		];
	}

	private function shouldHideDefaultTemplates()
	{
		return get_user_meta( get_current_user_id(), 'show_essentials_vc_elements', true ) != 1;
	}
}
