<?php
/*
 * Plugin Name: FlexiStatic
 * Plugin URI: https://de.wordpress.org/plugins/flexistatic/
 * Description:	 Make real static posts flexible.
 * Author: 3UU
 * Version: 1.0.3
 * Author URI: http://datenverwurstungszentrale.com
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Donate link: http://folge.link/?bitcoin:1Ritz1iUaLaxuYcXhUCoFhkVRH6GWiMTP
 * Text Domain: flexistatic

// rtzTODO
- hook fuer speichern (aenderungen content ebenso wie URI)
- log all static so that they can get unlink also if structure of permalinks changes
- speichern an anderem Ort / auf anderem Server (trigger publish)
- make all / remove all
*/

// prevent direct calls 
if ( ! class_exists('WP') ) { die(); }

// main
function static3UU_init(){
  global $static3uu_startID, $static3uu_search;
  // sanitoeter
  $static3uu_search 	= isset($_REQUEST['static3uu_search']) 	                                                  ? sanitize_text_field($_REQUEST['static3uu_search']) : '';  
  $static3uu_post_ID    = isset($_REQUEST['static3uu_post_ID']) && intval($_REQUEST['static3uu_post_ID']) >= '0'  ? intval($_REQUEST['static3uu_post_ID']) : '';
  $static3uu_startID 	= isset($_REQUEST['static3uu_startID']) && intval($_REQUEST['static3uu_startID']) >= '0'  ? intval($_REQUEST['static3uu_startID']) :  '';

  // create/remove static content
  $static3uu_act        = isset($_REQUEST['static3uu_act'])                                                       ? sanitize_text_field($_REQUEST['static3uu_act'])    : '';
  if($static3uu_act=='static3UU_proc_make_static')   static3UU_proc_make_static($static3uu_post_ID);
  if($static3uu_act=='static3UU_proc_remove_static') static3UU_proc_remove_static($static3uu_post_ID);
}
add_action( 'admin_init', 'static3UU_init' );

// translations
add_action( 'admin_init', 'static3UU_load_textdomain' );
function static3UU_load_textdomain(){ load_plugin_textdomain( 'flexistatic', false, dirname(plugin_basename( __FILE__ )) ); }

// Admin Page
add_action( 'admin_menu', 'static3UU_admin_menu' );
function static3UU_admin_menu() {
#        add_submenu_page( 'flexistatic/flexistatic.php', 'Options', 'options', 'manage_options', 'flexistatic/flexistatic.php', 'admin_sub_page' ); 
        add_menu_page( '3UU flexi static', 'flexi static', 'manage_options', 'flexistatic/flexistatic.php', 'static3UU_admin_page', 'dashicons-hammer' );
        }

