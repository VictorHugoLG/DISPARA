<ul class="thumbnails">
    
<?php foreach ($images as $name => $url) : ?>
    <li class="span4">
        <div class="thumbnail">
            <img src="<?php echo $url; ?>" alt="<?php echo $name; ?>">
            <br>
            <p><?php echo $url; ?></p>
        </div>
    </li>    
<?php endforeach; ?>
    
</ul>