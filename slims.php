<?php
/**
 * SLiMS Catalog Integration
 *
 * @package           SLiMSCatalog
 * @author            Ari Nugraha
 * @copyright         Ari Nugraha
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       SLiMS Catalog Integration
 * Plugin URI:        https://dicarve.com/slims-wp
 * Description:       Integrate SLiMS Catalog into WordPress. This plugins uses SLiMS JSON/XML catalog service to fetch catalog records. Compatible with SLiMS 9.
 * Version:           0.5.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ari Nugraha
 * Author URI:        https://dicarve.blogpsot.com
 * Text Domain:       slims-opac
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://dicarve.com/slims-wp
 */

define( 'SLIMS_PAGING_PAGE_VARNAME', 'spage' );
define( 'SLIMS_PLUGIN_BASE', plugin_basename( __FILE__ ) );

/**
 * SLiMS Activation hook function
 */
function slims_activate() {
  // register pages for SLiMS opac
  if (!get_page_by_path('biblio-opac')) {
    $biblio_opac_page = array(
        'post_name' => 'biblio-opac',
        'post_title'    => 'OPAC',
        'post_content'  => '<!-- wp:shortcode -->[slims_search_block]<!-- /wp:shortcode --><!-- wp:shortcode -->[slims_biblio_opac]<!-- /wp:shortcode -->',
        'post_status'   => 'publish',
        'post_type' => 'page',
        'comment_status ' => 'closed'
      );
  }

  if (!get_page_by_path('biblio-detail')) {
    // register pages for SLiMS biblio detail
    $biblio_detail_page = array(
        'post_name' => 'biblio-detail',
        'post_title'    => 'Detail Koleksi',
        'post_content'  => '<!-- wp:shortcode -->[slims_biblio_detail]<!-- /wp:shortcode -->',
        'post_status'   => 'publish',
        'post_type' => 'page',
        'comment_status ' => 'closed'
    );    
  }
  
  // Insert the pages into the database
  $insert_page1 = wp_insert_post( $biblio_opac_page );
  $insert_page2 = wp_insert_post( $biblio_detail_page );
}
register_activation_hook( __FILE__, 'slims_activate' );

/**
 * SLiMS Deactivation hook function
 */
function slims_deactivate() {

}
register_deactivation_hook( __FILE__, 'slims_deactivate' );

/**
 * SLiMS specific stylesheet and js registration
 **/ 
function slims_register_css_js() {
  wp_enqueue_style( 'slims-main-style', plugins_url('/public/css/slims.css', __FILE__), array(), time() );
  wp_enqueue_script( 'slims-main-js', plugins_url('/public/js/slims.js', __FILE__), array(), time(), array('in_footer' => true) );
  wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.7.1.slim.min.js', null, null, true );
}
add_action( 'wp_enqueue_scripts', 'slims_register_css_js' );


function _slims_query($query_string = '', $page = 1, $adv_search = array()) {
	if ($query_string) {
		$query_string = urlencode($query_string);	
	}
    if ($page < 1) {
        $page = 1;
    }

    $slims_config = get_option( 'slims_options' );

    // using JSON or XML
    $fetch_method = 'JSONLD=true';
    if ($slims_config['slims_field_fetch_method'] == 'xml') {
        $fetch_method = 'resultXML=true';
    }
	
    if ($adv_search) {
        $query = http_build_query($adv_search);
        $ch = curl_init($slims_config['slims_base_url']."/index.php?$fetch_method&$query&search=search&page=$page");
    } else {
        $ch = curl_init($slims_config['slims_base_url']."/index.php?$fetch_method&keywords=$query_string&search=search&page=$page");
    }
	
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$slims_results = curl_exec($ch);	
	$httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
	curl_close($ch);
	
	if ($httpcode != 200) {
		return null;
	} else {
        if ($slims_config['slims_field_fetch_method'] == 'json') {
            $records = json_decode($slims_results, true);
        } else {
            $records = new SimpleXMLElement($slims_results);
        }
		return $records;
	}
}

function _slims_biblio_detail_query($biblio_id) {
	$biblio_id = (int) $biblio_id;	
    $slims_config = get_option( 'slims_options' );

    // using JSON or XML
    $fetch_method = 'JSONLD=true';
    if ($slims_config['slims_field_fetch_method'] == 'xml') {
        $fetch_method = 'inXML=true';
    }

	$ch = curl_init($slims_config['slims_base_url']."/index.php?p=show_detail&$fetch_method&id=$biblio_id");
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$slims_result = curl_exec($ch);	
	$httpcode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
	curl_close($ch);
	
	if ($httpcode != 200) {
		// something wrong with the response so we return null
		return null;
	} else {
        if ($slims_config['slims_field_fetch_method'] == 'json') {
            $record = json_decode($slims_result, true);
        } else {
            $record = new SimpleXMLElement($slims_result);
        }
		return $record;
	}
}

