<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// | Based on an original by Donncha (http://ocaoimh.ie/)                 |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * Base class for tabbed paged.
 *
 * @category Domainmap
 * @package Render
 * @subpackage Network
 *
 * @since 4.0.0
 */
class Domainmap_Render_Network_Options extends Domainmap_Render_Network {

	/**
	 * Returns array of mapping variations.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 * @return array The array of mapping variations.
	 */
	private function _get_mapping_options() {
		return array(
			'user'     => __( 'domain entered by the user', 'domainmap' ),
			'mapped'   => __( 'mapped domain', 'domainmap' ),
			'original' => __( 'original domain', 'domainmap' ),
		);
	}

	/**
	 * Renders page header.
	 *
	 * @since 4.0.0
	 *
	 * @access protected
	 */
	protected function _render_header() {
		parent::_render_header();

		 if ( filter_input( INPUT_GET, 'saved', FILTER_VALIDATE_BOOLEAN ) ) :
			echo '<div id="message" class="updated fade">', __( 'Options updated.', 'domainmap' ), '</div>';
		endif;
	}

	/**
	 * Renders tab content.
	 *
	 * @since 4.0.0
	 *
	 * @access protected
	 */
	protected function _render_tab() {
		$msg = array();
		if ( !file_exists( WP_CONTENT_DIR . '/sunrise.php' ) ) {
			$msg[] = "<p><strong>" . __( "Please copy the sunrise.php to ", 'domainmap' ) . WP_CONTENT_DIR . __( "/sunrise.php and uncomment the SUNRISE setting in the ", 'domainmap' ) . ABSPATH . __( "wp-config.php file", 'domainmap' ) . "</strong></p>";
		}

		if ( !defined( 'SUNRISE' ) ) {
			$msg[] = "<p><strong>" . __( "If you've followed the instructions and not already added define( 'SUNRISE', 'on' ); then please do so. If you added the constant be sure to uncomment this line: //define( 'SUNRISE', 'on' ); in the wp-config.php file.", 'domainmap' ) . "</strong></p>";
		}

		if ( defined( 'DOMAIN_CURRENT_SITE' ) ) {
			$str = "<p><strong>" . __( "If you are having problems with domain mapping you should try removing the following lines from your wp-config.php file:.", 'domainmap' ) . "</strong></p>";
			$str .= "<ul>";
			$str .= "<li>" . "define( 'DOMAIN_CURRENT_SITE', '" . DOMAIN_CURRENT_SITE . "' );" . "</li>";
			$str .= "<li>" . "define( 'PATH_CURRENT_SITE', '" . PATH_CURRENT_SITE . "' );" . "</li>";
			$str .= "<li>" . "define( 'SITE_ID_CURRENT_SITE', 1 );" . "</li>";
			$str .= "<li>" . "define( 'BLOG_ID_CURRENT_SITE', 1 );" . "</li>";
			$str .= "</ul>";
			$str .= "<p><strong>" . __( "Note: If your domain mapping plugin is WORKING correctly, then please LEAVE these lines in place.", 'domainmap' ) . "</strong></p>";

			$msg[] = $str;
		}

		if ( !empty( $msg ) ) {
			echo '<div class="domainmapping-info">', implode( '</div><div class="domainmapping-info">', $msg ), '</div>';
		}

		$this->_render_domain_configuration();
		$this->_render_administration_mapping();
		$this->_render_login_mapping();
		$this->_render_pro_site();

		?><p class="submit">
			<button type="submit" class="button button-primary">
				<i class="icon-save"></i> <?php _e( 'Save Changes', 'domainmap' ) ?>
			</button>
		</p><?php
	}

