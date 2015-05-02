<h2>Add a new video</h2>

<?php echo validation_errors(); ?>
<?php echo $errors; ?>
<?php echo form_open_multipart('upload') ?>

    <h3>Titles and descriptions</h3>
	<?php
    //Languages
	foreach ($this->variables->langs as $lang => $value) {
	?>
        <div>
        	<input type="checkbox" name="lang_<?php echo $value; ?>" value="<?php echo $value; ?>" <?php if($lang == "English") { echo 'checked="yes" style="display:none"'; } ?> ><?php echo $lang; ?> <br />
        	<label for="title_<?php echo $value; ?>"> Title</label>
        	<input type="input" name="title_<?php echo $value; ?>" maxlength="100" />
        	<label for="description_<?php echo $value; ?>"> Description</label>
        	<textarea name="description_<?php echo $value; ?>" maxlength="400"></textarea><br />
        </div>  
	<?php } ?>

    <p>Sites</p>
    <ul>
    <?php //sites
        foreach ($this->variables->sites as $site => $value) {?>
            <li><input type="checkbox" name="site_<?php echo $value; ?>" value="<?php echo $value; ?>"><?php echo $site; ?></li>
    <?php } ?>
    </ul>

    <p>Categories</p>
    <ul>
    <?php 
        //Categories
        foreach ($this->variables->categories as $category => $value) {?>
            <li><input type="checkbox" name="category_<?php echo $value; ?>" value="<?php echo $value; ?>"><?php echo $category; ?></li>
    <?php } ?>
    </ul>
    <hr>
    Image: <input type="file" name="image" /><br>
    Video: <input type="file" name="video" /><br>
    <?php echo form_submit('submit', 'Upload Video'); ?>

<?php echo form_close(); ?>