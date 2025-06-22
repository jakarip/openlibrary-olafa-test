<!doctype html>
<?php


?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
              integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
              crossorigin="anonymous">
        <title></title>
		<style>
    .cloud-div {
        width: 100%;
        margin: auto; /* Centers the div horizontally */
        text-align: center; /* Centers the text inside the div */
    }
    .cloud-tag {
        padding: 0;
        padding-right: 5px;
        vertical-align: center;
        white-space: nowrap;
    }
</style>
    </head>
    <body>
        <div class="container-fluid">  
<?php
$starting_font_size = 12;
$factor = 0.8;
 
shuffle($word);
echo '<div class="cloud-div">';
foreach ($word as $t) {
	if($t->wc_word!=""){
		$x = $t->wc_weight/10 * $factor;
		$font_size = $starting_font_size + $x.'px';
        // Generate a random color
        $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
		echo '
			<span class="cloud-tag" style="font-size: '.$font_size. '; color: ' . $color . ';">'
			.$t->wc_word.'
			</span>';
	}
}
echo '</div>';
 ?>
        </div>
        <script
            src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
            integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
            crossorigin="anonymous"></script>
        <script
            src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
            integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
            crossorigin="anonymous"></script>
    </body>
</html>