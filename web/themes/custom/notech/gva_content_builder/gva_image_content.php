<?php 

if(!class_exists('element_gva_image_content')):
   class element_gva_image_content{
      public function render_form(){
         return array(
           'type'          => 'gsc_image_content',
            'title'        => t('Image content'),
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'admin'     => true,
                  'class'     => 'width-1-2'
               ),
               array(
                  'id'        => 'background',
                  'type'      => 'upload',
                  'title'     => t('Images'),
                  'class'     => 'width-1-2'
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content')
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'text',
                  'title'     => t('Link'),
                  'class'     => 'width-1-3'
               ),

               array(
                  'id'        => 'text_link',
                  'type'      => 'text',
                  'title'     => t('Text Link'),
                  'class'     => 'width-1-3'
               ),

               array(
                  'id'        => 'target',
                  'type'      => 'select',
                  'title'     => t('Open in new window'),
                  'desc'      => t('Adds a target="_blank" attribute to the link'),
                  'options'   => array( 'off' => 'No', 'on' => 'Yes' ),
                  'std'       => 'on',
                  'class'     => 'width-1-3'
               ),

               array(
                  'id'        => 'skin',
                  'type'      => 'select',
                  'title'     => t('Skin'),
                  'options'   => array( 
                     'skin-v1' => t('Skin 1'), 
                     'skin-v2' => t('Skin 2'), 
                     'skin-v3' => t('Skin 3'), 
                  ),
                  'class'     => 'width-1-4'
               ),

               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'class'     => 'width-1-4'
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_content_builder_animate(),
                  'class'     => 'width-1-4'
               ), 
               array(
                  'id'        => 'animate_delay',
                  'type'      => 'select',
                  'title'     => t('Animation Delay'),
                  'options'   => gavias_content_builder_delay_aos(),
                  'desc'      => '0 = default',
                  'class'     => 'width-1-4'
               ), 
         
            ),                                     
         );
      }

      public static function render_content( $attr = array(), $content = '' ){
         global $base_url;
         extract(gavias_merge_atts(array(
            'title'              => '',
            'background'         => '',
            'content'            => '',
            'link'               => '',
            'text_link'          => 'Read more',
            'target'             => '',
            'skin'               => 'skin-v1',
            'el_class'           => '',
            'animate'            => '',
            'animate_delay'      => ''
         ), $attr));

         // target
         if( $target =='on' ){
            $target = 'target="_blank"';
         } else {
            $target = false;
         }
         
         if($background) $background = $base_url . $background; 

         if($skin) $el_class .= ' ' . $skin;
         if($animate) $el_class .= ' wow ' . $animate;
         $title_html = $link ? "<a {$target} href=\"{$link}\">{$title}</a>" : $title;
         $link = gavias_get_uri($link);
         ob_start();
         ?>

         <?php if($skin == 'skin-v1'){ ?>
            <div class="image-content-one__single <?php print $el_class; ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>>
               <div class="image-content-one__image">
                  <img src="<?php print $background ?>" alt="<?php print strip_tags($title) ?>" />
                  <?php
                     if($link){ 
                        print '<a class="image-content-one__link-overlay" ' . $target . ' href="' . $link . '"></a>';
                     } 
                  ?>
               </div>
               <div class="image-content-one__content">
                  <?php 
                     if($title_html){
                        print '<h4 class="image-content-one__title">' . $title_html . '</h4>';
                     } 
                     if($link){ 
                        print '<div class="image-content-one__read-more"><a class="btn-inline" ' . $target . ' href="' . $link . '">';
                        print $text_link;
                        print '</a></div>';
                     } 
                  ?>

               </div>  
            </div>
         <?php } ?>   

         <?php if($skin == 'skin-v2'){ ?>
            <div class="image-content-two__single <?php print $el_class; ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>>
               <div class="image-content-two__image">
                  <img src="<?php print $background ?>" alt="<?php print strip_tags($title) ?>" />
                  <?php
                     if($link){ 
                        print '<a class="image-content-two__link-overlay" ' . $target . ' href="' . $link . '"></a>';
                     } 
                  ?>
                  </div>
               <div class="image-content-two__content">
                  <?php 
                     if($title_html){
                        print '<h4 class="image-content-two__title">' . $title_html . '</h4>';
                     } 
                     if($link){ 
                        print '<div class="image-content-two__read-more"><a class="btn-inline" ' . $target . ' href="' . $link . '">';
                        print $text_link;
                        print '</a></div>';
                     } 
                  ?>
               </div>  
            </div>
         <?php } ?> 

         <?php if($skin == 'skin-v3'){ ?>
            <div class="image-content-three__single <?php print $el_class; ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>>
               <div class="image-content-three__image">
                  <img src="<?php print $background ?>" alt="<?php print strip_tags($title) ?>" />
                  <?php
                     if($link){ 
                        print '<a class="image-content-three__link-overlay" ' . $target . ' href="' . $link . '"></a>';
                     } 
                  ?>
                  </div>
               <div class="image-content-three__content">
                  <?php 
                     if($title_html){
                        print '<h4 class="image-content-three__title">' . $title_html . '</h4>';
                     } 
                     if($content){
                        print '<div class="image-content-three__desc">' . $content . '</div>';
                     }
                     if($link){ 
                        print '<div class="image-content-three__read-more"><a class="btn-inline" ' . $target . ' href="' . $link . '">';
                        print $text_link;
                        print '</a></div>';
                     } 
                  ?>
               </div>  
            </div>
         <?php } ?> 

         <?php return ob_get_clean() ?>
        <?php            
      } 

   }
endif;   
