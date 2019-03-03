<?php
add_action( 'add_admin_bar_menus', 'gr_add_admin_bar_menus' );
function gr_add_admin_bar_menus() {
/*
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
*/
	// Site related.
	remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 40 );
	add_action( 'admin_bar_menu', 'gr_admin_bar_wp_menu', 10 );

	// Content related.
	if ( ! is_network_admin() && ! is_user_admin() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
	}
	remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );

//	add_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );
}
function gr_admin_bar_wp_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'gr-logo',
		'title' => '<img class="gr-icon" src="'.get_stylesheet_directory_uri().'/images/gr-logo-t.png"/>',
		'href'  => 'http://www.gotta-ride.com',
		'meta'  => array(
			'title' => 'ゴッタライド',
		),
	) );
}
add_action( 'wp_head', 'gr_head' );
add_action( 'admin_head', 'gr_head' );
function gr_head() {
?>
<style type="text/css" media="screen">
#wpadminbar{background:#f5f5f5;border-bottom:1px solid #333;}
#wpadminbar .quicklinks a, #wpadminbar .quicklinks .ab-empty-item, #wpadminbar .shortlink-input, #wpadminbar { height: 40px; line-height: 40px; }
#wpadminbar #wp-admin-bar-gr-logo { background-color: #f5f5f5;}
#wpadminbar .gr-icon { vertical-align: middle; }
body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 40px;}
#wpadminbar .ab-top-secondary,
#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar #wp-admin-bar-gr-logo a:hover{background-color:transparent;background-image:none;color:#333;}
#screen-meta-links{display:none;}
#wpadminbar .ab-sub-wrapper, #wpadminbar ul, #wpadminbar ul li {background:#F5F5F5;}
#wpadminbar .quicklinks .ab-top-secondary > li > a, #wpadminbar .quicklinks .ab-top-secondary > li > .ab-empty-item,
#wpadminbar .quicklinks .ab-top-secondary > li {border-left: 1px solid #f5f5f5;}
#wpadminbar * {color: #333;text-shadow: 0 1px 0 #fff;}
</style>
<?php
}
add_filter( 'admin_footer_text', '__return_false' );
add_filter( 'update_footer', '__return_false', 9999 );
add_action( 'admin_notices', 'gr_update_nag', 0 );
function gr_update_nag() {
	if ( ! current_user_can( 'administrator' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}

// seko ページでは editor 非表示
add_action( 'admin_print_styles-post.php', 'bc_post_page_style' );
add_action( 'admin_print_styles-post-new.php', 'bc_post_page_style' );
function bc_post_page_style() {
	if ( in_array( $GLOBALS['current_screen']->post_type, array( 'seko', 'slide_img', 'chirashi','event' ,'voice','craftsman','staff','whatsnew','price','special','enquete' ) ) ) :
?>
<style type="text/css">
#postdivrich{display:none;}
#<?php global $current_screen; var_dump( $current_screen) ?>{}
</style>
<?php
	endif;
}


// カスタムフィールド&カスタム投稿タイプの追加
function gr_register_terms( $terms, $taxonomy ) {
	foreach ( $terms as $key => $label ) {
		$keys = explode( '/', $key );
		if ( 1 < count( $keys ) ) {
			$key = $keys[1];
			$parent_id = get_term_by( 'slug', $keys[0], $taxonomy )->term_id;
		} else {
			$parent_id = 0;
		}
		if ( ! term_exists( $key, $taxonomy ) ) {
			wp_insert_term( $label, $taxonomy, array( 'slug' => $key, 'parent' => $parent_id ) );
		}
	}
}

add_action( 'init', 'bc_create_customs', 0 );
function bc_create_customs() {

	// お知らせ
	register_post_type( 'whatsnew', array(
			'labels' => array(
		'name' => __( 'お知らせ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'whatsnew_cat', 'whatsnew', array(
			 'label' => 'お知らせカテゴリー',
		     'hierarchical' => true,
	) );

	// 施工事例
    register_post_type( 'seko', array(
        'labels' => array(
            'name' => __( '施工事例' ),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'supports' => array( 'title', 'editor','author' ),
    ) );

    register_taxonomy( 'seko_cat', 'seko', array(
         'label' => '施工事例カテゴリー',
         'hierarchical' => true,
    ) );



	register_taxonomy( 'seko_staff', 'seko', array(
		'label' => 'スタッフカテゴリー',
         	'hierarchical' => true,
	) );


	// イベント
	register_post_type( 'event', array(
			'labels' => array(
		'name' => __( 'イベント' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'event_cat', 'event', array(
			 'label' => 'イベントカテゴリー',
		     'hierarchical' => true,
	) );


		// よくあるご質問
	register_post_type( 'faq', array(
			'labels' => array(
		'name' => __( 'よくあるご質問' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'faq_cat', 'faq', array(
			 'label' => 'よくあるご質問',
		     'hierarchical' => true,
	) );


	// 現場日記
	register_post_type( 'genbanikki', array(
			'labels' => array(
		'name' => __( '現場日記' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'genba_cat', 'genbanikki', array(
			 'label' => '現場日記カテゴリー',
				     'hierarchical' => true,
	) );

	// 大型事例
	register_post_type( 'special', array(
			'labels' => array(
		'name' => __( '大規模リフォーム' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 19,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'special_cat', 'special', array(
			'label' => 'スタッフカテゴリー',
			'hierarchical' => true,
		 	'update_count_callback' => '_update_post_term_count',
		 	'singular_label' => 'スタッフカテゴリー',
		 	'public' => true,
		 	'show_ui' => true	) );

	// ありがとうの輪
	register_post_type( 'voice', array(
			'labels' => array(
		'name' => __( 'ありがとうの輪' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'voice_staff', 'voice', array(
			 'label' => 'スタッフカテゴリー',
		     'hierarchical' => true,
	) );

	// お客様アンケート
	register_post_type( 'enquete', array(
			'labels' => array(
		'name' => __( 'お客様の声' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );

	// スタッフ
	register_post_type( 'staff', array(
			'labels' => array(
		'name' => __( 'スタッフ' ),
			),
			'public' => true,
			'menu_position' => 5,
		    'has_archive' => true,
	'supports' => array( 'title', 'editor','author' ),
	) );
	register_taxonomy( 'staff_cat', 'staff', array(
			 'label' => 'スタッフカテゴリー',
		     'hierarchical' => true,
	) );

	// 職人
	register_post_type( 'craftsman', array(
			'labels' => array(
		'name' => __( '職人' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );



	// チラシ
	register_post_type( 'chirashi', array(
			'labels' => array(
		'name' => __( 'チラシ' ),
		'singular_name' => __( 'チラシ')
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );
		// アルバム
	register_post_type( 'album', array(
			'labels' => array(
		'name' => __( 'アルバム' ),
		'singular_name' => __( 'アルバム')
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor','author' ),
	) );

	// スタッフブログ
	register_post_type( 'staffblog', array(
			'labels' => array(
		'name' => __( 'スタッフブログ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor', 'comments' ),
	) );
	register_taxonomy( 'staffblog_cat', 'staffblog', array(
			 'label' => 'スタッフブログカテゴリー',
         		 'hierarchical' => true,
	) );


	// セミナー日程
	register_post_type( 'seminardata', array(
			'labels' => array(
		'name' => __( 'セミナー日程' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'author' ),
	) );



}

//// hooks
add_filter( 'wp_list_categories', 'gr_list_categories', 10, 2 );
function gr_list_categories( $output, $args ) {
	return preg_replace( '@</a>\s*\((\d+)\)@', ' ($1)</a>', $output );
}

add_action( 'pre_get_posts', 'gr_pre_get_posts' );
function gr_pre_get_posts( $query ) {
	if ( is_admin() ) {
		if ( in_array( $query->get( 'post_type' ), array( 'seko', 'staff' ) ) ) {
			$query->set( 'posts_per_page', -1 );
		}
		return;
	}
/*
	if ( is_post_type_archive() ) {
		if ( 'seko' == get_query_var( 'post_type' ) ) {
			$query->tax_query[] = array(
				'taxonomy' =>	'seko_cat',
				'term'     => 'kitchen',
				'field'    => 'slug',
			);
		}
	}
*/
}

function gr_adjacent_post_join( $join, $in_same_cat, $excluded_categories ) {
	if ( false && $in_same_cat ) {
		global $post, $wpdb;

		$taxonomy  = $post->post_type . '_cat';
		$terms     = implode( ',', wp_get_object_terms( $post->ID, $taxonomy, array('fields' => 'ids') ) );
		$join      = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		$join     .= $wpdb->prepare( " AND tt.taxonomy = %s AND tt.term_id IN ($terms)", $taxonomy );
	}

	return $join;
}

//// functions
function gr_title() {
	global $page, $paged;

	wp_title( '|', true, 'right' );
	bloginfo( 'name' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && is_front_page() )
		echo " | $site_description";

	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf(  '%sページ', max( $paged, $page ) );
}

function gr_description() {
	$desc = get_option( 'gr_description' );

	if ( is_front_page() || ! $desc ) {
		bloginfo( 'description' );
	} else {
		$title = str_replace( '|', '', wp_title( '|', false ) );
		echo str_replace( '%title%', $title, get_option( 'gr_description' ) );
	}
}

function gr_get_posts_count() {
	global $wp_query;
	return get_query_var( 'posts_per_page' ) ? $wp_query->found_posts : $wp_query->post_count;
}

function gr_get_pagename() {
	$pagename = '';

	if ( is_page() ) {
		/*
		$obj = get_queried_object();
		if ( 14 == $obj->post_parent )
			$pagename = 'business';
		else
		*/
			$pagename = get_query_var( 'pagename' );
	} elseif( ! $pagename = get_query_var( 'post_type' ) ) {
		//
	}

	return $pagename;
}

define( 'GR_IMAGES', get_stylesheet_directory_uri() . '/images/' );
function gr_img( $file, $echo = true ) {
	$img = esc_attr( GR_IMAGES . $file );

	if ( $echo )
		echo $img;
	else
		return $img;
}

function gr_get_post( $post_name ) {
	global $wpdb;
	$null = $_post = null;

	if ( ! $_post = wp_cache_get( $post_name, 'posts' ) ) {
		$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_name = %s LIMIT 1", $post_name ) );
		if ( ! $_post )
			return $null;
		_get_post_ancestors($_post);
		$_post = sanitize_post( $_post, 'raw' );
		wp_cache_add( $post_name, $_post, 'posts' );
	}

	return $_post;
}

function gr_get_permalink( $name, $taxonomy = '' ) {
	$link = false;

	if ( false && term_exists( $name, $taxnomy ) ) {
		$link = get_term_link( $name );
	} else if ( post_type_exists( $name ) ) {
		$link = get_post_type_archive_link( $name );
	} else {
		$_post = gr_get_post( $name );
		if ( $_post )
			$link = get_permalink( $_post );
	}

	return $link;
}

function gr_image_id( $key ) {
    $imagefield = post_custom( $key );
    return  preg_replace('/(\[)([0-9]+)(\])(http.+)?/', '$2', $imagefield );
}

function gr_get_image( $key, $att = '' ) {
	$id = gr_image_id( $key );

	if ( is_numeric( $id ) ) {
		if ( isset( $att['size'] ) ) {
			$size = $att['size'];
			unset( $att['size'] );
		}
		if ( isset( $att['width'] ) ) {
			$size = array( $att['width'], 200 );
			unset( $att['width'] );
		}
		return wp_get_attachment_image( $id, $size, false, $att );
	}

	if ( $id ) {
		/* ファイル存在チェック
		 * $id = /images/seko/289-2-t.jpg のようなパスでここに渡ってくるので
		 * get_stylesheet_directory_uri()のようなhttpで絶対パスを指定せず
		 * dirname(__FILE__)でチェック
		 */
		if( file_exists( dirname(__FILE__) . "$id" ) ) {
			return sprintf(
				'<img src="%1$s%2$s"%3$s%4$s%5$s />',
				get_stylesheet_directory_uri(),
				$id,
				( $att['width' ] ? ' width="' .$att['width' ].'"' : '' ),
				( $att['height'] ? ' height="'.$att['height'].'"' : '' ),
				( $att['alt'   ] ? ' alt="'   .$att['alt'   ].'"' : '' )
			);
		}
	}

	return '';
}

function gr_get_image_src( $key ) {
	$id = gr_image_id( $key );
	$src = '';

	if ( is_numeric( $id ) ) {
		@list( $src, $width, $height ) = wp_get_attachment_image_src( $id, $size, false );
	} else if ( $id ) {
		$src = get_stylesheet_directory_uri() . $id;
	}
	return $src;
}

function gr_contact_banner() {
?>
<!-- ======================問合わせテーブルここから======================= -->
<div class="contact">
	<a href="/contact"><img src="<?php echo get_template_directory_uri(); ?>/images/common/bnr_contact_rollout.gif" width="699" height="96" alt="お問い合わせ" /></a>
</div>
<!-- ======================問合わせテーブルここまで======================= -->

<?php
}

function gr_kaiyu() {
?>

<!-- ======================回遊バナーここから======================= -->
　<div class="kaiyu">
	<p class="mb10"><a href="/riyu"><img src="<?php echo get_template_directory_uri(); ?>/images/common/bnr_riyu_rollout.png" width="699" height="156" alt="モンテホームが選ばれる理由" /></a></p>
	<!--<p class="mb10"><a href="/shiharai"><img src="<?php echo get_template_directory_uri(); ?>/images/common/bnr_kinri0_rollout.png" width="699" height="156" alt="金利０キャンペーン！" /></a></p>-->
<!--<p class="mb10"><a href="/mukinri"><img src="<?php echo get_template_directory_uri(); ?>/images/common/bnr_mukinri_rollout.gif" width="699" height="156" alt="金利０キャンペーン！" /></a></p>-->
	<p><a href="/raiten"><img src="<?php echo get_template_directory_uri(); ?>/images/common/bnr_raiten_rollout.jpg" width="699" height="120" alt="ご来店予約" /></a></p>
</div>
<!-- ======================回遊バナーここまで======================= -->

<?php
}



if ( function_exists('register_sidebar') ) {
	register_sidebar( array(
				'name' => 'sidebar',
				'before_widget' => '',
				'after_widget' => '</ul>',
				'before_title' => '<p class="pic">',
				'after_title' => '</p><ul class="page_left_menu">',
	) );
}

//// enqueue
add_action( 'wp_print_styles', 'gr_print_styles' );
function gr_print_styles() {
	if( ! is_admin() ) {
		if ( is_front_page() ) {
			wp_enqueue_style( 'gr_orbit'  , get_stylesheet_directory_uri() . '/common/css/orbit.css' );
		}
		wp_enqueue_style( 'gr_common'   , get_stylesheet_directory_uri() . '/css/common.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'gr_enqueue_scripts' );
function gr_enqueue_scripts() {
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

	if ( ! is_admin() ) {
		wp_enqueue_script( 'jquery'	);//		, get_stylesheet_directory_uri() . '/common/js/jquery-1.5.1.min.js'			, array(		  ), false, true );
		if ( is_front_page() ) {
			wp_enqueue_script( 'gr_orbit'		, get_stylesheet_directory_uri() . '/common/js/jquery.orbit-1.2.3.min.js'	, array( 'jquery' ), false, true );
		}
		wp_enqueue_script( 'gr_rollover'	, get_stylesheet_directory_uri() . '/common/js/rollover2.js'				, array( 'jquery' ), false, true );
		wp_enqueue_script( 'gr_index'		  , get_stylesheet_directory_uri() . '/common/js/index.js'					, array( 'jquery', 'gr_shadowbox' ), false, true );
	}
}

//// admin

//add_action( 'admin_print_scripts-options-general.php', 'gr_options_general' );
add_action( 'admin_footer-options-general.php', 'gr_options_general' );
function gr_options_general() {
?>
<script type="text/javascript">
//<![CDATA[
(function($) {
	if($('body.options-general-php').length) {
		$('#blogdescription').parent().parent().before( $('#gr_companyname' ).parent().parent() );
		$('#blogdescription').parent().parent()
			.after( $('#gr_author' ).parent().parent() )
			.after( $('#gr_keywords' ).parent().parent() )
			.after( $('#gr_description' ).parent().parent() );
	}
})(jQuery);
//]]>
</script>
<?php
}

class GR_Admin {
	static private $options = NULL;

	public function GR_Admin() {
		$this->__construct;
	}

	public function __construct() {
		$this->options = array(
			array( 'id' => 'companyname', 'label' => '会社名'		     , 'desc' => '著作権表示用などに使用する会社名です。' ),
			array( 'id' => 'author'		, 'label' => '作成者'		     , 'desc' => 'サイトの作成者情報です。' ),
			array( 'id' => 'description', 'label' => 'ディスクリプション', 'desc' => '下層ページ用description' ),
			array( 'id' => 'keywords'	, 'label' => 'キーワード'	     , 'desc' => '半角コンマ（,）で区切って複数指定できます。' ),
		);
		add_action( 'admin_init'			, array( &$this, 'add_settings_fields' 		) );
		add_filter( 'whitelist_options'		, array( &$this, 'whitelist_options' 		) );
	}
	public function whitelist_options( $whitelist_options ) {
		foreach ( (array) $this->options as $option ) {
			$whitelist_options['general'][] = 'gr_' . $option['id'];
		}

		return $whitelist_options;
	}
	public function add_settings_fields() {
		foreach ( (array) $this->options as $key => $option ) {
			add_settings_field(
				$key+1, $option['label'], array( &$this, 'print_settings_field' ), 'general', 'default',
				array(
					'label_for' 	=> 'gr_' . $option['id'],
					'description' 	=> $option['desc'],
				)
			);
		}
	}
	public function print_settings_field( $args ) {
		printf(
			'<input name="%1$s" type="text" id="%1$s" value="%2$s" class="regular-text" />',
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) )
		);
		if ( ! empty( $args['description'] ) )
			printf(
				'<span class="description">%1$s</span>',
				esc_html( $args['description'] )
			);
	}
}

new GR_Admin;

/***************************************/

/**
 * 管理画面でのフォーカスハイライト
 */
function focus_highlight() {
	?>
		<style type="text/css">
		input:focus,textarea:focus{
			background-color: #dee;
		}
	</style>
		<?php
}

add_action( 'admin_head', 'focus_highlight' );

/**
 * 投稿での改行
 * [br] または [br num="x"] x は数字を入れる
 */
function sc_brs_func( $atts, $content = null ) {
	extract( shortcode_atts( array(
					'num' => '5',
					), $atts ));
	$out = "";
	for ($i=0;$i<$num;$i++) {
		$out .= "<br />";
	}
	return $out;
}

add_shortcode( 'br', 'sc_brs_func' );

//テンプレートURLショートコードで表示
add_shortcode('tmpl_url', 'shortcode_templateurl');
function shortcode_templateurl() {
return get_template_directory_uri();
}



//---------------------------------------------------------------------------
//\r\nの文字列の無効化
//---------------------------------------------------------------------------

add_filter('post_custom', 'fix_gallery_output');

function fix_gallery_output( $output ){
  $output = str_replace('rn', '', $output );
  return $output;
}


// echo fix_gallery_output(file_get_contents(__FILE__));

//---------------------------------------------------------------------------
//パンくず
//---------------------------------------------------------------------------

function the_pankuzu_keni( $separator = '　→　', $multiple_separator = '　|　' )
{
	global $wp_query;

	echo("<li><a href=\""); bloginfo('url'); echo("\">HOME</a>$separator</li>" );

	$queried_object = $wp_query->get_queried_object();

	if( is_page() )
	{
		//ページ
		if( $queried_object->post_parent )
		{
			echo( get_page_parents_keni( $queried_object->post_parent, $separator ) );
		}
		echo '<li>'; the_title(); echo '</li>';
	}
	else if( is_archive() )
	{
		if( is_post_type_archive() )
		{
			echo '<li>'; post_type_archive_title(); echo '</li>';
		}
		else if( is_category() )
		{
			//カテゴリアーカイブ
			if( $queried_object->category_parent )
			{
				echo get_category_parents( $queried_object->category_parent, 1, $separator );
			}
			echo '<li>'; single_cat_title(); echo '</li>';
		}
		else if( is_day() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('F j, Y','keni'))); echo '</li>';
		}
		else if( is_month() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('F Y','keni'))); echo '</li>';
		}
		else if( is_year() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('Y','keni'))); echo '</li>';
		}
		else if( is_author() )
		{
			echo '<li>'; _e('Archive List for authors','keni'); echo '</li>';
		}
		else if(isset($_GET['paged']) && !empty($_GET['paged']))
		{
			echo '<li>'; _e('Archive List for blog','keni'); echo '</li>';
		}
		else if( is_tag() )
		{
			//タグ
			echo '<li>'; printf( __('Tag List for %s','keni'), single_tag_title('',0)); echo '</li>';
		}
	}
	else if( is_single() )
	{
		$obj = get_post_type_object( $queried_object->post_type );
		if ( $obj->has_archive ) {
			printf(
				'<li><a href="%1$s">%2$s</a>%3$s</li>',
				get_post_type_archive_link( $obj->name ),
				apply_filters( 'post_type_archive_title', $obj->labels->name ),
				$separator
			);
		} else {
			//シングル
			echo '<li>'; the_category_keni( $separator, 'multiple', false, $multiple_separator ); echo '</li>';
			echo( $separator );
		}
		echo '<li>'; the_title(); echo '</li>';
	}
	else if( is_search() )
	{
		//検索
		echo '<li>'; printf( __('Search Result for %s','keni'), strip_tags(get_query_var('s'))); echo '</li>';
	}
	else
	{
		$request_value = "";
		foreach( $_REQUEST as $request_key => $request_value ){
			if( $request_key == 'sitemap' ){ $request_value = $request_key; break; }
		}

		if( $request_value == 'sitemap' )
		{
			echo '<li>'; _e('Sitemap','keni'); echo '</li>';
		}
		else
		{
			echo '<li>'; the_title(); echo '</li>';
		}
	}
}

function get_page_parents_keni( $page, $separator )
{
	$pankuzu = "";

	$post = get_post( $page );

	$pankuzu = '<li><a href="'. get_permalink( $post ) .'">' . $post->post_title . '</a>' . $separator . '</li>';

	if( $post->post_parent )
	{
		$pankuzu = get_page_parents_keni( $post->post_parent, $separator ) . $pankuzu;
	}

	return $pankuzu;
}

function the_category_keni($separator = '', $parents='', $post_id = false, $multiple_separator = '/') {
	echo get_the_category_list_keni($separator, $parents, $post_id, $multiple_separator);
}

function get_the_category_list_keni($separator = '', $parents='', $post_id = false, $multiple_separator = '/')
{
	global $wp_rewrite;
	$categories = get_the_category($post_id);
	if (empty($categories))
		return apply_filters('the_category', __('Uncategorized', 'keni'), $separator, $parents);

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, TRUE, $separator);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>';
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= $category->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i )
				$thelist .= $multiple_separator . ' ';
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, TRUE, $separator);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= "$category->cat_name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->name.'</a>';
			}
			++$i;
		}
	}
	return apply_filters('the_category', $thelist, $separator, $parents);
}
function get_6kajo(){
	include "yakusoku6.php";
}
function get_specials(){
	include "specials.php";
}
function get_menuohuro1(){
	include "menuohuro1.php";
}
function get_menuohuro2(){
	include "menuohuro2.php";
}

function get_menukitchen1(){
	include "menukitchen1.php";
}
function get_menukitchen2(){
	include "menukitchen2.php";
}

function get_menutoilet1(){
	include "menutoilet1.php";
}
function get_menutoilet2(){
	include "menutoilet2.php";
}

function get_menuyane1(){
	include "menuyane1.php";
}
function get_menuyane2(){
	include "menuyane2.php";
}

function get_menugaiheki1(){
	include "menugaiheki1.php";
}
function get_menugaiheki2(){
	include "menugaiheki2.php";
}

function get_menuyuka1(){
	include "menuyuka1.php";
}
function get_menuyuka2(){
	include "menuyuka2.php";
}

function get_menukabegami1(){
	include "menukabegami1.php";
}
function get_menukabegami2(){
	include "menukabegami2.php";
}

function get_menuj2w1(){
	include "menuj2w1.php";
}
function get_menuj2w2(){
	include "menuj2w2.php";
}
function get_menutaishin1(){
	include "menutaishin1.php";
}
function get_menutaishin2(){
	include "menutaishin2.php";
}

function get_menukyuto1(){
	include "menukyuto1.php";
}
function get_menukyuto2(){
	include "menukyuto2.php";
}


//ダッシュボードの記述▼

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets() {
global $wp_meta_boxes;

wp_add_dashboard_widget('custom_help_widget', 'ゴッタライドからのお知らせ', 'dashboard_text');
}
function dashboard_text() {
echo '<iframe src="http://www.gotta-ride.com/cloud/news.html" height=200 width=100% scrolling=no>
この部分は iframe 対応のブラウザで見てください。
</iframe>';
}

function example_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']); // 現在の状況
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']); // 最近のコメント
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); // 被リンク
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); // プラグイン
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); // クイック投稿
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']); // 最近の下書き
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); // WordPressブログ
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); // WordPressフォーラム
}
add_action('wp_dashboard_setup', 'example_remove_dashboard_widgets');

//ダッシュボードの記述▲

//投稿画面から消す▼

function remove_post_metaboxes() {
    remove_meta_box('tagsdiv-post_tag', 'post', 'normal'); // タグ
}
add_action('admin_menu', 'remove_post_metaboxes');

//投稿画面から消す▲ /ログイン時メニューバー消す▼

add_filter('show_admin_bar', '__return_false');

//ログイン時メニューバー消す▲　/アップデートのお知らせを管理者のみに　▼
if (!current_user_can('edit_users')) {
  function wphidenag() {
    remove_action( 'admin_notices', 'update_nag');
  }
  add_action('admin_menu','wphidenag');
}

//アップデートのお知らせ▲

/**
 *
 * 最新記事のIDを取得
 * @return  Int ID
 *
 */
function get_the_latest_ID() {
    global $wpdb;
    $row = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC");
    return !empty( $row ) ? $row->ID : '0';
}
function the_latest_ID() {
    echo get_the_latest_ID();
}

/*ＩＤ取得*/

function get_gaiyobar(){

echo <<<BNR

<div class="content_gaiyobt">
<h3><img src="/wp-content/themes/gaiheki/page_image/gaiyo/tit_gaiyobt.gif" width="269" height="17" alt="株式会社ダイケンリハウス 会社概要" title="株式会社ダイケンリハウス 会社概要" /></h3>
<ul>
	<li><a href="/company/" title="会社案内"><img src="/wp-content/themes/gaiheki/page_image/gaiyo/bt_gaiyo1_rollout.gif" width="222" height="52" alt="会社案内" /></a></li>
	<li><a href="/staff/" title="スタッフ紹介"><img src="/wp-content/themes/gaiheki/page_image/gaiyo/bt_gaiyo5_rollout.gif" width="222" height="52" alt="スタッフ紹介" /></a></li>
	<li><a href="/voice/" title="ありがとうの輪"><img src="/wp-content/themes/gaiheki/page_image/gaiyo/bt_gaiyo7_rollout.gif" width="222" height="52" alt="ありがとうの輪" /></a></li>
</ul>
</div>
BNR;

}


function get_tenpoinfo(){

echo <<<BNR

<div class="content_gaiyo_list">
<h3><img src="/wp-content/themes/gaiheki/page_image/company/tit_tenpolist.gif" width="730" height="67" alt="店舗一覧" /></h3>
<ul>
	<li><a href="/company/yasu"><img src="/wp-content/themes/gaiheki/page_image/company/bnr_yasu_rollout.jpg" width="165" height="110" alt="野洲店を見る" /></a></li>
	<li><a href="/company/katata"><img src="/wp-content/themes/gaiheki/page_image/company/bnr_katata_rollout.jpg" width="165" height="110" alt="堅田店を見る" /></a></li>
	<li><a href="/company/moriyama"><img src="/wp-content/themes/gaiheki/page_image/company/bnr_moriyama_rollout.jpg" width="165" height="110" alt="守山駅前店を見る" /></a></li>
	<li><a href="/company/minakuchi"><img src="/wp-content/themes/gaiheki/page_image/company/bnr_mizuguchi_rollout.jpg" width="165" height="110" alt="水口店を見る" /></a></li>
</ul>
</div>
BNR;

}

//リフォームメニュー　一覧のURL取得処理
function getReformListUrl($cat,$post_id){
	$terms = get_the_terms($post_id,'soudan_cat');
	foreach($terms as $term){
		if($term->slug === $cat){
		  $link = get_term_link((int)$term->term_id,'soudan_cat'). '#' . $post_id;
		  break;
		}
	}
	return $link;
}

//トップ施工事例部分
function getTopSeko($post_id){
	query_posts( array( 'seko_cat' => $post_id, 'posts_per_page' => 3 ));
	if(have_posts()) : while (have_posts()) : the_post();

        echo '<p class="c_jirei">';
        echo '<a href="';
        the_permalink();
        echo '"><span class="im_box">';
        if(post_custom('seko_after_image')){
		printf(
			'%s',
			gr_get_image(
				'seko_after_image',
				array( 'width' => 216, 'alt' => esc_attr( post_custom( 'seko_comment' ) ) )
			)
		);
	}
        else if(post_custom('seko_point_image01')){
		printf(
			'%s',
			gr_get_image(
				'seko_point_image01',
				array( 'width' => 216, 'alt' => esc_attr( post_custom( 'seko_comment' ) ) )
			)
		);
	}
	else if($img = post_custom('seko_csv01')){
		echo '<img src="/wp-content/themes/gaiheki/page_image' . $img . '" width="216" alt="" />';
	}
	else if($img = post_custom('seko_csv2')){
		echo '<img src="/wp-content/themes/gaiheki/page_image' . $img . '" width="216" alt="" />';
	}
	else if($img = post_custom('seko_csv3')){
		echo '<img src="/wp-content/themes/gaiheki/page_image' . $img . '" width="216" alt="" />';
	}
	else if($img = post_custom('seko_csv4')){
		echo '<img src="/wp-content/themes/gaiheki/page_image' . $img . '" width="216" alt="" />';
	}
	else if($img = post_custom('seko_csv5')){
		echo '<img src="/wp-content/themes/gaiheki/page_image' . $img . '" width="216" alt="" />';
	}
	echo '</span><br />';
        echo '<b>' . get_the_title();
	if( post_custom('seko_newicon') ){
	 	echo '<img src="/wp-content/themes/gaiheki/page_image/new.gif" width="30" height="10" alt="NEW" />';
	};
        echo '</b><br />';
        echo post_custom( 'seko_city' ) . post_custom( 'seko_name' );
        echo '<br />';
        echo '費用：' . post_custom( 'seko_price' );
        echo '工期：' . post_custom( 'seko_duration' ) . '<br />';
        echo '<span class="array_jirei">施工事例を見る</span></a></p>';

	endwhile; endif; wp_reset_query();
}
//店舗別スタッフ紹介
function tenpoStaffList($tenpo,$belong){
	$args = array(
		'tax_query' => array(
		'relation' => 'AND',
			array(
				'taxonomy' => 'staff_cat',
				'terms' => array( $tenpo ),
				'field' => 'slug',
				'operator' => 'IN',
			),
			array(
				'taxonomy' => 'staff_cat',
				'terms' => array( $belong ),
				'field' => 'slug',
				'operator' => 'IN',
			),
		),
		'posts_per_page' => 15,
	);
	query_posts( $args );
	if(have_posts()) :
	echo '<h4><img src="/wp-content/themes/gaiheki/page_image/staff/' . $belong . '.gif" width="92" alt="" /></h4>';
	echo '<ul>';

	while (have_posts()) : the_post();
	echo '<li><a href="';
	the_permalink();
	echo '">' . gr_get_image('staff_new_img',array('width'=>'78')) . '</a><div><span><strong>' . get_the_title() . '</strong>' . post_custom('staff_name_romaji') . '</br>';
	echo post_custom('staff_belongs') .'</span></div></li>';
	endwhile;

	echo '</ul>';
	endif;
}
function tenpoStaff($tenpo){
	tenpoStaffList($tenpo,'belong_responsible');
	tenpoStaffList($tenpo,'belong_estate');
	tenpoStaffList($tenpo,'belong_reform');
	tenpoStaffList($tenpo,'belong_execution');
	tenpoStaffList($tenpo,'belong_drafting');
	tenpoStaffList($tenpo,'belong_planning');
	tenpoStaffList($tenpo,'belong_general');
	tenpoStaffList($tenpo,'belong_sales');
	tenpoStaffList($tenpo,'belong_office');
}

function get_bottombnr(){

echo <<<BNR

<ul class="content_bnrgroup">
<li style="float:left; margin:0 19px 0 0;"><a href="http://www.daiken-tosou.com/knowledge/money"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_kakakuhyo_rollout.jpg" width="360" height="119" alt="塗り替えリフォーム価格表"></a></li>
<li><a href="http://www.daiken-tosou.com/knowledge/paint"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_hikaku_rollout.jpg" width="360" height="119" alt="価格と機能でわかる塗料比較"></a></li>
<li><a href="http://www.daiken-tosou.com/campaign"><img src="/wp-content/themes/gaiheki/images/common/bnr_campaign2_rollout.jpg" width="740" height="122" alt="外壁塗装リフォームキャンペーン" class="mb-10"></a></li>
<li style="float:left; margin:0 19px 0 0;"><a href="http://www.daiken-tosou.com/gaina/"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_gaina_rollout.jpg" width="360" height="155" alt="ＴＶでも話題のエコ塗料　遮熱・断熱効果で夏は涼しく冬は暖かい！耐用年数１２～１５年ＧＡＩＮＡ（ガイナ）８９８,０００円（税抜）"></a></li>
<li><a href="http://www.daiken-tosou.com/muki/"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_muki_rollout.jpg" width="360" height="155" alt="フッ素より長持ち超高耐久塗料　耐用年数２０年スーパームキコート７９８，０００円（税抜）"></a></li>
<li><a href="http://www.daiken-tosou.com/knowledge/kodawari"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_kodawari_rollout.jpg" width="740" height="120" alt="こだわりの施工技術 お客様から選ばれている理由はこちら"></a></li>
<li class="unosp"><a href="http://www.daiken-tosou.com/knowledge/first"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_five_rollout.jpg" width="740" height="122" alt="屋根・外壁リフォームでよくあるトラブル"></a></li>
<li class="tnosp"><a href="http://www.daiken-tosou.com/book"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_book_rollout.jpg" width="740" height="133" alt="外壁塗装をご検討中の方にぜひ読んでいただきたい小冊子 無料プレゼント"></a></li>
</ul>

<div class="kaiyuseko_border_s_d4d4d4">
<a href="http://www.daiken-tosou.com/seko_cat/gaiheki"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_gaiheki02_rollout.jpg" width="227" height="95" alt="外壁塗装 施工事例" class="mb15"></a>
<a href="http://www.daiken-tosou.com/seko_cat/yane"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_yane02_rollout.jpg" width="227" height="95" alt="屋根リフォーム 施工事例" class="mb15"></a>
<a href="http://www.daiken-tosou.com/seko_cat/ibaragi"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_ibaragi02_rollout.jpg" width="227" height="95" alt="茨城県 施工事例" class="mb15"></a>
<br clear="all">
<a href="http://www.daiken-tosou.com/seko_cat/cellicon"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_cellicon_rollout.jpg" width="227" height="95" alt="シリコン塗料 施工事例" class="mb15"></a>
<a href="http://www.daiken-tosou.com/seko_cat/muki"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_muki_rollout.jpg" width="227" height="95" alt="スーパームキコート 施工事例" class="mb15"></a>
<a href="http://www.daiken-tosou.com/seko_cat/gaina"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_gaina_rollout.jpg" width="227" height="95" alt="ガイナ 施工事例" class="mb15"></a>
<br clear="all">
<img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_more.jpg" width="240" height="40" alt="ガイナ まだまだあります" class="f-l">
<a href="http://www.daiken-tosou.com/seko"><img src="/wp-content/themes/gaiheki/page_image/kaiyu/kaiyu_seko_allmore_rollout.jpg" width="461" height="40" alt="すべての施工事例はこちら"></a>
<br clear="all">
</div>

BNR;

}

//カレンダー
function widget_customCalendar($args) {
	extract($args);
	echo $before_widget;
	echo get_calendar_custom(カスタム投稿名);
	echo $after_widget;
}


function get_calendar_custom($posttype,$initial = true) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$key = md5( $m . $monthnum . $year );
	if ( $cache = wp_cache_get( 'get_calendar_custom', 'calendar_custom' ) ) {
		if ( isset( $cache[ $key ] ) ) {
			echo $cache[ $key ];
			return;
		}
	}

	ob_start();
	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if ( !$gotsome )
			return;
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE post_date < '$thisyear-$thismonth-01'

		AND post_type = '$posttype' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");

	$next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE post_date >	'$thisyear-$thismonth-01'

		AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
		AND post_type = '$posttype' AND post_status = 'publish'
			ORDER	BY post_date ASC
			LIMIT 1");

	echo '<div id="calendar_wrap">
	<table id="wp-calendar" summary="' . __('Calendar') . '">
	<caption>' . date('Y年n月', $unixmonth) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	echo '
	</tr>
	</tfoot>
	<tbody>
	<tr>';

	// Get days with posts
	$dyp_sql = "SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts

		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE MONTH(post_date) = '$thismonth'

		AND YEAR(post_date) = '$thisyear'
		AND post_type = '$posttype' AND post_status = 'publish'
		AND post_date < '" . current_time('mysql') . "'";

	$dayswithposts = $wpdb->get_results($dyp_sql, ARRAY_N);

	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "

		."LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) "
		."LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) "

		."WHERE YEAR(post_date) = '$thisyear' "

		."AND MONTH(post_date) = '$thismonth' "
		."AND post_date < '".current_time('mysql')."' "
		."AND post_type = '$posttype' AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = apply_filters( "the_title", $ak_post_title->post_title );
				$post_title = str_replace('"', '&quot;', wptexturize( $post_title ));

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
			echo '<td id="today">';
		else
			echo '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				echo '<a href="' .  $home_url . '/' . $posttype .  '/date/' . $thisyear . '/' . $thismonth . '/' . $day . "\">$day</a>";
		else
			echo $day;
		echo '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table></div>";

	echo "\n\t<div class=\"calender_navi\"><table cellspacing=\"0\" cellpadding=\"0\"><tr>";

	if ( $previous ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3" id="prev"><a href="' .  $home_url . '/' . $posttype .  '/date/' . $previous->year . '/' . $previous->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),			date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .  $home_url . '/' . $posttype .  '/date/' . $next->year . '/' . $next->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),			date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}
	echo "\n\t</tr></table></div>";

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	$cache[ $key ] = $output;
	wp_cache_set( 'get_calendar_custom', $cache, 'calendar_custom' );
}
//カレンダー



//施工事例関数化
function cateSeko($id,$kensu){ ?>
<div class="content_seko_detail">
<? $i = 0;
$x = 0; ?>
<?php query_posts( array( 'seko_cat' => $id, 'posts_per_page' => $kensu )); ?>
<?php if(have_posts()) : while( have_posts() ) : the_post(); ?>
		<div class="box">
			<a href="<?php the_permalink() ?>" class="<?php if($i%3==0){ echo " clear_l "; $x++; } ?>heightLine-group<?php echo $x; ?>">
			<div class="img">
				<?php
global $post;
$Tokuchou = get_post_meta($post->ID,'seko_sekomachi');

if(in_array("施工待ち",$Tokuchou)):?>
				<span class="stamp"><img src="<?php echo site_url(); ?>/wp-content/themes/gaiheki/images/common/icon_machi.png" width="78" height="78" alt="施工待ち" /></span>
				<?php
						elseif(in_array("施工中",$Tokuchou)):?>
				<span class="stamp"><img src="<?php echo site_url(); ?>/wp-content/themes/gaiheki/images/common/icon_chu.png" width="78" height="78" alt="施工中" /></span>
				<?php
						endif;

        if(post_custom('seko_after_image')){
	printf(
		'%s',
		gr_get_image(
			'seko_after_image',
			array( 'width' => 205, 'alt' => esc_attr( get_the_title() ), 'title' => esc_attr( get_the_title() ) )
		)
	);
}
        else if(post_custom('seko_point_image01')){
	printf(
		'%s',
		gr_get_image(
			'seko_point_image01',
			array( 'width' => 205, 'alt' => esc_attr( get_the_title() ), 'title' => esc_attr( get_the_title() ) )
		)
	);
}
else if($img = post_custom('seko_csv01')){
	echo '<img src="/wp-content/themes/reform/page_image' . $img . '" width="205" alt="" />';
}
else if($img = post_custom('seko_csv2')){
	echo '<img src="/wp-content/themes/reform/page_image' . $img . '" width="205" alt="" />';
}
else if($img = post_custom('seko_csv3')){
	echo '<img src="/wp-content/themes/reform/page_image' . $img . '" width="205" alt="" />';
}
else if($img = post_custom('seko_csv4')){
	echo '<img src="/wp-content/themes/reform/page_image' . $img . '" width="205" alt="" />';
}
else if($img = post_custom('seko_csv5')){
	echo '<img src="/wp-content/themes/reform/page_image' . $img . '" width="205" alt="" />';
}
	?>
			</div>
			<p class="txt heightLine-group<?php echo $x; ?>_txt"><span class="new">
				<? if(post_custom('seko_newicon')){
if( post_custom('seko_newicon') ){
 	echo '<img src="/wp-content/themes/reform/page_image/seko/new.gif" width="30" height="10" alt="new" />';
}} ?>
				</span><?php echo post_custom( 'seko_city' ); ?> <?php echo post_custom( 'seko_name' ); ?><br />
				<span class="tit">
				<? the_title(); ?>
				</span><br />
				工期：<span class="koki"><?php echo post_custom( 'seko_duration' ); ?></span>　費用：<span class="hiyo"><?php echo post_custom( 'seko_price' ); ?></span></p>
			<div class="more">
				<img src="<?php bloginfo('template_url'); ?>/page_image/seko/seko_more.gif" alt="詳しく見る" width="180" height="30" class="over" />
			</div>
			</a>
		</div>
<?php
$i++;
endwhile; ?>
<?php else : ?>
<p>現在登録されておりません。</p>
<?php endif; ?>
<?php wp_reset_query(); ?>
</div>
<? } ?>
