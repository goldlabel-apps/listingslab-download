<?php

  class Listingslab {
    
        private static $instance;
        private $pingping_screen; 

      /**
       * The option group, indicating that this UI should be on the 'General Settings' page.
       *
       * @var string
       */

        public function InitPlugin(){
          add_action( 'admin_menu', array( $this, 'AdminMenu' ));
          add_action( 'wp_body_open', array( $this, 'RenderPWA' ));
        } 

        public function AdminMenu(){
           $this->pingping_screen = add_menu_page(
                    '@Pingpong Admin', 
                    '@Pingpong', 
                    'manage_options',
                    __FILE__, 
                    array($this, 'RenderAdmin'), 
                    plugins_url('/pingpong/php/pingpong.png'),
                    30
              );
        }

        public function RenderAdmin(){ ?>
            <div class='none'>
              <?php 
                $wordpress = array();
                $fields = array(
                    'name', 
                    'description', 
                    'url', 
                    'admin_email'
                );
                foreach($fields as $field) {
                    $wordpress[$field] = get_bloginfo($field);
                }
                echo '<script>';
                echo 'var wordpress = ' . json_encode( $wordpress ) . ';';
                echo '</script>';
                $html = file_get_contents(plugin_dir_path( __DIR__ ) . 'react/wp-admin/build/index.html');
                $html = str_replace('href="/static', 'href="'. plugin_dir_url( __DIR__ ) .
              'react/wp-admin/build/static', $html);
                $html = str_replace('src="/static', 'src="'. plugin_dir_url( __DIR__ ) .
              'react/wp-admin/build/static', $html);
                echo $html;
              ?>
         </div>
       <?php }

        public function RenderPWA(){
                $plugins_url = plugins_url();
                $pwaData = array();                
                $pwaData[ 'adminBar' ] = is_admin_bar_showing();
                $pwaData[ 'loggedIn' ] = is_user_logged_in();
          ?>
          <div class="pwa">
            <script>
              var pwaData = <?php echo json_encode( $pwaData ); ?>;
            </script>
            <?php  
              $html = file_get_contents(plugin_dir_path( __DIR__ ) . 'react/wp-pwa/build/index.html');
              $html = str_replace('href="/static', 'href="'. plugin_dir_url( __DIR__ ) .
            'react/wp-pwa/build/static', $html);
              $html = str_replace('src="/static', 'src="'. plugin_dir_url( __DIR__ ) .
            'react/wp-pwa/build/static', $html);
              $html = str_replace('<meta name="viewport" content="width=device-width,initial-scale=1"><link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">', '', $html);
              echo $html;
            ?>
           </div>
           <?php }

    static function GetInstance(){
      if (!isset(self::$instance)) {
          self::$instance = new self();
      }
      return self::$instance;
    }
    
  }