function slims_new_titles_shortcode() {
    $output = NULL;
    $slims_config = get_option( 'slims_options' );

	$biblio_result = _slims_query();
	if ($biblio_result) {
    	// Start output buffering
    	ob_start();
    	// Include the template file
    	require_once plugin_dir_path(__FILE__) . 'public/views/new-titles.php';
    	// End buffering and return its contents
    	$output = ob_get_clean();
  
    	return $output;		
	} else {
		return '<div class="card slims-no-result notice">No New Titles</div>';
	}
}

function slims_search_block_shortcode() {
    $output = NULL;

    $slims_config = get_option( 'slims_options' );
    // Start output buffering
    ob_start();
    // Include the template file
    require_once plugin_dir_path(__FILE__) . 'public/views/search-block.php';
    // End buffering and return its contents
    $output = ob_get_clean();
  
    return $output;
}

function slims_biblio_opac_shortcode() {
    $output = NULL;
    $keywords = null;
    $adv_search = array();
    $page = 1;
    $slims_config = get_option( 'slims_options' );

    if (isset($_GET['keywords'])) {
        $keywords = sanitize_text_field($_GET['keywords']);
        if (!empty($_GET['title']) OR !empty($_GET['author']) OR !empty($_GET['topic'])) {
            $keywords = null;
        }
        if (!empty($_GET['title'])) {
            $adv_search['title'] = sanitize_text_field($_GET['title']);
        }
        if (!empty($_GET['author'])) {
            $adv_search['author'] = sanitize_text_field($_GET['author']);
        }
        if (!empty($_GET['topic'])) {
            $adv_search['subject'] = sanitize_text_field($_GET['topic']);
        }
    }

    if (isset($_GET[SLIMS_PAGING_PAGE_VARNAME])) {
        $page = $_GET[SLIMS_PAGING_PAGE_VARNAME];
    }
	$biblio_result = _slims_query($keywords, $page, $adv_search);
	if ($biblio_result) {
    	// Start output buffering
    	ob_start();
    	// Include the template file
    	require_once plugin_dir_path(__FILE__) . 'public/views/opac.php';
    	// End buffering and return its contents
    	$output = ob_get_clean();
  
    	return $output;
	} else {
		return '<div class="slims-no-result notice">No Collection Found Yet</div>';
	}
}

function slims_biblio_detail_shortcode() {
    $output = NULL;
    $slims_config = get_option( 'slims_options' );

    if (!isset($_GET['biblio_id']) || empty($_GET['biblio_id'])) {
        return $output;
    }

    $biblio_id = $_GET['biblio_id'];
    $biblio_detail = _slims_biblio_detail_query($biblio_id);
	
    // Start output buffering
    ob_start();
    // Include the template file
    require_once plugin_dir_path(__FILE__) . 'public/views/detail.php';
    // End buffering and return its contents
    $output = ob_get_clean();
  
    return $output;
}

/**
 * 
 * Register shortcode
 * 
 */
add_shortcode( 'slims_new_titles', 'slims_new_titles_shortcode' );
add_shortcode( 'slims_search_block', 'slims_search_block_shortcode' );
add_shortcode( 'slims_biblio_opac', 'slims_biblio_opac_shortcode' );
add_shortcode( 'slims_biblio_detail', 'slims_biblio_detail_shortcode' );

/* SLiMS setting page */
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

/**
 * UTILITY FUNCTIONS
 */

