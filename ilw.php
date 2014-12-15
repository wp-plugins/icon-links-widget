<?php
/**
 * Plugin Name: Icon Links Widget
 * Plugin URI: 
 * Description: A simple icon links widget, allowing you to add FontAwesome icons to any widget area and link them anywhere.
 * Version: 0.1
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

class ilw_widget extends WP_Widget {

	function __construct(){
		parent::__construct(
			'ilw_widget',
			__( 'Icon links widget', 'ilw_widget' ),
			array(
				'description' => __( 'Icon links added to your widget areas.', 'ilw_widget' ),
			)
		);
	}

	public function widget( $args, $instance ){
		wp_enqueue_style( 'ilw_fa_style', plugins_url('vendor/font-awesome/4.2.0/css/font-awesome.min.css', __FILE__) );
		wp_enqueue_style( 'ilw_widget_style', plugins_url('css/widget.css', __FILE__) );

		$title = $instance['title'];
		$fields = $instance['fields'];

		echo $args['before_widget'];

			if( !empty( $title ) ){
				echo $args['before_title'] . $title . $args['after_title'];
			}

			if( !empty( $fields ) ){
				echo '<div class="widget_ilw_widget">';
					foreach ($fields as $value){
						echo '<a rel="nofollow" href="' . $value['link'] . '" class="ilw_widget_icon" target="_blank"><i class="fa fa-' . $value['icon'] . '"></i></a>';
					}
				echo '</div>';
			}

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ){
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['fields'] = array();

		if ( isset( $new_instance['fields'] ) ){
			// $instance['fields'] = $new_instance['fields'];
			foreach ( $new_instance['fields'] as $key => $value){
				$icon = strip_tags( $value['icon'] );
				$link = strip_tags( $value['link'] );
				if( '' !== trim( $icon ) || '' !== trim( $link ) ){
					$instance['fields'][ $key ]['icon'] = $icon;
					$instance['fields'][ $key ]['link'] = $link;
				}
			}
		}

		return $instance;
	}

	public function form( $instance ){
		wp_enqueue_style( 'ilw_admin_style', plugins_url('css/admin.css', __FILE__) );

		if( isset( $instance[ 'title' ] ) ){
			$title = $instance[ 'title' ];
		} else {
			$title = __( '', 'ilw_widget' );
		}
		echo '<p><label for="' . $this->get_field_id( 'title' ) . '" class="ilw_label">' . __( 'Title:', 'ilw_widget' ) . '</label>'; 
		echo '<input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $title ) . '" class="widefat" />';
		echo '</p>';

		echo '<small class="ilw_intro">Reference the <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">FontAwesome</a> icon library, should you need the class names. Use the names only minus the "<strong>fa-</strong>".</small>';

		if( isset( $instance['fields'] ) ){
			$fields = $instance['fields'];
		} else {
			$fields = array();
		}
		$field_num = count( $fields );
        $fields[ $field_num ]['icon'] = '';
        $fields[ $field_num ]['link'] = '';
        $fields_counter = 0;
        foreach( $fields as $field ){
        	echo '<p class="ilw_control_group">';
        	echo '<label for="' . $this->get_field_id( 'fields' ) . '[' . $fields_counter . '][icon]" class="ilw_label">' . __( 'Icon Link:', 'ilw_widget' ) . '</label>';
            echo '<input type="text" id="' . $this->get_field_id( 'fields' ) . '[' . $fields_counter . '][icon]" name="' . $this->get_field_name( 'fields' ) . '[' . $fields_counter . '][icon]" value="' . esc_attr( $field['icon'] ) . '" class="ilw_control ilw_icon" placeholder="Icon (Class)" />';
            echo '<input type="text" id="' . $this->get_field_id( 'fields' ) . '[' . $fields_counter . '][link]" name="' . $this->get_field_name( 'fields' ) . '[' . $fields_counter . '][link]" value="' . esc_attr( $field['link'] ) . '" class="ilw_control ilw_link" placeholder="Link (URL)" />';
       		echo '</p>';
            $fields_counter += 1;
        }
		
		echo '<small class="ilw_reference">Get in touch and share your thoughts!</small>';
        echo '<small class="ilw_credit"><a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">FontAwesome</a> v4.2.0 &middot; <a href="https://twitter.com/yusrimathews" target="_blank">@yusrimathews</a></small>';
	}
}

function ilw_load_widget(){
	register_widget( 'ilw_widget' );
}
add_action( 'widgets_init', 'ilw_load_widget' );

?>