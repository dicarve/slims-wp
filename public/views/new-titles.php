<?php
$max = 5;
$c = 0;
?>
<div class="slims-biblio-list slims-new-titles">
<?php
foreach ($biblio as $b) {
	if ($c == $max) {
		break;
	}
	echo '<div class="slims-biblio-item">';
	echo '<div class="slims-title"><h5><a href="'.site_url().'/biblio-detail/?biblio_id='.get_biblio_id($b['@id']).'" target="_blank">'.$b['name']."</a></h5></div>";
	echo '<div class="slims-author">'.!empty($b['author'])?_ellipse(implode(' - ', $b['author']['name']), 80):''.'</div>';
	echo '</div>';
	$c++;
}
?>
</div>