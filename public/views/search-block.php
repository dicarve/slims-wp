<div class="slims-search-box">
	<form method="get" action="<?php echo $biblio_opac_permalink; ?>">
		<?php if ($is_plain_permalink) : ?>
			<input type="hidden" name="page_id" value="<?php echo $biblio_opac_page_id ?>" />
		<?php endif; ?>
		<div class="slims-search-input">
			<input type="text" name="keywords" id="slims-search" value="<?php echo isset($_GET['keywords'])?trim($_GET['keywords']):'' ?>" placeholder="Type one or more keywords to search" />
			<button type="submit" id="submit">Search</button>
			<button type="button" class="show-advance-search">Advance Search</button>
		</div>
		<fieldset class="advance-search">
			<legend>Advanced Search</legend>
			<div class="advance-search-fields">
				<div>
					<h5>Title</h5>
					<input type="text" name="title" id="slims-search-title" value='<?php echo isset($_GET['title'])?trim(stripslashes($_GET['title'])):'' ?>' />
				</div>
				<div>
					<h5>Author</h5>
					<input type="text" name="author" id="slims-search-author" value='<?php echo isset($_GET['author'])?trim(stripslashes($_GET['author'])):'' ?>' />
				</div>
				<div>
					<h5>Subject/Topic</h5>
					<input type="text" name="topic" id="slims-search-topic" value='<?php echo isset($_GET['topic'])?trim(stripslashes($_GET['topic'])):'' ?>' />
				</div>
				<div>
					<h5>&nbsp;</h5>
					<button type="submit" id="submit-advance">Search</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>