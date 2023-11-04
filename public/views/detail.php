<?php
$authors = array();
if ($slims_config['slims_field_fetch_method'] == 'xml') {
    $_slims_info = $biblio_detail->mods->children('http://slims.web.id');
    # echo '<pre>';
    # print_r( var_dump($biblio_detail) );
    # echo '</pre>';
    $title = $biblio_detail->mods->titleInfo->title;
    if (isset($biblio_detail->mods->titleInfo->subTitle)) {
        $title .= $biblio_detail->mods->titleInfo->subTitle;
    }
    if (isset($biblio_detail->mods->name) && $biblio_detail->mods->name) {
        foreach ($biblio_detail->mods->name as $author) {
            $authors[] = $author->namePart;
        }
    }
    $publisher = $biblio_detail->mods->originInfo->publisher;
    $publication_place = isset($biblio_detail->mods->originInfo->place->placeTerm)?$biblio_detail->mods->originInfo->place->placeTerm:'';
    $publication_date = isset($biblio_detail->mods->originInfo->dateIssued)?$biblio_detail->mods->originInfo->dateIssued:'';
    
    if ($publication_date) {
        $publisher .= ". ".$publication_date;    
    }
    if ($publication_place) {
        $publisher .= ". ".$publication_place;
    }
    
    $edition = ''; # no edition field in XML result
    $language = $biblio_detail->mods->language->languageTerm[1];
    $format = isset($biblio_detail->mods->physicalDescription->form)?$biblio_detail->mods->physicalDescription->form:'';
    $extent = isset($biblio_detail->mods->physicalDescription->extent)?$biblio_detail->mods->physicalDescription->extent:'';
    $notes = isset($biblio_detail->mods->note)?$biblio_detail->mods->note:'';
    $standard_number = isset($biblio_detail->mods->identifier)?$biblio_detail->mods->identifier:'';
    if (isset($biblio_detail->mods->subject[1])) {
        foreach ($biblio_detail->mods->subject as $s) {
            $keywords[] = $s->topic;
        }
    } else {
        $keywords = isset($biblio_detail->mods->subject)?$biblio_detail->mods->subject->topic:'';
    }
} else {
    $title = $biblio_detail['name'];
    $subTitle = $biblio_detail['alternativeHeadline'];
    if ($subTitle) {
        $title .= $subTitle;
    }
    if ($biblio_detail['author']) {
        $authors = $biblio_detail['author']['name'];
    }
    $publisher = $biblio_detail['publisher']['name'];
    $edition = $biblio_detail['version'];
    $language = $biblio_detail['inLanguage'];
    $format = $biblio_detail['bookFormat'];
    $extent = $biblio_detail['numberOfPages'];
    $notes = $biblio_detail['description'];
    $standard_number = $biblio_detail['isbn'];
    $keywords = $biblio_detail['keywords'];
} 
?>
<div class="card slims-biblio-detail">
    <h3 class="slims-biblio-detail-heading">
        <?php if ($slims_config['slims_field_fetch_method'] == 'xml') : ?>
        <div class="slims-biblio-img">
            <?php
		    if (isset($_slims_info->image)) {
		    	echo '<img src="'.$slims_config['slims_base_url'].'/lib/minigalnano/createthumb.php?filename='.urlencode('images/docs/'.$_slims_info->image).'&width=100" class="img-responsive" />';
		    } else {
		    	echo '<img src="'.plugins_url('/../image.png', __FILE__).'" class="img-responsive" />';
		    }
		    ?>
		</div>
        <?php endif; ?>
        <div class="biblio-title-top"><?php echo $title ?></div>
    </h3>
    <div class="slims-biblio-detail-row">
        <label for="biblio-title" class="biblio-detail-label"><?php echo __("Title") ?></label>
        <div class="biblio-detail-data" id="biblio-title"><?php echo $title ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-authors" class="biblio-detail-label"><?php echo __("Author") ?></label>
        <div class="biblio-detail-data" id="biblio-authors"><?php 
            if ($authors) {
                array_walk($authors, function(&$i, $k) { $i = '<div class="biblio-author"><a href="'.get_site_url(null, '/biblio-opac/').'?keywords=&author='.urlencode('"'.$i.'"').'">'.$i.'</a></div>'; });
                echo implode("\n", $authors);
            }
        ?>
        </div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-publisher" class="biblio-detail-label"><?php echo __("Publication") ?></label>
        <div class="biblio-detail-data" id="biblio-publisher"><?php echo $publisher ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-edition" class="biblio-detail-label"><?php echo __("Edition") ?></label>
        <div class="biblio-detail-data" id="biblio-edition"><?php echo $edition ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-language" class="biblio-detail-label"><?php echo __("Language") ?></label>
        <div class="biblio-detail-data" id="biblio-language"><?php echo $language ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-format" class="biblio-detail-label"><?php echo __("Format") ?></label>
        <div class="biblio-detail-data" id="biblio-format"><?php echo $format  ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-physical-desc" class="biblio-detail-label"><?php echo __("Physical Information") ?></label>
        <div class="biblio-detail-data" id="biblio-physical-desc"><?php echo $extent ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-notes" class="biblio-detail-label"><?php echo __("Summary/Description") ?></label>
        <div class="biblio-detail-data" id="biblio-physical-notes"><?php echo $notes ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-standard-number" class="biblio-detail-label"><?php echo __("Standard Number (ISBN/ISSN)") ?></label>
        <div class="biblio-detail-data" id="biblio-standard-number"><?php echo $standard_number ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-keywords" class="biblio-detail-label"><?php echo __("Keyword(s)") ?></label>
        <div class="biblio-detail-data" id="biblio-keywords"><?php 
            if (is_array($keywords)) {
                foreach ($keywords as $k) {
                    echo '<div class="biblio-topic"><a href="'.get_site_url(null, '/biblio-opac/').'?keywords=&topic='.urlencode('"'.$k.'"').'">'.$k.'</a></div>';
                }
            } else {
                echo '<div class="biblio-topic"><a href="'.get_site_url(null, '/biblio-opac/').'?keywords=&topic='.urlencode('"'.$keywords.'"').'">'.$keywords.'</a></div>';
            } 
        ?></div>
    </div>
    <?php
    if (isset($biblio_detail->mods->location)) {
        echo '<div class="slims-biblio-detail-row">';
        echo '<label for="biblio-location" class="biblio-detail-label">'.__("Physical Location").'</label>';
        echo '<div class="biblio-detail-data" id="biblio-keywords">';
        foreach ($biblio_detail->mods->location as $l) {
            echo '<div class="biblio-location">'.$l->physicalLocation.' (Call Number: '.$l->shelfLocator.')</div>';
        }
        echo '</div></div>';
    }
    ?>
</div>