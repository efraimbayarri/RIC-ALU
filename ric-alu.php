<?php
/**
 * Plugin Name: RIC-ALU
 * Plugin URI: http://replicantsfactory.com/
 * Author: Efraim Bayarri
 * Author URI: http://replicantsfactory.com/
 * Version: 2014.21.1
 * Description: Projecte Web Alumnes (Escola Ramon i Cajal)
 * Release Version:(??)
 * Release Date: ??
 */

##	BEGIN debug
require_once(WP_PLUGIN_DIR.'/ric-alu/dumper.php');
require_once(WP_PLUGIN_DIR.'/ric-alu/dump_r.php');
##	END debug
add_action( 'plugins_loaded', 'ricalu_init');

add_shortcode( 'ricalu-alumne', 'ricalu_shortcode_alumne' );


function ricalu_init() {
	global $wpdb;
	global $ricca3dbname;
	$ricca3dbname = 'ricca3';
}

#############################################################################################
/**
 * Introduir identificació 
 * shortcode: [ricalu-alumne]
 *
 * @since ricca3.v.2013.13.6
 * @author Efraim Bayarri
 */
#############################################################################################
function ricalu_shortcode_alumne($atts, $content = null) {
	global $wpdb;
	global $ricca3dbname;

//	dump_r($_POST);
	
	if(!isset($_POST['primer'])){
		printf('<form method="post" action="" name="cercar"><table><tr><td>', NULL);
		printf('<INPUT type="text" name="email" /></td></tr><tr><td>', NULL);
		printf('<INPUT type="password" name="DNI" />', NULL);
		printf('<INPUT type="submit" name="primer" value="entra" />', NULL);
		printf('</td></tr></table></form>', NULL);
	}
	if(isset($_POST['primer']) && $_POST['primer'] == 'entra'){
		$upper_DNI = strtoupper($_POST['DNI']);
		$lower_email = strtolower($_POST['email']);
		$query = $wpdb->prepare('SELECT * FROM ricca3.ricca3_alumne WHERE dni=%s AND email=%s ', $upper_DNI, $lower_email);
		if($wpdb->query($query)){
			$data_alumne = $wpdb->get_results($query, ARRAY_A);
			$query=$wpdb->prepare('SELECT * FROM ricca3.ricca3_alumne_especialitat WHERE idalumne=%s ', $data_alumne[0]['idalumne']);
			printf('<form method="post" action="" name="cercar"><table><tr><td>', NULL);
			printf('<b>%s</b>', $data_alumne[0]['nomicognoms']);
			printf('</td></tr></table></form>', NULL);
			$count = $wpdb->query($query);
			$data_espec = $wpdb->get_results($query, ARRAY_A);
			printf('<form method="post" action="" name="cercar"><table>', NULL);
			for($i=0; $i<$count; $i++){
				$data_grup=$wpdb->get_results($wpdb->prepare('SELECT * FROM ricca3.ricca3_grups WHERE idgrup=%s', $data_espec[$i]['idgrup']), ARRAY_A);
				$data_any =$wpdb->get_results($wpdb->prepare('SELECT * FROM ricca3.ricca3_any WHERE idany=%s ', $data_espec[$i]['idany']), ARRAY_A);
				$actual='';
				if($data_any[$i]['actual'] == 1)$actual='<b>*</b>';
				printf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', $actual, $data_any[0]['any'],$data_grup[0]['grup']);
			}
			printf('</table></form>', NULL);
		}
	}
}