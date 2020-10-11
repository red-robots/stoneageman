<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bellaworks
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
define('THEMEURI',get_template_directory_uri() . '/');

function bellaworks_body_classes( $classes ) {
    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if ( is_front_page() || is_home() ) {
        $classes[] = 'homepage';
    } else {
        $classes[] = 'subpage';
    }

    $browsers = ['is_iphone', 'is_chrome', 'is_safari', 'is_NS4', 'is_opera', 'is_macIE', 'is_winIE', 'is_gecko', 'is_lynx', 'is_IE', 'is_edge'];
    $classes[] = join(' ', array_filter($browsers, function ($browser) {
        return $GLOBALS[$browser];
    }));

    return $classes;
}
add_filter( 'body_class', 'bellaworks_body_classes' );

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}


function add_query_vars_filter( $vars ) {
  $vars[] = "pg";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );


function shortenText($string, $limit, $break=".", $pad="...") {
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }

  return $string;
}

/* Fixed Gravity Form Conflict Js */
add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
    return true;
}

function get_page_id_by_template($fileName) {
    $page_id = 0;
    if($fileName) {
        $pages = get_pages(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => $fileName.'.php'
        ));

        if($pages) {
            $row = $pages[0];
            $page_id = $row->ID;
        }
    }
    return $page_id;
}

function string_cleaner($str) {
    if($str) {
        $str = str_replace(' ', '', $str); 
        $str = preg_replace('/\s+/', '', $str);
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
        $str = strtolower($str);
        $str = trim($str);
        return $str;
    }
}

function format_phone_number($string) {
    if(empty($string)) return '';
    $append = '';
    if (strpos($string, '+') !== false) {
        $append = '+';
    }
    $string = preg_replace("/[^0-9]/", "", $string );
    $string = preg_replace('/\s+/', '', $string);
    return $append.$string;
}

function get_instagram_setup() {
    global $wpdb;
    $result = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = 'sb_instagram_settings'" );
    if($result) {
        $option = ($result->option_value) ? @unserialize($result->option_value) : false;
    } else {
        $option = '';
    }
    return $option;
}

function extract_emails_from($string){
  preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
  return $matches[0];
}

function email_obfuscator($string) {
    $output = '';
    if($string) {
        $emails_matched = ($string) ? extract_emails_from($string) : '';
        if($emails_matched) {
            foreach($emails_matched as $em) {
                $encrypted = antispambot($em,1);
                $replace = 'mailto:'.$em;
                $new_mailto = 'mailto:'.$encrypted;
                $string = str_replace($replace, $new_mailto, $string);
                $rep2 = $em.'</a>';
                $new2 = antispambot($em).'</a>';
                $string = str_replace($rep2, $new2, $string);
            }
        }
        $output = apply_filters('the_content',$string);
    }
    return $output;
}

function get_social_links() {
    $social_types = array(
        'facebook'  => 'fab fa-facebook-square',
        'instagram' => 'fab fa-instagram',
        'twitter'   => 'fab fa-twitter-square',
        'linkedin'  => 'fab fa-linkedin-in',
        'youtube'   => 'fab fa-youtube'
    );
    $social = array();
    foreach($social_types as $k=>$icon) {
        $value = get_field($k,'option');
        if($value) {
            $social[$k] = array('link'=>$value,'icon'=>$icon,'name'=>$k);
        }
    }
    return $social;
}

function get_banner($postId) {
    $banner = get_field("banner",$postId);
    return ($banner) ? $banner : '';
}

function get_the_per_page() {
    $perPage = ( get_option('posts_per_page') ) ? get_option('posts_per_page') : 12;
    return $perPage;
}

