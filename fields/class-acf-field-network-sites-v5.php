<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_network_sites') ) :


class acf_field_network_sites extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		
		$this->name = 'network_sites';
		
		
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		
		$this->label = __('Network Sites', 'network_sites');
		
		
		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		
		$this->category = 'relational';
		
		
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		
		$this->defaults = array(

		);
		
		
		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('FIELD_NAME', 'error');
		*/
		
		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'network_sites'),
		);
		
		
		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/
		
		$this->settings = $settings;
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {

		$field['choices'] = $this->_get_blogs();

		echo '<select id="' . $field['id'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '">';

		foreach($field['choices'] as $key => $value)
		{
			$selected = '';
			if($value->blog_id == $field['value'])
			{
				$selected = ' selected="selected"';
			}

			echo '<option value="'.$value->blog_id.'"'.$selected.'>'.$value->domain.$value->path.'</option>';
		}
		echo '</select>';

	}

	function _get_blogs($blog_id = 0){
		global $wpdb;
		$query = "SELECT * FROM $wpdb->blogs WHERE archived = '0' AND deleted = '0'";
		if(!empty($blog_id)){
			$query .= ' AND blog_id = %d';
		}

		$query .= '  ORDER BY blog_id';

		$query = $wpdb->prepare( $query, $blog_id );

		$sites = $wpdb->get_results( $query );

		return $sites;
	}

	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function render_field_settings( $field ) {

		// return_format
		acf_render_field_setting( $field, array(
			'label'			=> __('Return Format','acf'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'choices'		=> array(
				'id'			=> __("Post ID",'acf'),
				'object'		=> __("Post Object",'acf'),
			),
			'layout'	=>	'horizontal',
		));
	}

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {
			return $value;
		}

		// load posts if needed
		if( $field['return_format'] == 'object' ) {

			$value = $this->_get_blogs($value);

		}


		// return
		return $value;

	}
	
}


// initialize
new acf_field_network_sites( $this->settings );


// class_exists check
endif;

?>