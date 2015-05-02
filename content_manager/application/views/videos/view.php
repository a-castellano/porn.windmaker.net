<h1>Edit Video</h1>
<a href="/index.php/manage"><b>Manage menu</b></a><br>
<h2><?php echo $video['title']?></h2><br>
<img src="<?php echo $video['image_url']?>" style="max-height: 400px; width: auto;"><br>
<video src="<?php echo $video['video_url']?>" style="max-height: 400px; width: auto;" controls></video>

<?php echo validation_errors(); ?>
<?php echo $errors; ?>
<?php echo form_open('update') ?>
<input type="input" name="id" style="display:none" value="<?php echo $video['id']?>"/>
    <h3>Titles and descriptions</h3>
	<?php
    //Languages
	foreach ($this->variables->langs as $lang => $value) {
	?>
        <div>
        	<input type="checkbox" name="lang_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if($lang == "English") { echo 'checked="yes" style="display:none"'; } if(array_key_exists($value,$titles)) { echo 'checked="yes"'; } ?> ><?php echo $lang; ?> <br />
        	<label for="title_<?php echo $value; ?>"> Title</label>
        	<input type="input" name="title_<?php echo $value; ?>" maxlength="100" value="<?php if($lang == "English"){ echo $video['title']; } else{ if(array_key_exists($value,$titles)) {echo $titles[$value]['title'];}} ?>"/>
        	<label for="description_<?php echo $value; ?>"> Description</label>
        	<textarea name="description_<?php echo $value; ?>" maxlength="400" ><?php if($lang == "English"){ echo $video['description']; } else{ if(array_key_exists($value,$titles)) {echo $titles[$value]['description'];}}?></textarea><br />
        </div>  
	<?php } ?>

    <p><b>Websites</b></p>
    <?php //sites
        foreach ($this->variables->sites as $site => $value) {?>
            <input type="checkbox" name="site_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$websites,$value)!=FALSE) { echo 'checked="yes"'; } ?> ><?php echo $site; ?>
    <?php } ?>


    <p><b>Categories</b></p>
    <?php 
        //Categories
        foreach ($this->variables->categories as $category => $value) {?>
            <input type="checkbox" name="category_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if(stripos('-'.$categories,$value)!=FALSE) { echo 'checked="yes"'; } ?> ><?php echo $category; ?>
    <?php } ?>
	<br>
    <?php echo form_submit('submit', 'Update video info'); ?>

<?php echo form_close(); ?>
<hr><hr>
<a href="/index.php/delete/<?php echo $video['id']?>">|||DELETE VIDEO|||</a><br>
<hr><hr>
<a href="/index.php/manage"><b>Manage menu</b></a><br>