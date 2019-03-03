<?php get_header();
function get_the_post_image_src($postid,$size,$order=0,$max=null) {
    $attachments = get_children(array('post_parent' => $postid, 'post_type' => 'attachment', 'post_mime_type' => 'image'));
    if ( is_array($attachments) ){
        foreach ($attachments as $key => $row) {
            $mo[$key]  = $row->menu_order;
            $aid[$key] = $row->ID;
        }
        array_multisort($mo, SORT_ASC,$aid,SORT_DESC,$attachments);
        $max = empty($max)? $order+1 :$max;
        for($i=$order;$i<$max;$i++){
            return wp_get_attachment_image_src( $attachments[$i]->ID, $size );
        }
    }
}
function get_the_post_image_id($post_id){
	$attachments = get_children(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image' ));
	if(is_array($attachments)){
	        foreach ($attachments as $attachments) {
	            $imgL = wp_get_attachment_image_src( $attachments->ID, 'large' );
	            echo '<p><a href="' . $imgL[0] . '" rel="lightbox[genba]">' . wp_get_attachment_image( $attachments->ID, 'large' ) . '</a></p>';
	        }
	}
}
the_post(); ?>

<!-- ======================コンテンツここから======================= -->
	<div id="main">
		<!-- ========================================================右コンテンツ ここから -->
		<div id="contents">

				<ul id="pankuzu" class="clearfix">
					<?php the_pankuzu_keni( ' &gt; ' ); ?>
				</ul>


<div id="ogata_page">
<h3 class="main_tit"><? the_title(); ?></h3>


<? if(post_custom('special_image')){ ?>
		<div class="top_img"><a href="<? echo gr_get_image_src('special_image'); ?>" rel="lightbox[special]" ><? echo gr_get_image( 'special_image', array( 'width' => 710 ) ); ?></a><?}?>
		</div>
				<? if(post_custom('special_name')||post_custom('special_add')||post_custom('special_koki')||post_custom('special_naiyo')){ ?>

  <table class="syosai">
						<? 	$text1 = post_custom('special_name');
							$text2 = post_custom('special_add');
							$text3 = post_custom('special_koki');
							if (post_custom('special_name')||post_custom('special_add')||post_custom('special_koki')){
						?>
						<tr>
							<th>お名前・住所</th>
							<td><? echo $text1; ?>　<? echo $text2; ?></td>
							<th>工期</th>
							<td><? echo $text3; ?></td>
						</tr>
								<? }
						if($text = post_custom('special_naiyo')){ ?>
    <tr>
      <th>リフォーム内容</th>
      <td colspan="4"><? echo $text; ?></td>
    </tr>
						<? } ?>
					</table>
				<? } ?>
<? if(post_custom('special_cust_image')||post_custom('special_cust_voice')){ ?>
  <div id="from_cust">
  <h3 class="ot_tit">お客様より</h3>
  <? if(post_custom('special_cust_image')){ ?>
		<div class="pic"> <a href="<? echo gr_get_image_src('special_cust_image'); ?>" rel="lightbox[special]" ><? echo gr_get_image( 'special_cust_image', array( 'width' => 300 ) ); ?></a></div>
		<? }
if(post_custom('special_cust_voice')){ ?>
		<div class="txt"><? echo nl2br(post_custom('special_cust_voice')); ?></div>
		<? } ?>

    <br clear="all">
    </div>
    		<? } ?>

  <br clear="all">
  <div class="block">
    <h3 class="stit">After</h3>
    <div class="picup">
      <h5 class="tit"><?php echo post_custom('special_after_tit');?></h5>
      <p class="txt"><? echo nl2br(post_custom('special_setsumei')); ?></p>
    </div>
    <div id="pic_area">
		<?
		for($i=1;$i<11;$i++){
			$j	= $i*3-2;
			$dai	= post_custom("special_h" . $i);
			$img1	= "special_point_image" . zeroise($j,2);
			$img2	= "special_point_image" . zeroise($j+1,2);
		if($dai||post_custom($img1)||post_custom($img2)||post_custom($img3)){ ?>
		<? if(post_custom($img1)||$csv1){ ?>
      <div class="box_l"> <a title="<? echo post_custom("special_point". zeroise($j,2) ); ?>" href="<? if(post_custom($img1)){echo gr_get_image_src($img1);} ?>" rel="lightbox[special]" ><? if(post_custom($img1)){echo gr_get_image( $img1, array( 'width' => 340 ) );}?></a>
        <p><? echo nl2br(post_custom("special_point". zeroise($j,2) )); ?></p>
      </div>
      			<? } ?>
      			<? if(post_custom($img2)||$csv2){ ?>

      <div class="box_r"><a href="<? if(post_custom($img2)){echo gr_get_image_src($img2);}?>" title="<? echo post_custom("special_point". zeroise($j+1,2) ); ?>" rel="lightbox[special]" ><? if(post_custom($img2)){echo gr_get_image( $img2, array( 'width' => 340 ) );}?></a>        <p><? echo nl2br(post_custom("special_point". zeroise($j+1,2) )); ?></p>
      </div>
			<? } ?>

      <br clear="all" />
		<? }} ?>




    </div>
  </div>
  <div class="block">

<? if(post_custom('special_before_madori')||post_custom('special_after_madori')){ ?>
    <h3 class="stit">Plan</h3>

    <div id="madori"><a href="<? echo gr_get_image_src('special_before_madori'); ?>" rel="lightbox[special]" ><? echo gr_get_image( 'special_before_madori', array( 'width' => 315 ) ); ?></a><img src="http://www.home-arrange.jp/wp-content/themes/reform/page_image/ogata/arrow.gif" width="74" height="209" alt="arow" /><a href="<? echo gr_get_image_src('special_after_madori'); ?>" rel="lightbox[special]" ><? echo gr_get_image( 'special_after_madori', array( 'width' => 315 ) ); ?></a></div>
  </div><?}?>
  <div class="block tanto">
    <h3 class="ot_tit">担当者より</h3>
    		<?
		$terms = get_the_terms($post->ID, 'special_cat');
		$x = 0;
		if($terms){
		foreach ( $terms as $term ) :
		$args = array(
			'post_type' => 'staff',
			'page_id' => $term->slug,
			'posts_per_page' => 1
		);
		query_posts( $args );
		while(have_posts()): the_post();
		$x++;





		echo '<div class="pic">';
		if(post_custom($img = 'staff_single_img1')){
			echo gr_get_image( $img, array( 'width' => 150 ) );
		}else if(post_custom($img = 'staff_img')){
			echo gr_get_image( $img, array( 'width' => 150 ) );

		}
			echo '<strong>' . post_custom('staff_belongs') . '<br />' . get_the_title() . '</strong>';
		echo '</div>';
		endwhile;
		wp_reset_query();

		endforeach;
		} ?>

    <div class="txt">
      <p><?php echo nl2br(post_custom('special_staff_voice')); ?></p>
     </div>
   <br clear="all" /></div>
</div>


		<!--
====================================================新大型施工事例　ここから　-->
<div id="t_ogata" class="c_page">
			<h3 class="main_tit">その他の大型リフォーム・リノベーション施工事例</h3>
						<?php $args = array(
			'post_type' => 'special', /* 投稿タイプ */
			'paged' => $paged,
			'posts_per_page' => 6 /* 件数表示 */
			); ?>
	<?php query_posts( $args ); ?>
	<?php if (have_posts()) : ?>
<? $i = 0;
$x = 0; ?>

					<?php while( have_posts() ) : the_post(); ?>


<div class="box<?php if($i%3==0){ echo " clear_left";$n++; } ?>"><a href="<?php the_permalink(); ?>" class="heightLine-group<?php echo $n; ?>"><p class="pic"><?php if(post_custom('special_t_image')){echo gr_get_image( 'special_t_image', array( 'width' => 215 ) );}
	else { echo gr_get_image( 'special_image', array( 'width' => 215 ) );}?>
	<? if(post_custom('special_magaicon')){
 	echo '<img src="/wp-content/themes/reform/page_image/special/icon_maga.png" class="maga" />';
} ?>
	<? if(post_custom('special_newicon')){
 	echo '<img src="/wp-content/themes/reform/images/top/new.png" class="new" />';
} ?>


	</p>
  <p><span class="tit"><?php the_title();?></span>
  <?php if($text=post_custom( 'special_add' )){ echo ( $text );} ?>  <?php if($text=post_custom( 'special_name' )){ echo ( $text . '<br />');} ?>
 <?php if($text=post_custom( 'special_naiyo' )){ echo ( $text );} ?></p>
  <p class="more">詳しくはこちら&gt;&gt;</p>
  </a> </div>
<?php $i++; ?>
							<?php endwhile; ?>

<br clear="all">
<?php endif; ?>
<?php wp_reset_query(); ?>

</div>
<!--====================================================新大型施工事例　ここまで　-->




				<!--customer_navi-->
				<div class="customer_navi_c clearfix" style="margin-top:20px;">
					<div class="page_back_btn01">
						<p class="page_back_text01">
							<?php previous_post_link('%link', '&lt; 前の施工事例へ'); ?>
						</p>
					</div>

					<div class="page_back_btn02">
						<p class="page_back_text02"><a href="<?php echo get_post_type_archive_link( 'special' ); ?>">大規模リフォーム・リノベーション 一覧</a></p>
					</div>

					<div class="page_back_btn02" style="margin-bottom:20px;">
							<p class="page_back_text02">
							<?php next_post_link('%link', '次の施工事例へ &gt;'); ?>
							</p>
					</div>
				</div>

		<!--customer_navi-->

</div>

<?php gr_kaiyu(); ?>
<?php gr_contact_banner(); ?>
		</div>
		<!-- ========================================================右コンテンツ ここまで -->

<?php get_sidebar(); ?>
		<br clear="all" />
	</div>
	<!-- /main -->
<?php get_footer(); ?>
