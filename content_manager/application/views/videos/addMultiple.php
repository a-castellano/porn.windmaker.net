<h2>Add a new video</h2>

<?php echo validation_errors(); ?>
<?php echo $errors; ?>
<?php echo form_open_multipart("uploadmultiple/$numVideos") ?>

<?php for ( $counter = 1; $counter <= $numVideos; $counter += 1) {?>

    <h3><?php echo $counter?> - Titles and descriptions</h3>
        <div>
        	<input type="checkbox" name="lang_1_<?php echo $counter?>" value="" checked="yes" style="display:none">English<br />
        	<label for="title_1_<?php echo $counter?>"> Title</label>
        	<input type="input" name="title_1_<?php echo $counter?>" maxlength="100" />
        	<label for="description_1_<?php echo $counter?>"> Description</label>
        	<textarea name="description_1_<?php echo $counter?>" maxlength="400"></textarea><br />
        </div>  

        Image: <?php echo $counter?><input type="file" name="image_<?php echo $counter?>" /><br>
        Video: <?php echo $counter?><input type="file" name="video_<?php echo $counter?>" /><br>

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
    <?php echo form_submit('submit', 'Upload Videos'); ?>

<?php echo form_close(); ?>