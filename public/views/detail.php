<div class="card slims-biblio-detail">
    <h3><?php echo $biblio_detail['name'] ?></h3>
    <div class="slims-biblio-detail-row">
        <label for="biblio-title" class="biblio-detail-label"><?php echo __("Title") ?></label>
        <div class="biblio-detail-data" id="biblio-title"><?php echo $biblio_detail['name'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-authors" class="biblio-detail-label"><?php echo __("Author") ?></label>
        <div class="biblio-detail-data" id="biblio-authors"><?php 
            if ($biblio_detail['author']) {
                array_walk($biblio_detail['author']['name'], function(&$i, $k) { $i = '<div class="biblio-author"><a href="'.site_url().'/biblio-opac/?keywords=&author='.urlencode('"'.$i.'"').'">'.$i.'</a></div>'; });
                echo implode("\n", $biblio_detail['author']['name']);
            }
        ?>
        </div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-publisher" class="biblio-detail-label"><?php echo __("Publisher") ?></label>
        <div class="biblio-detail-data" id="biblio-publisher"><?php echo $biblio_detail['publisher']['name'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-edition" class="biblio-detail-label"><?php echo __("Edition") ?></label>
        <div class="biblio-detail-data" id="biblio-edition"><?php echo $biblio_detail['version'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-language" class="biblio-detail-label"><?php echo __("Language") ?></label>
        <div class="biblio-detail-data" id="biblio-language"><?php echo $biblio_detail['inLanguage'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-format" class="biblio-detail-label"><?php echo __("Format") ?></label>
        <div class="biblio-detail-data" id="biblio-format"><?php echo $biblio_detail['bookFormat'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-physical-desc" class="biblio-detail-label"><?php echo __("Physical Information") ?></label>
        <div class="biblio-detail-data" id="biblio-physical-desc"><?php echo $biblio_detail['numberOfPages'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-notes" class="biblio-detail-label"><?php echo __("Summary/Description") ?></label>
        <div class="biblio-detail-data" id="biblio-physical-notes"><?php echo $biblio_detail['description'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-standard-number" class="biblio-detail-label"><?php echo __("Standard Number (ISBN/ISSN)") ?></label>
        <div class="biblio-detail-data" id="biblio-standard-number"><?php echo $biblio_detail['isbn'] ?></div>
    </div>
    <div class="slims-biblio-detail-row">
        <label for="biblio-keywords" class="biblio-detail-label"><?php echo __("Keyword(s)") ?></label>
        <div class="biblio-detail-data" id="biblio-keywords"><?php echo $biblio_detail['keywords'] ?></div>
    </div>
</div>