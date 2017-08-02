<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
		
/**
 * GET JSON FROM GOOGLE MAP API AND OUR USERS ARRAY
 */
function woffice_update_locations_now(){
	fw()->extensions->get( 'woffice-map' )->woffice_update_locations();
}
add_action('xprofile_set_field_data', 'woffice_update_locations_now');
add_action('xprofile_updated_profile', 'woffice_update_locations_now');
	
/**
 * GENERATE JS FOR THE MAP
 */		
function woffice_members_map_js_users(){
	if (function_exists('bp_is_active')):
		if(bp_is_members_directory()) {
			echo fw()->extensions->get( 'woffice-map' )->woffice_users_map_js("members");
			
		}
	endif;
}
add_action('wp_footer', 'woffice_members_map_js_users');

/**
 * CREATE FUNCTIONS TO ADD THE LOCATION FIELD TO XPROFILE
 */

function woffice_location_add_field() {

	if ( bp_is_active( 'xprofile' ) ){
		global $bp;
		$field = get_the_location_field();
	    if(count($field) > 0)
	    {
	        //in order to remove the old textarea on some Woffice websites
	        if ($field[0]->type === "textarea") {
                global $wpdb;
                $table_name = woffice_get_xprofile_table('fields');
                $wpdb->update(
                    $table_name,
                    array(
                        'type' => 'textbox'
                    ),
                    array( 'id' => $field[0]->id ),
                    array(
                        '%s',	// string
                    ),
                    array( '%d' )
                );
            }
	        return;
	    }
	    xprofile_insert_field(
	        array (
	        	'field_group_id'  => 1,
				'can_delete' => true,
				'type' => 'textbox',
				'description' => __('This address will be used on the members directory map, please make sure this address is valid for Google Map.','woffice'),
				'name' => fw()->extensions->get( 'woffice-map' )->woffice_map_field_name(),
				'field_order'     => 1,
				'is_required'     => false,
	        )
	    );
	 }

}
add_action('bp_init', 'woffice_location_add_field');

/**
 * We send the data to our scripts.js file
 *
 * @param array $data - the current data sent to the file
 */
if(!function_exists('woffice_location_data_exchanger')) {
    function woffice_location_data_exchanger($data)
    {

        $field = get_the_location_field();

        if(is_null($field))
            return $data;

        $data['input_location_bb'] = 'field_' . $field[0]->id;

        return $data;

    }
}
add_filter('woffice_js_exchanged_data', 'woffice_location_data_exchanger');
