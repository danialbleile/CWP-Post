<?php 
/*
 * CWP_Post_Public, version: 0.0.4
 *
 * @desc: Handles querying and displaying posts and wp_rest calls
*/

/**
 * FILTERS
 *
 * @name cwp_post_public_get_query.
 * @desc Filters constructed query prior to returning it.
 * @param $args - array of query args.
 * @param $type - type of query 'wp','rest'.
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
	
	
	
	public function cwp_get_local_posts( $args = array() , $return = false ) {
		
		$query = $this->cwp_get_wp_query( $args );
		
		$items = $this->cwp_get_wp_items_from_query( $query , $args );
		
		$articles = $this->cwp_get_articles_from_items( $items , $args );
		
		if ( ! $return ){
			
			echo $articles;
			
		} else {
			
			return $articles;
			
		}
		
	} // end cwp_get_local_posts
	
	
	
	public function cwp_get_rest_posts( $args = array() , $return = false ) {
		
		$query = $this->cwp_get_rest_query( $args );
		
		$items = $this->cwp_get_rest_items_from_query( $query , $args );
		
		$articles = $this->cwp_get_articles_from_items( $items , $args );
		
		if ( ! $return ){
			
			echo $articles;
			
		} else {
			
			return $articles;
			
		}
		
	} // end cwp_get_local_posts
	
	/****************************************************
	 * Query Posts
	****************************************************/
	 
	 
	// Version 0.0.1 
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
					
					if ( ! is_array( $tax_query['terms'] ) ){
						
						$tax_query['terms'] = explode( ',' , $tax_query['terms'] );
						
					} // end if
					
						
					$query['tax_query'][ $tax_index ]['taxonomy'] = $tax_query['taxonomy'];
				
					foreach( $tax_query['terms'] as $term ){
						
						// check if term is a number
						if ( is_numeric ( $term ) ){
							
							$query['tax_query'][ $tax_index ]['terms'][] = $term;
							
						} else {
							
							$term_array = get_term_by( 'name' , $term, $tax_query['taxonomy'] , 'ARRAY_A' );
				
							if ( ! empty( $term_array['term_id'] ) ){
								
								$query['tax_query'][ $tax_index ]['terms'][]  = intval ( $term_array['term_id'] );
								
							} // end if
							
						} // end if
						
					} // end foreach
					
					$query['tax_query'][ $tax_index ]['field'] = 'id';
					
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
	
	
	// Version 0.0.3
	public function cwp_get_wp_items_from_query( $query , $args ) {
		
		$fields = $this->get_item_fields( $args );
		
		if ( in_array( 'img' , $fields ) ){
		
			$img_size = ( ! empty ( $args['img_size'] ) )?  $args['img_size'] : 'thumbnail';
		
		} // end if
		
		$items = array();
		
		$results = new WP_Query( $query );
		
		if ( $results->have_posts() ){
			
			while ( $results->have_posts() ){
				
				$item = array();
				
				$results->the_post();
				 
				
				if ( in_array( 'type' , $fields ) ){
				
					$item['type'] = $results->post->post_type;
				
				} // end if
				
				if ( in_array( 'title' , $fields ) ){
						
					$item['title'] = get_the_title();
				
				} // end if
				
				if ( in_array( 'content' , $fields ) ){
					
					$item['content'] = get_the_content();
				
				} // end if
				
				if ( in_array( 'excerpt' , $fields ) ){
						
					$item['excerpt'] = get_the_excerpt();
				
				} // end if
				
				if ( in_array( 'img' , $fields ) ){
				
					$item['img'] = get_the_post_thumbnail( $results->post->ID , $img_size );
				
				} // end if
				
				if ( in_array( 'link' , $fields ) ){
					
					if ( ! empty ( $args['more_url'] ) && ! empty ( $args['more_rewrite'] ) && $args['more_rewrite'] ){
				
						$item['link'] = $args['more_url'];
					
					} else {
						
						$item['link'] = get_permalink();
						
					} // end if
					
					$item['link_start'] = $this->get_item_link( $item['link'] , $args );
				
					$item['link_end'] = '</a>';
				
				} else {
					
					$item['link_start'] = '';
				
					$item['link_end'] = '';
					
				} // end if
				
				$items[] = $item; 
				
			} // end while
			
		} // end if
		
		wp_reset_postdata();
		
		return $items;
		
	} // end cwp_get_wp_items_from_query
	
	
	
	public function cwp_get_rest_items_from_query( $query , $args ){
		
		$fields = $this->get_item_fields( $args );
		
	} // end cwp_get_rest_items_from_query
	
	
	
	/****************************************************
	 * Get Articles
	****************************************************/
	
	
	// Version 0.0.4
	public function cwp_get_articles_from_items( $items , $args ){
		
		$articles = array();
		
		if ( $items ){
			
			foreach( $items as $item_index => $item ){
		
				$args['display'] = ( ! empty( $args['display'] ) )? $args['display'] : 'promo';
				
				
				
				switch( $args['display'] ){
					
					case 'promo':
						$articles[] = $this->get_promo_html( $item , $args );
						break;
					
				} // end switch
				
			} // end foreach
			
		} // end if
		
		return $articles;
		
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
	
	public function get_item_link( $link , $args = array() ) {
		
		$class = array();
		
		$html = '<a href="' . $link . '" ';
		
		if( ! empty( $args['show_lightbox'] ) ){
		
			$html .= 'class="clb-action" ';
			
		} // end if
		
		if( ! empty( $args['new_window'] ) ){
			
			$html .= 'target="_blank" ';
			
		} // end if
		
		$html .= ' >';
		
		return $html;
		
	} 
	
	/****************************************************
	 * Displays
	****************************************************/
	
	// Version 0.0.5
	public function get_promo_html( $item , $args ){
		
		$html = '';
		
		$html .= '<article class="promo" style="display: table">';
	
			$html .= '<div class="cwp-inner-wrapper" style="display: table-row">';
    		
				if ( ! empty( $item['img'] ) ){
					
					$html .= '<div class="cwp-article-image" style="display: table-cell; width: 150px; vertical-align: top;">';
					
						$html .= $item['link_start'] . $item['img'] . $item['link_end'];
						
					$html .= '</div>';
				} // end if;
        
				$html .= '<div class="cwp-article-content" style="display: table-cell; vertical-align: top;">';
				
				if ( ! empty( $item['title'] ) ){
					
					$html .= '<h4>' . $item['link_start'] . $item['title'] . $item['link_end'] . '</h4>';
				
				} // end if
				if ( ! empty( $item['excerpt'] ) ){
					
					$html .= $item['excerpt'];
				
				} // end if
				
				$html .= '</div>';
				
       		$html .= '</div>';
			
		$html .= '</article>';
		
		return $html;
		
	} // end get_promo_html
	
	
} // end CWP_Post