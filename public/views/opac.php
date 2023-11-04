<?php
if ($slims_config['slims_field_fetch_method'] == 'xml') {
	$biblio = $biblio_result;
    // get result info from SLiMS namespace
    $slims_result_info = $biblio_result->children('http://slims.web.id');
	$biblio_result['total_rows'] = $slims_result_info->resultInfo->modsResultNum;
	$biblio_result['records_each_page'] = $slims_result_info->resultInfo->modsResultShowed;
} else {
	$biblio = $biblio_result['@graph'];
}
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
<div class="slims-biblio-list slims-opac">
<?php
$paging_base_permalink = $is_plain_permalink?preg_replace('/\?page_id=[1-9]+/i', '', $biblio_opac_permalink):$biblio_opac_permalink;
$paging = paging( $paging_base_permalink, $biblio_result['total_rows'], $biblio_result['records_each_page'], 10, SLIMS_PAGING_PAGE_VARNAME );
echo $paging;
foreach ($biblio as $b) : ?>
	<div class="slims-biblio-item">
		<div class="slims-biblio-img">
			<?php // echo wp_get_attachment_image( 99, 'thumbnail', '', array( "class" => "img-responsive" ) );  ?>
			<?php if ($slims_config['slims_field_fetch_method'] == 'json') : ?>
				<img src="<?php echo plugins_url('/../image.png', __FILE__) ?>" class="img-responsive" />
			<?php else: ?>
				<?php 
				$b_slims = $b->children('http://slims.web.id');
				if (isset($b_slims->image)) {
					echo '<img src="'.$slims_config['slims_base_url'].'/lib/minigalnano/createthumb.php?filename='.urlencode('images/docs/'.$b_slims->image).'&width=100" class="img-responsive" />';
				} else {
					echo '<img src="'.plugins_url('/../image.png', __FILE__).'" class="img-responsive" />';
				}
				?>
			<?php endif; ?>
		</div>
		<div class="slims-item-detail">
			<div class="slims-title"><h5>
				<?php if ($slims_config['slims_open_biblio_detail'] == 'wp') : ?>
					<?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
						<a href="<?php echo $biblio_detail_permalink.( $is_plain_permalink?'&':'?' ); ?>biblio_id=<?php echo (string) $b['ID'] ?>" class="open-in-wp" target="_blank"><?php echo $b->titleInfo->title ?><?php echo isset($b->titleInfo->subTitle)?' '.$b->titleInfo->subTitle:'' ?></a>
					<?php else: ?>
						<a href="<?php echo $biblio_detail_permalink.( $is_plain_permalink?'&':'?' ); ?>biblio_id=<?php echo get_biblio_id($b['@id']) ?>" class="open-in-wp" target="_blank"><?php echo $b['name'] ?></a>
					<?php endif; ?>
				<?php else: ?>
					<?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
						<a href="<?php echo $slims_config['slims_base_url']  ?>/index.php?p=show_detail&id=<?php echo (string) $b['ID'] ?>" class="open-in-wp" target="_blank"><?php echo $b->titleInfo->title ?><?php echo isset($b->titleInfo->subTitle)?' '.$b->titleInfo->subTitle:'' ?></a>
					<?php else: ?>
						<a href="<?php echo $b['@id'] ?>" class="open-in-slims" target="_blank"><?php echo $b['name'] ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</h5></div>
			<div class="slims-author"><?php
				if ($slims_config['slims_field_fetch_method'] == 'xml') {
					if (isset($b->name) && $b->name) {
						$authors = array();
						foreach ($b->name as $author) {
							$authors[] = '<a href="'.$biblio_opac_permalink.( $is_plain_permalink?'&':'?' ).'keywords=&author='.urlencode('"'.$author->namePart.'"').'">'.$author->namePart.'</a>';
						}
						echo implode(' - ', $authors);						
					}
				} else {
					if ($b['author']) {
						array_walk($b['author']['name'], function(&$i, $k) { $i = '<a href="'.$biblio_opac_permalink.( $is_plain_permalink?'&':'?' ).'keywords=&author='.urlencode('"'.$i.'"').'">'.$i.'</a>'; });
						echo implode(' - ', $b['author']['name']);
					}
				}
				?>
			</div>
			<?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
				<?php if ($b_slims->digitals) : ?>
					<div class="slims-files">
					<div class="slims-files-heading"><?php echo __('Downloads') ?>: </div>	
					<?php foreach ($b_slims->digitals as $file) : 
						$attr = $file->digital_item->attributes(); ?>
						<div class="slims-file"><a href="<?php echo $slims_config['slims_base_url'] ?>/index.php?p=fstream&fid=<?php echo $attr['id'] ?>&bid=<?php echo (string) $b['ID'] ?>"><?php echo $file->digital_item ?></a></div>
					<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
<?php
endforeach;
echo $paging;
?>
</div>

<!-- The Modal -->
<div id="slims-modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>Detail</h2>
    </div>
    <div class="modal-body"></div>
  </div>
</div>