/* Get Next Posts via Ajax */
add_action( 'wp_ajax_nopriv_get_next_posts', 'get_next_posts' );
add_action( 'wp_ajax_get_next_posts', 'get_next_posts' );
function get_next_posts() {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $excludeID = ($_POST['excludeID']) ? $_POST['excludeID'] : null;
        $paged = ($_POST['pg']) ? $_POST['pg'] : 1;
        $perpage = ($_POST['perpage']) ? $_POST['perpage'] : 6;
        $posttype = ($_POST['posttype']) ? $_POST['posttype'] : 'post';
        $term_id = ($_POST['termid']) ? $_POST['termid'] : '';
        $taxonomy = ($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
        if($term_id) {
            $catInfo['taxonomy'] = $taxonomy;
            $catInfo['term_id'] = $term_id;
        } else {
            $catInfo = null;
        }
        //$paged = $paged + ;
        $html = get_blog_posts($paged,$posttype,$perpage,$excludeID,$catInfo);
        $response['content'] = $html;
        $response['current_page'] = $paged;
        $response['next_page'] = $paged + 1;
        $response['perpage'] = $perpage;

        /* Check if Last Items */
        $nexpgNum = $paged + 1;
        $args2 = array(
            'posts_per_page'=> $perpage,
            'post_type'     => $posttype,
            'post_status'   => 'publish',
            'paged'         => $nexpgNum
        );
        if($excludeID) {
            $args2['post__not_in'] = array($excludeID);
        }
        if($term_id) {
            $args2['tax_query'] = array( 
                array(
                    'taxonomy' => $taxonomy, 
                    'field'    => 'term_id',
                    'terms'    => array($term_id), 
                )
            );
        }
        $nextPosts = get_posts($args2);
        $isLastBatch = ($nextPosts) ? false : true;
        $response['isLastBatch'] = $isLastBatch;

        echo json_encode($response);
    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
    die();
}

function get_blog_posts($paged,$post_type='post',$perpage=10,$exClude=null,$category=null) {
    $posts_per_page = $perpage;
    $content = '';
    $args = array(
        'posts_per_page'=> $posts_per_page,
        'post_type'     => $post_type,
        'post_status'   => 'publish',
        'paged'         => $paged
    );
    if($exClude) {
        $args['post__not_in'] = array($exClude);
    }

    if($category) {
        $catTax = $category['taxonomy'];
        $catId = $category['term_id'];
        $args['tax_query'] = array( 
                            array(
                                'taxonomy' => $catTax, 
                                'field'    => 'term_id',
                                'terms'    => array($catId), 
                            )
                        );
    }
    
    $blogs = new WP_Query($args);
    if ( $blogs->have_posts() ) { ob_start(); ?>

        <?php $j=1; while ( $blogs->have_posts() ) : $blogs->the_post(); 
            $id = get_the_ID();
            $content = get_the_content();
            $content = ($content) ? strip_tags($content) : '';
            $excerpt = ($content) ? shortenText($content,90,' ','&hellip;') : '';
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $featImage = wp_get_attachment_image_src($thumbnail_id,'medium_large');

            $thumbImage = get_field("thumbnail_image",$id);

            $placeholder = THEMEURI . 'images/portrait.png';
            $bClass = ($featImage) ? 'haspic':'nopic';
            ?>
            <article id="paged<?php echo $paged.'-'.$j ?>" data-postid="<?php echo $id ?>" data-pagegroup="<?php echo $paged ?>" class="post-item animated fadeIn">
                <a href="<?php echo get_permalink(); ?>" class="postlink <?php echo $bClass ?>">
                    <?php if ($thumbImage) { ?>
                        <span class="photo" style="background-image:url('<?php echo $thumbImage['url']?>')">
                            <img src="<?php echo $placeholder ?>" alt="" aria-hidden="true" />
                        </span>
                    <?php } ?>
                    <span class="text">
                        <h4 class="title"><?php echo get_the_title(); ?></h4>
                        <span class="excerpt"><?php echo $excerpt; ?></span>
                    </span>
                </a>
            </article>
        <?php $j++; endwhile; wp_reset_postdata(); ?>

    <?php
     $content = ob_get_contents();
     ob_end_clean();
    }
    return $content;
}


