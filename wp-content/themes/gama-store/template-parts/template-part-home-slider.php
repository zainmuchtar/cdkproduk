</div>
</div>
<div id="home-slider" class="flexslider">
    <ul class="slides">
      <?php $gama_store_entries = get_post_meta( get_the_ID(), 'gama_store_home_slider', true );
        foreach ( (array) $gama_store_entries as $key => $entry ) {
          $img = $title = $desc = $button = $button_url = '';
          if ( isset( $entry['gama_store_title'] ) )
            $title = esc_html( $entry['gama_store_title'] );
          if ( isset( $entry['gama_store_desc'] ) )
            $desc = wpautop( $entry['gama_store_desc'] );
          if ( isset( $entry['gama_store_button_text'] ) )
            $button = esc_html( $entry['gama_store_button_text'] );
          if ( isset( $entry['gama_store_url'] ) )
            $button_url = esc_url( $entry['gama_store_url'] );    
          if ( isset( $entry['gama_store_image_id'] ) ) {
            $img = esc_url( $entry['gama_store_image'] );
          } ?>
             <?php if ($img != '') { ?> 
                <li class="homepage-slider" style="background-image: url(<?php echo $img; ?>);"> 
                    <div class="flex-caption">
														<div class="home-content">
                              <?php if ($title != '') { ?>
                                <header>		
                      						<h2 class="title">
                      							<?php echo $title; ?>    
                      						</h2>
                      					</header><!--.header-->
                      				<?php } ?>	
                    					<?php if ($desc != '') { ?>
                      					<div class="slider-description hidden-xs">
                                    <?php echo $desc; ?>       
                                </div>
                              <?php } ?> 
                             </div>
                        <?php if ($button_url != '' && $button != '') { ?> 
                          <a class="btn btn-primary btn-md outline" href="<?php echo $button_url; ?>"><?php echo $button; ?></a>
                        <?php } ?>        
                    </div>                         
                  </li>
             <?php } ?>     
      <?php } ?> 
    </ul>
	</div>   
<div class="container rsrc-container" role="main"> 
