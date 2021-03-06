<?php
    if (!defined("ACCESS")) {
        die("Error: You don't have permission to access here...");
    }
    
    $URL = path("codes/". $code["ID_Code"] ."/". $code["Slug"], false, $code["Language"]);
?>
<div class="codes">
	<h2>
		<?php echo getLanguage($code["Language"], true); ?> <a href="<?php echo $URL; ?>" title="<?php echo quotes($code["Title"]); ?>"><?php echo quotes($code["Title"]); ?></a>
	</h2>

	<span class="small italic grey">
		<?php 
			echo __("Published") ." ". howLong($code["Start_Date"]) ." ". __("by") .' <a title="'. $code["Author"] .'" href="'. path("codes/author/". $code["Author"]) .'">'. $code["Author"] .'</a> '; 
			 
			if ($code["Languages"] !== "") {
				echo __("in") ." ". exploding(implode(", ", array_map("strtolower", explode(", ", $code["Languages"]))), "codes/language/");
			}
		?>			
		<br />

		<?php 
			echo '<span class="bold">'. __("Likes") .":</span> ". (int) $code["Likes"]; 
			echo ' <span class="bold">'. __("Dislikes") .":</span> ". (int) $code["Dislikes"];
			echo ' <span class="bold">'. __("Views") .":</span> ". (int) $Views;
		?>
	</span>

    <?php 
        echo display(social($URL, $code["Title"], false), 4); 

        if ($code["Description"] !== "") {
            echo str_replace("\\", "", htmlTag("p", showLinks($code["Description"])));
        }

        foreach ($code["Files"] as $file) {
        ?>
            <p>
                <div class="title-file">
                    <?php
                        echo htmlTag("div", array("class" => "filename"), $file["Name"]);
                        
                        echo htmlTag("a", array(
                            "name"  => slug($file["Name"]),
                            "class" => "permalink",
                            "title" => __("Permalink to this file"),
                            "href"  => "#" . slug($file["Name"])
                        ), "&para;&nbsp;");
                    ?>
                </div>

                <pre class="prettyprint linenums"><?php echo htmlentities(stripslashes($file["Code"])); ?></pre>
            </p>
        <?php
        }
		
        if (SESSION("ZanUser")) {
	    ?>
			<p class="small italic">
				<?php  echo like($code["ID_Code"], "codes", $code["Likes"]) ." ". dislike($code["ID_Code"], "codes", $code["Dislikes"]) ." ". report($code["ID_Code"], "codes"); ?>
			</p>
        <?php
        }
        ?>
    
        <p>
            <a <?php
                if (SESSION("ZanUser")) {
                    echo 'href="'. path("codes/download/". $code['ID_Code'] ."/". $code['Slug']) .'" target="_blank"';
                } else {
                    echo 'href="'. path("users/login") .'/?type=1&return_to='. urlencode(path("codes/". $code['ID_Code'] ."/". $code['Slug'] ."/download")) .'"';
                }
            ?>class="btn download"><?php echo __("Download code"); ?></a>
        </p>
    
        <br />
    
        <?php
            echo display('<p>'. getAd("728px") .'</p>', 4);
        ?>   

    <p>
        <a name="comments">
            <?php echo fbComments($URL); ?>   
        </a>
    </p>
	
	<p>
		<a href="<?php echo path("codes"); ?>">&lt;&lt; <?php echo __("Go back"); ?></a>
	</p>
</div>