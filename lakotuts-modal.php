<?php
/**
 * Plugin Name: LakoTuts Bootstrap Modal
 * Plugin URI: http://lakotuts.com
 * Description: Plugin za tutorijal na lakotuts.com
 * Version:  1.0
 * Author: Igor benić
 * Author URI: http://twitter.com/igorbenic
 * License: GPL2
 *
 *
 * 
 *   Copyright 2014  Igor Benić  (email : i.benic@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
 /**
 * Proper way to enqueue scripts and styles
 */
function lakotuts_add_style_and_script() {
	wp_enqueue_style( 'lakotuts-bootstrap-css', "https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" );
	wp_enqueue_script( 'lakotuts-bootstrap-js', "https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js", array('jquery'), '1.0.0', true );
	
}

add_action( 'wp_enqueue_scripts', 'lakotuts_add_style_and_script' );


function lakotuts_add_bootstrap_modal(){
	?>
	<!-- Modal -->
<div class="modal fade" id="lakotutsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="modalTitle">Modal title</h4>
      </div>
      <div class="modal-body" id="modalBody">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>
<?php
}

add_action('wp_footer','lakotuts_add_bootstrap_modal');


function lakotuts_show_post_in_modal( $atts ) {
    $attributes = shortcode_atts( array(
        'id' => 0,
        'text' => "Open post in modal",
        'class' => "btn btn-primary",
        'style' => ''
    ), $atts );
    $ajax_nonce = wp_create_nonce( "lakotuts-bootstrap" );
    ?>
    <script>
    function lakotuts_show_post_js(id){
	    jQuery.ajax({
	    	url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
	    	data: { id:  id, action: 'lakotuts_show_post', security: '<?php echo $ajax_nonce; ?>'},
	    	
	    	success: function(response){
	    		if(response['error'] == '1'){
	    			jQuery('#modalTitle').html("Error");
	    					jQuery('#modalBody').html("No post found! Sorry :(");
	    		} else {
	    		jQuery('#modalTitle').html(response['post_title']);
	    		jQuery('#modalBody').html(response['post_content']);
	    		
	    	}
	    	jQuery('#lakotutsModal').modal('show');
	    		
	    	}
	    });
    }
    </script>
			<a style="<?php echo $attributes["style"]; ?>" class="<?php echo $attributes["class"]; ?>" onClick='lakotuts_show_post_js(<?php echo $attributes["id"]; ?>)'><?php echo $attributes["text"]; ?></a>
    <?php

    
}
add_shortcode( 'lakotuts_post', 'lakotuts_show_post_in_modal' );

add_action('wp_ajax_nopriv_lakotuts_show_post', 'lakotuts_show_post');
add_action('wp_ajax_lakotuts_show_post', 'lakotuts_show_post');
function lakotuts_show_post(){
	check_ajax_referer( 'lakotuts-bootstrap', 'security' );

	$id = $_GET['id'];
	$post = get_post($id);

	if($post){
	wp_send_json(array('post_title' => $post->post_title, 'post_content' => $post->post_content));
} else {
	wp_send_json(array('error' => '1'));
}

	die();
}


add_filter('widget_text', 'do_shortcode');
