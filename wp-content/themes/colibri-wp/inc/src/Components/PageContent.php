<?php

namespace ColibriWP\Theme\Components;


use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Translations;
use ColibriWP\Theme\View;

class PageContent extends ComponentBase {

	public static function selectiveRefreshSelector() {
		return '.colibri-page-content';
	}

	/**
	 * @return array();
	 */
	protected static function getOptions() {
		$prefix = 'page_content_';

		return array(
			"sections" => array(
				"page_content_section" => array(
					'title' => Translations::get( 'content_settings' ),
					'panel' => 'content_panel',
					'type'  => 'colibri_section',
				)
			),

			"settings" => array(
				"page_content_pen" => array(
					'control' => array(
						'type'        => 'pen',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					),

				),

				"{$prefix}plugin-content" => array(
					'control' => array(
						'type'        => 'plugin-message',
						'section'     => "{$prefix}section",
						'colibri_tab' => 'content',
					)
				),

			),

			"panels" => array(
				"content_panel" => array(
					'priority'       => 2,
					'title'          => Translations::get( 'content_sections' ),
					'type'           => 'colibri_panel',
					'footer_buttons' => array(
						'change_header' => array(
							'label'   => Translations::get( 'add_section' ),
							'name'    => 'colibriwp_add_section',
							'classes' => array( 'colibri-button-large', 'button-primary' ),
							'icon'    => 'dashicons-plus-alt',
						)
					)
				),
			),
		);
	}

	public function renderContent() {

		View::printIn( View::CONTENT_ELEMENT, function () {
			View::printIn( View::SECTION_ELEMENT, function () {
				View::printIn( View::ROW_ELEMENT, function () {
					View::printIn( View::COLUMN_ELEMENT, function () {
						while ( have_posts() ) : the_post();
							get_template_part( 'template-parts/content/content', 'page' );
						endwhile;
					} );
				} );
			} );
		}, array( array( 'page-content', 'colibri-page-content' ) ) );
	}
}