function paging($base_url, $total_rows, $rows_per_page = 10, $pages_per_section = 10, $page_var_name = 'page', $url_fragment = '', $target_frame = '_self') {
  // check for wrong arguments
  if ($total_rows <= $rows_per_page) {
      return;
  }
  // total number of pages
  $pages_total = ceil($total_rows/$rows_per_page);
  if ($pages_total < 2) {
      return;
  }
  // total number of pager set
  $pages_section = ceil($pages_total/$pages_per_section);
  // check the current page number
  $_page = 1;
  if (isset($_GET[$page_var_name]) AND $_GET[$page_var_name] > 1) {
      $_page = (integer)$_GET[$page_var_name];
  }

  $_current_page = $base_url."?$page_var_name=";
  // check the query string
  if (isset($_SERVER['QUERY_STRING']) AND !empty($_SERVER['QUERY_STRING'])) {
      parse_str($_SERVER['QUERY_STRING'], $arr_query_var);
      // rebuild query str without "page" var
      $_query_str_page = '';
      foreach ($arr_query_var as $varname => $varvalue) {
          if (is_string($varvalue)) {
              $varvalue = urlencode($varvalue);
              if ($varname != $page_var_name) {
                  $_query_str_page .= $varname.'='.$varvalue.'&';
              }
          } else if (is_array($varvalue)) {
              foreach ($varvalue as $e_val) {
                  if ($varname != $page_var_name) {
                      $_query_str_page .= $varname.'[]='.$e_val.'&';
                  }
              }
          }
      }
      // append "page" var at the end
      $_query_str_page .= "$page_var_name=";
      // create full URL
      $_current_page = $base_url.'?'.$_query_str_page;
  }
  // target frame
  $_target_frame = 'target="'.$target_frame.'"';
  // init the return string
  $_buffer = '<div class="paging">';
  $_stopper = 1;
  $_pager_offset = 1;
  // count the offset of paging
  if (($_page > 5) AND ($_page%5 == 1)) {
      $_lowest = $_page-5;
      if ($_page == $_lowest) {
          $_pager_offset = $_lowest;
      } else {
          $_pager_offset = $_page;
      }
  } else if (($_page > 5) AND (($_page*2)%5 == 0)) {
      $_lowest = $_page-5;
      $_pager_offset = $_lowest+1;
  } else if (($_page > 5) AND ($_page%5 > 1)) {
      $_rest = $_page%5;
      $_pager_offset = $_page-($_rest-1);
  }
  // Previous page link
  $_first = __('First Page');
  $_prev = __('Previous');
  if ($_page > 1) {
      $_buffer .= '<span class="paging-first-link"><a href="'.$_current_page.(1).$url_fragment.'" '.$_target_frame.'>'.$_first.'</a></span>';
      $_buffer .= '<span class="paging-prev-link"><a href="'.$_current_page.($_page-1).$url_fragment.'" '.$_target_frame.'>'.$_prev.'</a></span>';
  }
  for ($p = $_pager_offset; ($p <= $pages_total) AND ($_stopper < $pages_per_section+1); $p++) {
      if ($p == $_page) {
          $_buffer .= '<span class="paging-current-page">'.$p.'</span>';
      } else {
          $_buffer .= '<span class="paging-link"><a href="'.$_current_page.$p.$url_fragment.'" '.$_target_frame.'>'.$p.'</a></span>';
      }
      $_stopper++;
  }
  // Next page link
  $_next = __('Next');
  if (($_pager_offset != $pages_total-4) AND ($_page != $pages_total)) {
      $_buffer .= '<span class="paging-next-link"><a href="'.$_current_page.($_page+1).$url_fragment.'" '.$_target_frame.'>'.$_next.'</a></span>';
  }
  // Last page link
  $_last = __('Last Page');
  if ($_page < $pages_total) {
      $_buffer .= '<span class="paging-last-link"><a href="'.$_current_page.($pages_total).$url_fragment.'" '.$_target_frame.'>'.$_last.'</a></span>';
  }
  $_buffer .= '</div>';

  return $_buffer;
}


/**
 * 
 * Function to to ellipse long text
 * 
 **/
function _ellipse($text, $maxlen, $ellip='...', $towords=TRUE) {
  // trim whitespace
  $text = trim($text);

  // do nothing if we're shorter than maxlen
  if (strlen($text) <= $maxlen) {
    return $text;
  }

  // if maxlen is less than the ellip symbol, make maxlen = length of ellip
  $maxlen = strlen($ellip) > $maxlen ? strlen($ellip) : $maxlen;

  // we're longer than maxlen. First thing we do is shorten to maxlen - $ellip
  $_text = substr($text, 0, $maxlen - strlen($ellip));

  if ($towords) {
    $_text = strrev($_text);
    // if we're matching to complete words we look for the last instance of a
    // sentence terminator
    $pattern = '/\s/';
    $count = preg_match($pattern, $_text, $matches);
    $_text = strrev(substr($_text, strpos($_text, $matches[0])));
    return $_text . $ellip;
  } else {
    // Rules
    // 1. If the last char in a shortened string is inside a just append the ellipsis, e.g. hello => hel...
    // 2. If the last char in a shortened string is a space, append the ellipsis after the space e.g. hello how => hello ...
    // 3. If the last char in a shortended string is the last letter in a word, remove an extra character from the string e.g. hello => hell...

    // we decide what to do here based on the last character of the shortened message
    // and the character which FOLLOWS the last character in the shortened message
    $last_char = $text[$maxlen - strlen($ellip)-1];
    $switch_char = $text[$maxlen - strlen($ellip)];

    // last character is a space or both are non-terminating characters
    $terminators = array('.',',','!','?','%');
    if (($last_char == ' ') || ( ! in_array($last_char, $terminators) && ! in_array($switch_char, $terminators) && $switch_char != ' '))
      return $_text . $ellip;
    elseif ( ! in_array($last_char, $terminators) && (in_array($switch_char, $terminators) || $switch_char == ' '))
      return substr($_text, 0, strlen($_text)-1) . $ellip;
    elseif ( ! in_array($last_char, $terminators) && ! in_array($switch_char, $terminators) && $switch_char != ' ')
      return $_text . $ellip;
    else {
      // we get here if the last char and switch char are both terminators. e.g. '. '
      // in this situation we run ourselves again but with the characters trimmed off
      return ellipse($text, $maxlen-1, $ellip, $towords);
    }
  }
}

function get_biblio_id($str_url) {
    $slims_config = get_option( 'slims_options' );
    return preg_replace('/^.+p=show_detail&id=/i', '', $str_url);
}