<?php
if (!defined("ACCESS")) {
    die("Error: You don't have permission to access here...");
}
?>
<div class="codes">
	<?php 
		$i = 1;
		$rand1 = rand(1, 5);
		$rand2 = rand(6, 10);

		foreach ($codes as $code) { 
			$URL = path("codes/". $code["ID_Code"] ."/". $code["Slug"], false, $code["Language"]);
	?>
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
					echo ' <span class="bold">'. __("Views") .":</span> ". (int) $code["Views"];
				?>
			</span>

			<?php
				echo display(social($URL, $code["Title"], false), 4);

				if ($code["Description"] !== "") {
					echo str_replace("\\", "", htmlTag("p", showLinks($code["Description"])));
				}
			?>

            <p>
            	<pre class="prettyprint linenums"><?php echo htmlentities(stripslashes((linesWrap($code["File"]["Code"])))); ?></pre>
            </p>

			<?php 
				if (SESSION("ZanUser")) { 
			?>
					<p class="small italic">
						<?php
                            echo like($code["ID_Code"], "codes", $code["Likes"]) . " " . dislike($code["ID_Code"], "codes", $code["Dislikes"]) . " " . report($code["ID_Code"], "codes");
                        ?>
					</p>
			<?php 
				} 

				?><br /><?php
							
				if ($i === $rand2) {
					echo display('<p>'. getAd("728px") .'</p>', 4);
				}

				$i++;
			?>			
			<br />
	<?php 
		} 
	?>

	<?php echo $pagination; ?>
</div>