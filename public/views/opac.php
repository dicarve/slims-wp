<?php
$biblio = $biblio_result['@graph'];
?>
<div class="card slims-biblio-notice">
	<div class="notice">
		<?php if ($keywords) : ?>
			Found <?php echo $biblio_result['total_rows'] ?> from keywords: <em><?php echo $keywords ?></em>.
		<?php elseif($adv_search) : ?>
			Found <?php echo $biblio_result['total_rows'] ?> from your specific search with your keywords: <em><?php 
				array_walk($adv_search, function(&$i, $k) { $i = stripslashes($i); });
				echo implode(' AND ', $adv_search); ?></em>.
			<script>jQuery('.advance-search').show()</script>
		<?php else : ?>
			Found <?php echo $biblio_result['total_rows'] ?> from collection database.
		<?php endif; ?>
	</div>
</div>
<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
<div class="slims-biblio-list slims-opac">
<?php
echo paging( get_site_url(null, '/biblio-opac/'), $biblio_result['total_rows'], $biblio_result['records_each_page'], 10, SLIMS_PAGING_PAGE_VARNAME );
foreach ($biblio as $b) : ?>
	<div class="slims-biblio-item">
		<div class="slims-biblio-img">
			<?php // echo wp_get_attachment_image( 99, 'thumbnail', '', array( "class" => "img-responsive" ) );  ?>
			<img src="<?php echo plugins_url('/../image.png', __FILE__) ?>" class="img-responsive" />
		</div>
		<div class="slims-item-detail">
			<div class="slims-title"><h5><a href="<?php echo site_url() ?>/biblio-detail/?biblio_id=<?php echo get_biblio_id($b['@id']) ?>" target="_blank"><?php echo $b['name'] ?></a></h5></div>
			<div class="slims-author"><?php 
				if ($b['author']) {
					array_walk($b['author']['name'], function(&$i, $k) { $i = '<a href="'.site_url().'/biblio-opac/?keywords=&author='.urlencode('"'.$i.'"').'">'.$i.'</a>'; });
					echo implode(' - ', $b['author']['name']);
				}
				?>
			</div>
		</div>
	</div>
<?php
endforeach;
echo paging( get_site_url(null, '/biblio-opac/'), $biblio_result['total_rows'], $biblio_result['records_each_page'], 10, SLIMS_PAGING_PAGE_VARNAME );
echo '</div>';