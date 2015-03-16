<?php 
/*
 * CWP_Post_Public, version: 0.0.1
 *
 * @desc: Handles querying and displaying posts and wp_rest calls
*/

class CWP_Post_Public {
	
	public $display_fields = array(
		'promo'  		=> array( 'title','link','img','excerpt' ),
		'promo-small'  	=> array( 'title','link','img','excerpt' ),
		'full' 	  		=> array( 'title','link','content' ),
		'list' 	  		=> array( 'title','link','excerpt' ),
		'gallery' 		=> array( 'title','link','img','excerpt' ),
		'search-result' => array( 'title','link','img','excerpt' ),
		'slide' 		=> array( 'title','link','img','excerpt' ),
	);
	
	/****************************************************
	 * Get Posts
	****************************************************/
	
	
	
	public function cwp_get_local_posts( $args = array() ) {
		
		$query = $this->cwp_get_wp_query( $args );
		
		$items = $this->cwp_get_wp_items_from_query( $query , $args );
		
		$articles = $this->cwp_get_articles_from_items( $items , $args );
		
		return $articles;
		
	} // end cwp_get_local_posts
	
	
	
	public function cwp_get_rest_posts( $args = array() ) {
		
		$query = $this->cwp_get_rest_query( $args );
		
		$items = $this->cwp_get_rest_items_from_query( $query , $args );
		
		$articles = $this->cwp_get_articles_from_items( $items , $args );
		
		return $articles;
		
	} // end cwp_get_local_posts
	
	/****************************************************
	 * Query Posts
	****************************************************/
	 
	 
	 
	public function cwp_get_wp_query( $args ){
	} // end cwp_get_wp_query
	
	
	
	public function cwp_get_rest_query( $args ){
	} // end cwp_get_rest_query
	
	
	
	/****************************************************
	 * Get Items
	****************************************************/
	
	
	
	public function cwp_get_wp_items_from_query( $query , $args ) {
		
		$fields = $this->get_item_fields( $args );
		
	} // end cwp_get_wp_items_from_query
	
	
	
	public function cwp_get_rest_items_from_query( $query , $args ){
		
		$fields = $this->get_item_fields( $args );
		
	} // end cwp_get_rest_items_from_query
	
	
	
	/****************************************************
	 * Get Articles
	****************************************************/
	
	
	
	public function cwp_get_articles_from_items( $items , $args ){
	} // end cwp_get_articles_from_items
	
	
	
	/****************************************************
	 * Services
	****************************************************/
	
	
	
	private function get_item_fields( $args ) {
		
		$fields = array();
		
		$display = ( ! empty( $args['display'] ) )? $args['display'] : 'promo'; 
		
		if ( array_key_exists( $display , $this->display_fields ) ){
			
			$fields = $this->display_fields[ $display ];
			
		}
		
		return $fields;
		
	} // end get_item_fields
	
	
} // end CWP_Post