function static3UU_admin_page(){
        ?>
         <div class="wrap">
         <h2>flexible static content</h2>
         <p><? _e('For best results you should set a permalink that is ending in <code>.htm</code> or <code>.html</code>. Example: <code>/%postname%-%post_id%.htm</code>','flexistatic') ?><br>
         <? printf( __("You current definition for permalinks is <code>%s</code> and can be changed <a href='options-permalink.php'>here</a>.", 'flexistatic'),get_option('permalink_structure')) ?> 
         </p><p><? _e('IMPORTANT: Please DELETE ALL static posts/pages here BEFORE CHANGING the definition of permalinks!!!','flexistatic') ?></p> 
         <hr>
<?
#    global $wp_rewrite;
#    $rtz=$wp_rewrite->mod_rewrite_rules();
#    echo "<pre>";
#    var_dump($rtz);
#    echo "</pre>";
#    #$wp_rewrite->flush_rules();                

        // search a post
        global $static3uu_search;
        echo "<form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
        echo "<input type='text' name='static3uu_search' value='$static3uu_search' placeholder='".__('any title text', 'flexistatic')."'>
              <input type='hidden' name='static3uu_act' value='proc_search_posts'><input type='submit' value='".__('search posts', 'flexistatic')."'></form><br>";
        if(!empty($static3uu_search)){
          global $wpdb;
          $query = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE (post_type='post' OR post_type='page') AND post_title LIKE %s", "%". $static3uu_search. "%" );
          $posts = $wpdb->get_results($query);

          foreach($posts as $post) {
              $permalink=wp_make_link_relative(get_permalink($post->ID));
              if(!is_file($_SERVER['DOCUMENT_ROOT'].$permalink)){
                echo "<form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
                echo "<input type='hidden' name='static3uu_post_ID' value='".$post->ID."'>
                      <input type='hidden' name='static3uu_search' value='$static3uu_search'>
                      <input type='hidden' name='static3uu_act' value='static3UU_proc_make_static'><input type='submit' value='".__('make static','flexistatic')."'></form>";
              }else{
                echo "<form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
                echo "<input type='hidden' name='static3uu_post_ID' value='".$post->ID."'>
                      <input type='hidden' name='static3uu_search' value='$static3uu_search'>
                      <input type='hidden' name='static3uu_act' value='static3UU_proc_remove_static'><input type='submit' style='background-color: #ff0000;' value=".__('remove static','flexistatic')."'></form>";
              }
              echo "$post->post_type <a target='_blank' href='$permalink'>". esc_html($post->post_title) ."</a> $permalink <br>";
              echo "</form>";
          }
        }
?>
         </div>
         <?php

  global $static3uu_startID;
  
  // start page static
  echo "<h2>".__('Make the start page static.','flexistatic')."</h2>";
  if( !is_file($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()).'/index.html') ){
      echo "<p><form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
      echo "<input type='hidden' name='static3uu_post_ID' value='0'><input type='hidden' name='static3uu_startID' value='$static3uu_startID'>
            <input type='hidden' name='static3uu_act' value='static3UU_proc_make_static'><input type='submit' value='".__('make static','flexistatic')."'></form>";
      echo "Homepage (this will write the file <code><a target='_blank' href='".get_site_url()."'>".get_site_url()."</a>/<b>index.html</b></code> )</p>";
  }else{
      echo "<p><form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
      echo "<input type='hidden' name='static3uu_post_ID' value='0'><input type='hidden' name='static3uu_startID' value='$static3uu_startID'>
      <input type='hidden' name='static3uu_act' value='static3UU_proc_remove_static'><input type='submit' style='background-color: #ff0000;' value='".__('remove static','flexistatic')."'></form>";
      echo " ". __('Homepage (this will remove the file','flexistatic'). "<code><a target='_blank' href='".get_site_url()."'>".get_site_url()."</a>/<b>index.html</b></code> )</p>";
      // some plugins with dirty hooks to the /index.php of WP instead of the plugin files will not work
      // if index.html get preference. E.g. the very usefull "Simple Custom CSS".
      echo "<p>".__('Attention: Some more ore less usefull plugins do need direct requests to the <code>index.php</code> in the blog root folder. Therefor you should add','flexistatic')," ";
      echo "<pre>            RewriteEngine On
            RewriteCond %{REQUEST_URI} ^/index\.html
            RewriteCond %{QUERY_STRING} !^$
            RewriteRule . /index.php [L]</pre>";
      printf ( __("to your <code>%s/.htaccess</code> file.",'flexistatic'), $_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()) );
      echo "</p>";
  }

  // all list posts/pages
  echo "<h2>".__('Make single posts static.','flexistatic')."</h2>";
  // alle Seiten-Urls holen
  $posts = new WP_Query('post_type=any&posts_per_page=-1');
  $posts = $posts->posts;

  $i='0'; $j='0'; $nav='';
  
  foreach($posts as $post) {
    $permalink='';
    
    switch ($post->post_type) {
        case 'revision':
        case 'nav_menu_item':
        case 'attachment':
            continue;
            break;
        case 'page':
            $permalink = wp_make_link_relative(get_page_link($post->ID));
            break;
        case 'post':
            $permalink = wp_make_link_relative(get_permalink($post->ID));
            break;
        default:
            $permalink = wp_make_link_relative(get_post_permalink($post->ID));
            break;
    }
    #echo "{$permalink}";
    if( ($post->post_type!='post') && ($post->post_type!='page') )continue;

    $i++;
    if(($i<=$static3uu_startID) || ($i>$static3uu_startID+10)) {
      if( ($i % 10) == 1) {
        $j++; 
        $nav.="<a href='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php&static3uu_startID=". ($i - 1) ."'>$j</a> | ";
      }
      continue;
    }

    // draw the buttons
    $status = is_file($_SERVER['DOCUMENT_ROOT'].$permalink) ? 'static' : 'dynamic';
    if($status=='dynamic'){
      echo "<form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
      echo "$i.<input type='hidden' name='static3uu_post_ID' value='". sanitize_html_class($post->ID) ."'><input type='hidden' name='static3uu_startID' value='$static3uu_startID'>
            <input type='hidden' name='static3uu_act' value='static3UU_proc_make_static'><input type='submit' value='".__('make static','flexistatic')."'></form>";
    }else{
      echo "<form style='display:inline!important;' action='".$_SERVER['PHP_SELF']."?page=flexistatic/flexistatic.php' method='POST'>";
      echo "$i<input type='hidden' name='static3uu_post_ID' value='". sanitize_html_class($post->ID) ."'><input type='hidden' name='static3uu_startID' value='$static3uu_startID'>
            <input type='hidden' name='static3uu_act' value='static3UU_proc_remove_static'><input type='submit' style='background-color: #ff0000;' value='".__('remove static','flexistatic')."'></form>";
    }
    echo "$post->post_type <a target='_blank' href='$permalink'>". esc_html( $post->post_title) ."</a> $permalink <br>";
  }
  
  echo "<hr> $nav <hr>";
}


