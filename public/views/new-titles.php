<?php
$max = 5;
$c = 0;
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
<div class="slims-biblio-list slims-new-titles">
<?php
foreach ($biblio as $b) {
	if ($c == $max) {
		break;
	}
	echo '<div class="slims-biblio-item">';
	if ($slims_config['slims_field_fetch_method'] == 'xml') {
		$b_slims = $b->children('http://slims.web.id');
		if (isset($b_slims->image)) {
			echo '<div class="slims-biblio-img"><img src="'.$slims_config['slims_base_url'].'/lib/minigalnano/createthumb.php?filename='.urlencode('images/docs/'.$b_slims->image).'&width=200" class="img-responsive" /></div>';
		}
	}
	echo '<div class="slims-title"><h6>';
	if ($slims_config['slims_open_biblio_detail'] == 'wp') : ?>
		<?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
			<a href="<?php echo get_site_url(null, '/biblio-detail/') ?>?biblio_id=<?php echo (string) $b['ID'] ?>" class="open-in-wp" target="_blank"><?php echo $b->titleInfo->title ?><?php echo isset($b->titleInfo->subTitle)?' '.$b->titleInfo->subTitle:'' ?></a>
		<?php else: ?>
			<a href="<?php echo get_site_url(null, '/biblio-detail/') ?>?biblio_id=<?php echo get_biblio_id($b['@id']) ?>" class="open-in-wp" target="_blank"><?php echo $b['name'] ?></a>
		<?php endif; ?>
	<?php else: ?>
		<?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
			<a href="<?php echo $slims_config['slims_base_url']  ?>/index.php?p=show_detail&id=<?php echo (string) $b['ID'] ?>" class="open-in-wp" target="_blank"><?php echo $b->titleInfo->title ?><?php echo isset($b->titleInfo->subTitle)?' '.$b->titleInfo->subTitle:'' ?></a>
		<?php else: ?>
			<a href="<?php echo $b['@id'] ?>" class="open-in-slims" target="_blank"><?php echo $b['name'] ?></a>
		<?php endif; ?>
	<?php 
	endif;
	echo '</h6></div>';
	echo '<div class="slims-author">';
	if ($slims_config['slims_field_fetch_method'] == 'xml') {
		if (isset($b->name) && $b->name) {
			$authors = array();
			foreach ($b->name as $author) {
				$authors[] = '<a href="'.get_site_url(null, '/biblio-opac/').'?keywords=&author='.urlencode('"'.$author->namePart.'"').'">'.$author->namePart.'</a>';
			}
			// get only the first three author
			$first_three = count($authors)>3?array_slice($authors, 0, 3):$authors;
			echo implode(' - ', $first_three);						
		}
	} else {
		if ($b['author']) {
			array_walk($b['author']['name'], function(&$i, $k) { $i = '<a href="'.get_site_url(null, '/biblio-opac/').'?keywords=&author='.urlencode('"'.$i.'"').'">'.$i.'</a>'; });
			// get only the first three author
			$first_three = count($b['author']['name'])>3?array_slice($b['author']['name'], 0, 3):$b['author']['name'];
			echo implode(' - ', $first_three);
		}
	}	
	echo '</div>';

	echo '</div>';
	$c++;
}
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