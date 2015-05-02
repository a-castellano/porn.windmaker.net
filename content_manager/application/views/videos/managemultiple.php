<a href="/index.php/managemultiple"><h1>MANAGE!</h1></a><br>
<a href="/index.php">Return</a><br>
<?php echo form_open('updatemultiple') ?>
<input type="input" name="video_list" value="<?php echo $video_list?>" style="display:none" /><br>
<?php foreach ($videos as $video_item){ ?>

<h2><?php echo $video_item['title']?></h2><br>
<input type="checkbox" name="video_<?php echo $video_item['id']?>" ><br>
<img src="<?php echo $video_item['image_url']?>" style="max-height: 70px; width: auto;"><br>
<p><?php echo $video_item['description']?></p>
<p><b>Websites</b>: <?php

	$websites_array = explode("-", $video_item['websites']);
	array_pop($websites_array); //last item is always ""
	foreach ($websites_array as $website) {
		echo array_search($website, $this->variables->sites) . ', ';
	}

 ?></p>
 <p><b>Categories</b>: <?php

	$categories_array = explode("-", $video_item['categories']);
	array_pop($categories_array); //last item is always ""
	foreach ($categories_array as $category) {
		echo array_search($category, $this->variables->categories) . ', ';
	}

 ?></p>
  <p><b>Languages</b>: <?php

	$languages_array = explode("-", $video_item['languages']);
	array_pop($languages_array); //last item is always ""
	foreach ($languages_array as $language) {
		echo array_search($language, $this->variables->langs) . ', ';
	}

 ?></p>
 <hr>
<?php } ?>
<hr>
<p>Pages</p>

<?php for ( $counter = 1; $counter <= $number_indexes; $counter += 1) { ?>
        <a href="/index.php/managemultiple/<?php echo $websites ?>/<?php echo $categories ?>/<?php echo $languages ?>/<?php echo (($counter - 1) * $offset)  ?>/<?php echo $offset ?>"><?php echo $counter ?></a>
<?php }?>

<hr>
<p>Edit videos</p>
	

	<br>
    <b>Sites</b>
    <?php //sites
        foreach ($this->variables->sites as $site => $value) {?>
            <input type="checkbox" name="site_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$websites,$value)!=FALSE) { echo 'checked="yes"'; } ?> ><?php echo $site; ?>
    <?php } ?>
    <br>
    <b>Categories</b>
    <?php 
        //Categories
        foreach ($this->variables->categories as $category => $value) {?>
            <input type="checkbox" name="category_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$categories,$value)!=FALSE) { echo 'checked="yes"'; } ?>><?php echo $category; ?>
    <?php } ?>
    <br>

    <?php echo form_submit('submit', 'Edit'); ?>


<?php echo form_close(); ?>
<hr>
<hr>
<p>Searcher</p>
	
<?php echo form_open('searchmultiple') ?>
	<b>Languages</b>
	<?php
    //Languages
	foreach ($this->variables->langs as $lang => $value) {?>
		<input type="checkbox" name="lang_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if($lang == "English") { echo 'checked="yes" style="display:none"'; }  if(stripos($languages,$value)) { echo 'checked="yes"'; } ?> ><?php if($lang != "English") {echo $lang;} ?> 
	<?php }?>
	<br>
    <b>Sites</b>
    <?php //sites
        foreach ($this->variables->sites as $site => $value) {?>
            <input type="checkbox" name="site_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$websites,$value)!=FALSE) { echo 'checked="yes"'; } ?> ><?php echo $site; ?>
    <?php } ?>
    <br>
    <b>Categories</b>
    <?php 
        //Categories
        foreach ($this->variables->categories as $category => $value) {?>
            <input type="checkbox" name="category_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$categories,$value)!=FALSE) { echo 'checked="yes"'; } ?>><?php echo $category; ?>
    <?php } ?>
    <br>
    News number:
	<select name="nomberNews">
	  <option value="50">50</option>
	  <option value="100">100</option>
	  <option value="200">200</option>
	  <option value="300">300</option>
	  <option value="400">400</option>
	  <option value="500">500</option>
	  <option value="700">700</option>
	  <option value="1000">1000</option>
	</select><br>
    <?php echo form_submit('submit', 'Search'); ?>


<?php echo form_close(); ?>
<hr>
 <a href="/index.php/managemultiple/orphans/none/none">Orphan Videos</a><br>
<hr>


<a href="/index.php">Return</a><br>

<br><br><br><br><br><br><br><br>