function static3UU_proc_make_static($static3uu_post_ID){

    // make sure we deal with an int
    if( !(intval($static3uu_post_ID) >= '0') ) die('something is really wrong');
    
    $webfile=($static3uu_post_ID == '0') ? get_site_url() : get_permalink($static3uu_post_ID);
    $content=file_get_contents($webfile.'?ModPagespeed=off');
    
    $fsfile =($static3uu_post_ID == '0') ? $_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()).'/index.html' : $_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($static3uu_post_ID));

    // Falls das Unterverzeichnis noch nicht existiert, legen wir es an 
    if( !is_dir( dirname($fsfile) ) ) mkdir(dirname($fsfile), 0755, true);

    // commentar at the end. so we can look for it when removing static content. Only to make sure that 
    // we do not delete content that we did not produced. Or in a more horrable way: Files that would
    // needed by other parts of the software.
    $content.="<!-- static3UU|$static3uu_post_ID -->";
    file_put_contents($fsfile, $content);

    // If we have a directory in the permalink structur e.g. sombody use 
    // 		/%category%/%postname%
    // we have to make sure that
    //		/%category%/index.html 
    // will work like prior requests to 
    // 		/%category%/

    // remove the blog root from $fsfile
    // dont use get_home_path() because on virtual hosts working with synlinks and chroot we can get in trouble
    $pub_path=str_replace($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()),'',$fsfile);
    // explodiere am Slash
    $dirs=explode('/',$pub_path);

    $path=$_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url());
    $wsfile='';
    // as long as we have (sub)directories
    for($i='1';$i<count($dirs);$i++){
      $path.='/'.$dirs[$i];
      $wsfile.='/'.$dirs[$i];
      if(is_dir($path)){
        $content=''; $content2='';
        // do we have an index.html created by us?
        if(is_file($path.'/index.html')) $content=file_get_contents($path.'/index.html');
          if(strstr($content,"static3UU|$static3uu_post_ID") || strstr($content,"static3UU|autocreate")){
            // delete old file
            unlink($path.'/index.html');
         }
         // rename dir
         rename($path,$path.'static3UU');
         // request the dynamit content from WP
         $content2=file_get_contents(get_site_url().$wsfile)."<!-- static3UU|autocreate -->";
         // if we had have an old static dir, we move it back
         if(is_dir($path.'static3UU'))   rename($path.'static3UU',$path);
         // write new static file into the (sub)dir
         file_put_contents($path.'/index.html', $content2);
        }
    }
}

function static3UU_proc_remove_static($static3uu_post_ID){
  
  // make sure we deal with an int
  // 0 is internal used as sign for the blog start page because posts will start at 1
  if( !(intval($static3uu_post_ID) >= '0') ) die('something goes really wrong');
 
  // if we are in edit modus
  if(wp_is_post_revision($static3uu_post_ID)) { global $post; $static3uu_post_ID=$post->ID; }

    $fsfile=$_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($static3uu_post_ID));
    // the blog page has no permalink format. So we use index.html and hope that it has hiegher priority than index.php
    // rtzTodo: make an admin option to define the name of the index.html
    if($static3uu_post_ID == '0') $fsfile=$_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()).'/index.html'; 
    // look up the URI
    else $fsfile=$_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($static3uu_post_ID));

    // check that the static file exists. Than read it...
    if(is_file($fsfile)) $content=file_get_contents($fsfile);
    // ...to make sure that we only delete files that this plugin has written!
    if( strstr($content,"static3UU|$static3uu_post_ID") || strstr($content,"static3UU|autocreate") ) unlink($fsfile);
    // clean up empty subdirs
    // rtzTodo: Think about that sombody could have the stupid idea to set permalinks to the temp-dir 
    // auf this vhost. That could become an empty dir! But how to avoid this problem without leaving 
    // perhaps tousends of emty dirs?
    // However we should at least check that the dir is empty!
    if(count(scandir(dirname($fsfile)))=='2') rmdir(dirname($fsfile));
}