	/**
	 * Renders domain configuration section.
	 *
	 * @since 4.0.0
	 *
	 * @access private
	 */
	private function _render_domain_configuration() {
		$ips = false;
		if ( function_exists( 'dns_get_record' ) && !empty( $this->basedomain ) ) {
			$ips = wp_list_pluck( dns_get_record( $this->basedomain, DNS_A ), 'ip' );
		}

		?><h4><?php _e( 'Domain mapping Configuration', 'domainmap' ) ?></h4>
		<p>
			<?php _e( "Enter the IP address users need to point their DNS A records at. If you don't know what it is, ping this blog to get the IP address.", 'domainmap' ) ?><br>
			<?php _e( "If you have more than one IP address, separate them with a comma. This message is displayed on the Domain mapping page for your users.", 'domainmap' ) ?>
		</p>

		<?php if ( !empty( $ips ) ) : ?>
		<div class="domainmapping-info">
			<p><?php
				_e( 'Looks like we are able to resolve your DNS A record(s) for your main domain and fetch IP address(es) assigned to it. You can use folloding IP addres(es) to enter in the <b>Server IP Address</b> field below:', 'domainmap' )
			?></p>
			<p>
				<b><?php echo implode( '</b>, <b>', $ips ) ?></b>
			</p>
		</div>
		<?php endif; ?>

		<p>
			<?php _e( "Server IP Address: ", 'domainmap' ) ?>
			<input type="text" name="map_ipaddress" class="regular-text" value="<?php echo esc_attr( $this->map_ipaddress ) ?>">
		</p><?php
	}

	/**
	 * Renders admin mapping section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_administration_mapping() {
		?><h4><?php _e( 'Administration mapping', 'domainmap' ) ?></h4>
		<p><?php _e( "The settings below allow you to control how the domain mapping plugin operates with the administration area.", 'domainmap' ) ?></p>
		<p>
			<?php _e('The domain used for the administration area should be the', 'domainmap') ?>
			<select name='map_admindomain'>
				<?php foreach ( $this->_get_mapping_options() as $map => $label ) : ?>
				<option value="<?php echo $map ?>"<?php selected( $map, $this->map_admindomain ) ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
		</p><?php
	}

	/**
	 * Renders login mapping section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_login_mapping() {
		?><h4><?php _e( 'Login mapping', 'domainmap' ) ?></h4>
		<p><?php _e( "The settings below allow you to control how the domain mapping plugin operates with the login area.", 'domainmap' ) ?></p>
		<p>
			<?php _e( 'The domain used for the login area should be the', 'domainmap' ) ?>
			<select name="map_logindomain">
				<?php foreach ( $this->_get_mapping_options() as $map => $label ) : ?>
				<option value="<?php echo $map ?>"<?php selected( $map, $this->map_logindomain ) ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
		</p><?php
	}

	/**
	 * Renders pro site section.
	 *
	 * @since 4.0.0
	 *
	 * @access public
	 */
	private function _render_pro_site() {
		if ( !function_exists( 'is_pro_site' ) ) {
			return;
		}

		?><h4><?php _e( 'Restricted Access', 'domainmap' ) ?></h4>
		<p><?php _e( 'Make this functionality only available to certain Pro Sites levels', 'domainmap' ) ?></p>

		<table>
			<tr>
				<td valign="top">
					<strong><?php _e( "Select Pro Sites Levels: ", 'domainmap' ) ?></strong>
				</td>
				<td valign="top">
					<ul style="margin-top: 0"><?php
						$levels = (array)get_site_option( 'psts_levels' );
						if ( !is_array( $this->map_supporteronly ) && !empty( $levels ) && $this->map_supporteronly == '1' ) :
							$keys = array_keys( $levels );
							$this->map_supporteronly = array( $keys[0] );
						endif;

						foreach ( $levels as $level => $value ) :
							?><li>
								<label>
									<input type="checkbox" name="map_supporteronly[]" value="<?php echo $level ?>"<?php checked( in_array( $level, (array)$this->map_supporteronly ) ) ?>>
									<?php echo $level, ': ', esc_html( $value['name'] ) ?>
								</label>
							</li><?php
						endforeach;
					?></ul>
				</td>
			</tr>
		</table><?php
	}

}