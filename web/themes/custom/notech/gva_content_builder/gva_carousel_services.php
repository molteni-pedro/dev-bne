<?php 
if(!class_exists('element_gva_carousel_services')):
   class element_gva_carousel_services{
      public function render_form(){
         $fields = array(
            'type' => 'gsc_carousel_services',
            'title' => t('Carousel - Services'),
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title For Admin'),
                  'default'   => t('Services Carousel'),
                  'admin'     => true,
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'title'     => t('Style'),
                  'options'   => array(
                     'style-1'   => 'Style I',
                     'style-2'   => 'Style II',
                     'style-3'   => 'Style III',
                     'style-4'   => 'Style IV',
                  ),
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
                  'options'   => gavias_content_builder_delay_wow(),
                  'desc'      => '0 = default',
                  'class'     => 'width-1-4'
               ), 
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'class'     => 'width-1-4',
                  'desc'      => t('Add custom class for element'),
               ),   
            ),                                     
         );

         gavias_carousel_fields_settings($fields);
      
         for($i = 1; $i <= 5; $i++){
            $fields['fields'][] = array(
               'id'     => "info_{$i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}"),
               'default'   => 'Product development',
               'class'     => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'           => "icon_{$i}",
               'type'         => 'text',
               'title'        => t("Icon {$i}"),
               'desc'         => t('Use class icon font <a target="_blank" href="http://fontawesome.io/icons/">Icon Awesome</a> or <a target="_blank" href="http://gaviasthemes.com/icons/icons-2/">Custom icon</a>'),
               'default'      => 'flaticon-rating',
               'class'        => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'           => "desc_{$i}",
               'type'         => 'text',
               'title'        => t("Description {$i}"),
               'default'      => 'Discover, Explore & understanding the product',
               'class'        => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'        => "link_{$i}",
               'type'      => 'text',
               'title'     => t("Link {$i}"),
               'class'     => 'width-1-2'
            );
         }

         for($i = 6; $i <= 10; $i++){
            $fields['fields'][] = array(
               'id'     => "info_{$i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}"),
               'class'     => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'           => "icon_{$i}",
               'type'         => 'text',
               'title'        => t("Icon {$i}"),
               'class'     => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'           => "desc_{$i}",
               'type'         => 'text',
               'title'        => t("Description {$i}"),
               'class'     => 'width-1-2'
            );
            $fields['fields'][] = array(
               'id'        => "link_{$i}",
               'type'      => 'text',
               'title'     => t("Link {$i}"),
               'class'     => 'width-1-2'
            );
         }
         return $fields;
      }

      public static function render_content( $attr = array(), $content = '' ){
         global $base_url;
         $default = array(
            'title'           => '',
            'more_link'       => '',
            'more_text'       => 'View all services',
            'el_class'        => '',
            'animate'         => '',
            'animate_delay'   => '',
            'style'           => 'style-1',
            'col_lg'          => '4',
            'col_md'          => '3',
            'col_sm'          => '2',
            'col_xs'          => '1',
            'auto_play'       => '0',
            'pagination'      => '0',
            'navigation'      => '0'
         );

         for($i=1; $i<=10; $i++){
            $default["title_{$i}"] = '';
            $default["icon_{$i}"] = '';
            $default["desc_{$i}"] = '';
            $default["link_{$i}"] = '';
         }

         extract(gavias_merge_atts($default, $attr));

         $classes = array();
         $classes[] = $el_class;
         $classes[] = $style;
         if($animate){
            $classes[] = 'wow';
            $classes[] = $animate;
         } 
         $class = implode(' ', $classes);

         ob_start();
         ?>
         <div class="gsc-service-carousel <?php print $class ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>> 
            <div class="owl-carousel init-carousel-owl owl-loaded owl-drag" data-items="<?php print $col_lg ?>" data-items_lg="<?php print $col_lg ?>" data-items_md="<?php print $col_md ?>" data-items_sm="<?php print $col_sm ?>" data-items_xs="<?php print $col_xs ?>" data-loop="1" data-speed="500" data-auto_play="<?php print $auto_play ?>" data-auto_play_speed="2000" data-auto_play_timeout="5000" data-auto_play_hover="1" data-navigation="<?php print $navigation ?>" data-rewind_nav="0" data-pagination="<?php print $pagination ?>" data-mouse_drag="1" data-touch_drag="1">
               <?php 
                  for($i=1; $i<=10; $i++){ 
                
                     $title = "title_{$i}";
                     $icon = "icon_{$i}";
                     $desc = "desc_{$i}";
                     $link = "link_{$i}";
                     $$link = gavias_get_uri($$link);
                     
                     if($$title){
                        print '<div class="item">';
                           if($style=='style-1'){
                              print '<div class="service-one__single">';
                                 if($$title){
                                    print '<h3 class="service-one__title">' .  $$title . '</h3>';
                                 }
                                 if($$icon){ 
                                    print '<div class="service-one__icon"><i class="'. $$icon .'"></i></div>';
                                 }         
                                 if($$desc){ 
                                    print '<div class="service-one__desc">' . $$desc . '</div>';
                                 }   
                                 if($$link){ 
                                    print '<a class="service-one__link" href="' . $$link . '"></a>'; 
                                 }    
                              print '</div>';
                           }

                           if($style=='style-2'){
                              print '<div class="service-two__single">';
                                 if($$title){
                                    print '<h3 class="service-two__title">' .  $$title . '</h3>';
                                 }
                                 if($$icon){ 
                                    print '<div class="service-two__icon"><i class="'. $$icon .'"></i></div>';
                                 }         
                                 if($$desc){ 
                                    print '<div class="service-two__desc">' . $$desc . '</div>';
                                 }   
                                 if($$link){ 
                                    print '<a class="service-two__link" href="' . $$link . '"></a>'; 
                                 }    
                              print '</div>';
                           }

                           if($style=='style-3'){
                              print '<div class="service-three__single">';
                                 if($$icon){ 
                                    print '<div class="service-three__icon"><i class="'. $$icon .'"></i></div>';
                                 }
                                 if($$title){
                                    print '<h3 class="service-three__title">' .  $$title . '</h3>';
                                 }
                                 if($$desc){ 
                                    print '<div class="service-three__desc">' . $$desc . '</div>';
                                 }   
                                 if($$link){ 
                                    print '<a class="service-three__link">' . t('Learn more') . '</a>'; 
                                    print '<a class="service-three__link-overlay" href="' . $$link . '"></a>'; 
                                 }    
                              print '</div>';
                           }

                           if($style=='style-4'){
                              print '<div class="service-four__single">';
                                 if($$icon){ 
                                    print '<div class="service-four__icon"><i class="'. $$icon .'"></i></div>';
                                 }
                                 if($$title){
                                    print '<h3 class="service-four__title">' .  $$title . '</h3>';
                                 }
                                 if($$desc){ 
                                    print '<div class="service-four__desc">' . $$desc . '</div>';
                                 }   
                                 if($$link){ 
                                    print '<a class="service-four__link-overlay" href="' . $$link . '"></a>'; 
                                 }    
                              print '</div>';
                           }

                        print '</div>';   
                     } 
                  } 
               ?>
            </div> 
         </div>   
         <?php return ob_get_clean();
      }

   }
 endif;  



