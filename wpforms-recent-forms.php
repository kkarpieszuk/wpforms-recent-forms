<?php
/**
 * Plugin Name: WPForms Recent Forms
 * Description: Adds quick links to recently edited forms in WPForms menu in the admin bar.
 * Version: 0.1
 * Plugin URI: https://github.com/kkarpieszuk/wpforms-recent-forms
 */

namespace WPForms\DevTools;

/**
 * Recent forms class.
 *
 * @since {VERSION}
 */
class RecentForms {

	/**
	 * Register hooks.
	 *
	 * @since {VERSION}
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'wpforms_admin_adminbarmenu_register_all_forms_menu_after', [ $this, 'admin_bar_menu_links' ], 10, 1 );
	}

	/**
	 * Adds positions to WPForms menu in admin bar.
	 *
	 * @since {VERSION}
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
	 *
	 * @return void
	 */
	public function admin_bar_menu_links( $wp_admin_bar ) {

		if ( ! function_exists( 'wpforms' ) ) {
			return;
		}

		// Parent node for recent forms.
		$nodes[] = [
			'parent' => 'wpforms-menu',
			'title'  => esc_html__( 'Recent forms', 'wpforms' ),
			'id'     => 'wpforms-recent-forms',
			'href'   => '#',
		];

		$recent_forms = get_posts(
			[
				'post_type' => 'wpforms',
				'orderby'   => 'modified',
				'order'     => 'DESC',
			]
		);

		foreach ( $recent_forms as $form ) {
			$edit_link = add_query_arg(
				[
					'view'    => 'fields',
					'form_id' => $form->ID,
				],
				admin_url( 'admin.php?page=wpforms-builder' )
			);

			// Single form.
			$nodes[] = [
				'parent' => 'wpforms-recent-forms',
				'title'  => esc_html( $form->post_title ),
				'id'     => 'wpforms-recent-form-' . $form->ID,
				'href'   => esc_url( $edit_link ),
				'meta'   => [
					'class' => 'wpforms-recent-single-form-link',
				],
			];

			// Child node for single form - edit link.
			$nodes[] = [
				'parent' => 'wpforms-recent-form-' . $form->ID,
				'id'     => 'wpforms-recent-form-' . $form->ID . '-edit',
				'title'  => esc_html__( 'Edit', 'wpforms' ),
				'href'   => esc_url( $edit_link ),
			];

			// Child node for single form - preview link.
			$nodes[] = [
				'parent' => 'wpforms-recent-form-' . $form->ID,
				'id'     => 'wpforms-recent-form-' . $form->ID . '-preview',
				'title'  => esc_html__( 'Preview', 'wpforms' ),
				'href'   => esc_url( wpforms_get_form_preview_url( $form->ID ) ),
			];
		}

		foreach ( $nodes as $node ) {
			$wp_admin_bar->add_node( $node );
		}
	}
}

( new RecentForms() )->hooks();
