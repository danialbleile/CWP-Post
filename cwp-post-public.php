<?php 
/*
 * CWP_Post_Public, version: 0.0.2
 *
 * @desc: Handles querying and displaying posts and wp_rest calls
*/

/**
 * FILTERS
 *
 * @name cwp_post_public_get_query.
 * @param $args - array of query args.
 * @param $type - type of query 'wp','rest'.
 * @desc Filters constructed query prior to returning it.
 *
**/  

class CWP_Post_Public {
	
	private $display_fields = array(
		'promo'  		=> array(
			'display'  => 'promo.php',
			'supports' => array('title','link','img','excerpt' ),
		),
		//'promo-small'  	=> array( 'title','link','img','excerpt' ),
		//'full' 	  		=> array( 'title','link','content' ),
		//'list' 	  		=> array( 'title','link','excerpt' ),
		//'gallery' 		=> array( 'title','link','img','excerpt' ),
		//'search-result' => array( 'title','link','img','excerpt' ),
		//'slide' 		=> array( 'title','link','img','excerpt' ),
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
		
		$query = array(
			'post_type' => 'post',
		);
		
		// Search Query
		if ( ! empty( $args['s'] ) ) $query['s'] = $args['s'];
		
		// Post Type Query
		if ( ! empty( $args['post_type'] ) ) $query['post_type'] = $args['post_type'];
		
		// Posts Per Page Query
		if ( ! empty( $args['post_per_page'] ) ) $query['post_per_page'] = $args['post_per_page'];
		
		/**
		 * Tax Query: Converts comma seperated tax lists to an array of ids
		**/
		if ( ! empty( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
			
			foreach( $args['tax_query'] as $tax_index => $tax_query ){
				
				if ( ! empty( $tax_query['terms'] ) && ! empty( $tax_query['taxonomy'] ) ) {
					
					$terms = explode( ',' , $tax_query['terms'] );
					
					if ( $terms ) {
						
						$query['tax_query'][ $tax_index ]['taxonomy'] = $tax_query['taxonomy'];
					
						foreach( $terms as $term ){
							
							// check if term is a number
							if ( is_numeric ( $term ) ){
								
								$query['tax_query'][ $tax_index ]['terms'] = $term;
								
							} else {
								
								
							} // end if
							
						} // end foreach
						
						$query['tax_query'][ $tax_index ]['field'] = 'id';
						
					} // end if
					
				} // end if
				
			} // end foreach
			
		} // end if
		
		return apply_filters( 'cwp_post_public_get_query' , $query , $args , 'wp' );
		
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
		
		if ( ! empty( $this->display_fields[ $display ]['supports'] ) ){
			
			$fields = $this->display_fields[ $display ]['supports'];
			
		}
		
		return $fields;
		
	} // end get_item_fields
	
	
} // end CWP_Post