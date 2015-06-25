<?php
/**
 * Plugin Name: Icon Links Widget
 * Plugin URI: https://wordpress.org/plugins/icon-links-widget/
 * Description: A simple icon links widget, allowing you to add FontAwesome icons to any widget area and link them anywhere.
 * Version: 2.1
 * Author: Yusri Mathews
 * Author URI: http://yusrimathews.co.za/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Icon Links Widget Plugin
 * Copyright (C) 2014, Yusri Mathews - yo@yusrimathews.co.za
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

function ilw_activation(){
	global $current_user;
	$user_id = $current_user->ID;

	update_user_meta( $user_id, 'ilw_plugin_activation', date( 'F j, Y' ) );
	update_user_meta( $user_id, 'ilw_rate_ignore', 'false' );
	update_user_meta( $user_id, 'ilw_donate_ignore', 'false' );
}
register_activation_hook( __FILE__, 'ilw_activation' );

include_once('inc/notices.php');

class ilw_widget extends WP_Widget {

    // Construct Wigdet
	function __construct(){
		parent::__construct(
			'ilw_widget',
			__( 'Icon links widget', 'ilw_widget' ),
			array(
				'description' => __( 'Add icon links to your widget areas.', 'ilw_widget' ),
			)
		);
	}

    // Widget Display
	public function widget( $args, $instance ){
		wp_enqueue_style( 'ilw_fa_style', plugins_url( 'vendor/font-awesome/4.2.0/css/font-awesome.min.css', __FILE__ ) );
		wp_enqueue_style( 'ilw_widget_style', plugins_url( 'css/public.min.css', __FILE__ ), array( 'ilw_fa_style' ) );

		$title = $instance['title'];
		$fields = $instance['fields'];

		echo $args['before_widget'];

        if( !empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if( !empty( $fields ) ){
            echo '<div class="widget_ilw_widget">';
                foreach( $fields as $field ){
                    echo '<a' . ( $field['nofollow'] == true ? ' rel="nofollow"' : '' ) . ' href="' . $field['link'] . '" class="ilw_widget_icon"' . ( $field['newtab'] == true ? ' target="_blank"' : '' ) . '><i class="fa fa-' . $field['icon'] . '"></i></a>';
                }
            echo '</div>';
        }

		echo $args['after_widget'];
	}

    // Widget Save
	public function update( $new_instance, $old_instance ){
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['fields'] = array();

		if ( isset( $new_instance['fields'] ) ){
			foreach( $new_instance['fields'] as $index => $field ){
				$strip_icon = strip_tags( $field['icon'] );
				$icon_fa = str_replace( 'fa-', '', $strip_icon );
				$icon = str_replace( 'fa ', '', $icon_fa );
				$link = strip_tags( $field['link'] );
				if( '' !== trim( $icon ) ){
					$instance['fields'][ $index ]['icon'] = trim( $icon );
					$instance['fields'][ $index ]['link'] = trim( $link );
					$instance['fields'][ $index ]['newtab'] = ( !empty( $field['newtab'] ) ) ? true : '';
					$instance['fields'][ $index ]['nofollow'] = ( !empty( $field['nofollow'] ) ) ? true : '';
				}
			}
		}
		return $instance;
	}

    // Widget Dashboard
	public function form( $instance ){
		wp_enqueue_style( 'ilw_admin_style', plugins_url('css/admin.min.css', __FILE__) );
		wp_enqueue_script( 'ilw_admin_script', plugins_url('js/admin.min.js', __FILE__), array( 'jquery-ui-sortable' ) );

		if( isset( $instance[ 'title' ] ) ){
			$title = $instance[ 'title' ];
		} else {
			$title = __( '', 'ilw_widget' );
		}
		echo '<p>';
			echo '<label for="' . $this->get_field_id( 'title' ) . '" class="ilw_label">' . __( 'Title:', 'ilw_widget' ) . '</label>'; 
			echo '<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $title ) . '" class="widefat" />';
		echo '</p>';

		echo '<small class="ilw_intro">Reference the <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_blank">FontAwesome cheatsheet</a> should you need the icon names.</small>';

		if( isset( $instance['fields'] ) ){
			$fields = array_filter( $instance['fields'] );
		} else {
			$fields = array();
		}

		$fieldcount = count( $fields );
		$arraykey = array_pop( array_keys( $fields ) );
		if( !empty( $fields[ $arraykey ]['icon'] ) ){
	        $fields[ $fieldcount ]['icon'] = '';
	        $fields[ $fieldcount ]['link'] = '';
	        $fields[ $fieldcount ]['newtab'] = '';
	        $fields[ $fieldcount ]['nofollow'] = '';
		}

		echo '<div class="ilw_sortable">';
        foreach( $fields as $index => $field ){
			echo '<div class="widget ilw_widget">
					<div class="ilw_widget_top">
						<div class="widget-title-action">
							<a class="widget-action hide-if-no-js" href="#"></a>
						</div>
						<div class="widget-title">
							<h4>
								<input type="text" id="' . $this->get_field_id( 'fields' ) . '[' . $index . '][icon]" name="' . $this->get_field_name( 'fields' ) . '[' . $index . '][icon]" value="' . esc_attr( $field['icon'] ) . '" class="ilw_icon" placeholder="Icon" />
								<input type="text" id="' . $this->get_field_id( 'fields' ) . '[' . $index . '][link]" name="' . $this->get_field_name( 'fields' ) . '[' . $index . '][link]" value="' . esc_attr( $field['link'] ) . '" class="ilw_link" placeholder="Link" />
							</h4>
						</div>
					</div>
					<div class="widget-inside">
	            		<label class="ilw_item">
	            			<input id="' . $this->get_field_id( 'fields' ) . '[' . $index . '][newtab]" name="' . $this->get_field_name( 'fields' ) . '[' . $index . '][newtab]" type="checkbox"' . ( $field['newtab'] == true ? ' checked="checked"' : '' ) . '>
	            			Open in new tab
	            		</label>
	            		<label class="ilw_item">
	            			<input id="' . $this->get_field_id( 'fields' ) . '[' . $index . '][nofollow]" name="' . $this->get_field_name( 'fields' ) . '[' . $index . '][nofollow]" type="checkbox"' . ( $field['nofollow'] == true ? ' checked="checked"' : '' ) . '>
	            			No follow
	            		</label>
					</div>
			</div>';
        }
		echo '</div>';
	}
}

// Initiate Widget
function ilw_load_widget(){
	register_widget( 'ilw_widget' );
}
add_action( 'widgets_init', 'ilw_load_widget' );