#function static3UU_proc_update_static($static3uu_post_ID){
#  // update only needed, if static content exists
#  if(file_exists($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($static3uu_post_ID)))){
#    static3UU_proc_remove_static($static3uu_post_ID);
#    // perhaps we have a (now outdated) static start page too
#    if( file_exists($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_site_url()).'/index.html') ){
#      static3UU_proc_remove_static('0');
#    } static3UU_proc_make_static('0');
#  } static3UU_proc_make_static($static3uu_post_ID);
#}

# only a warning on edit. prehaps later on auto delete/create
add_action('edit_form_top','statis3uu_warning');
function statis3uu_warning($static3uu_post_ID){
  if( file_exists($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($static3uu_post_ID) ) ) ){ 
    echo "<h2>WARNING: A static version of this post exists. Please remove it first!!! You must create the static site (and perhaps a static blog start page) again after editing!</h2>";
  }
}

# Metabox
function add_static3uu_meta_boxes( $post ) {
    add_meta_box( 'static3uu-metabox', __( 'FlexiStatic','flexistatic' ), 'render_static3uu_meta_box', 'post', 'side', 'default' );
}
function render_static3uu_meta_box($post){
  // if we are in edit modus, check that we do not try to work on the revision ID
#  if(wp_is_post_revision($post_ID)) { global $post; $post_ID=$post->ID; }
# rtzTodo: Hier muessen wir noch abfangen, dass beim Aendern/speichern automagisch eine statische erzeugt wird.

  if( file_exists($_SERVER['DOCUMENT_ROOT'].wp_make_link_relative(get_permalink($post->ID) ) ) ){
    echo "<form style='display:inline!important;' action='".admin_url()."admin.php?page=flexistatic/flexistatic.php' method='POST'><input type='hidden' name='static3uu_post_ID' value='".$post->ID."'>";
    echo "<input type='hidden' name='static3uu_act' value='static3UU_proc_remove_static'><input type='submit' style='background-color: #ff0000;' value='".__('remove static','flexistatic')."'></form>";
  }else{
   # echo "<form style='display:inline!important;' action='".admin_url()."admin.php?page=flexistatic/flexistatic.php' method='POST'><input type='hidden' name='static3uu_post_ID' value='".$post->ID."'>";
   # echo "<input type='hidden' name='static3uu_act' value='static3UU_proc_make_static'><input type='submit' value='".__('make static','flexistatic')."'></form>";
  }
}
add_action( 'add_meta_boxes_post', 'add_static3uu_meta_boxes' );

#function static3UUadmin_sub_page(){
#        echo "rtz";
#}

// rtzTodo: later version should also support costum types in static3UU_admin_page()
#$filters = array(
#    'post_link',       // Normal post link
#    'post_type_link',  // Custom post type link
#    'page_link',       // Page link
#    'attachment_link', // Attachment link
#    'get_shortlink',   // Shortlink
#
#    'post_type_archive_link',    // Post type archive link
#    'get_pagenum_link',          // Paginated link
#    'get_comments_pagenum_link', // Paginated comment link
#
#    'term_link',   // Term link, including category, tag
#    'search_link', // Search link
#
#    'day_link',   // Date archive link
#    'month_link',
#    'year_link',
#);
#foreach ( $filters as $filter ) { add_filter( $filter, 'wp_make_link_relative' ); }

#add_action( 'init', 'ao_add_rewrite_rule');
#function ao_add_rewrite_rule() {
#  add_rewrite_rule( "tescht", 'index.php?&p=18264', 'top');
##  add_rewrite_endpoint( "tescht", EP_PERMALINK | EP_PAGES );
#  flush_rewrite_rules();
##remove_filter('template_redirect', 'redirect_canonical');
#}

?>
