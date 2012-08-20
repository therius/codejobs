<?php 
				
		$URL = path("blog/". $post["Year"] ."/". $post["Month"] ."/". $post["Day"] ."/". $post["Slug"]);		
		$in  = ($post["Tags"] !== "") ? __("in") : NULL;
?>
		<div class="post">
			<div class="post-title">
				<a href="<?php echo $URL; ?>" title="<?php echo $post["Title"]; ?>">
					<?php echo $post["Title"]; ?>
				</a>
			</div>
			
		
		<div class="post-left">
				<?php echo __(_("Published")) ." ". howLong($post["Start_Date"]) ." $in ". exploding($post["Tags"], "blog/tag/") ." " . __(_("by")) . ' <a href="'. path("users/". $post["Author"]) .'">'. $post["Author"] .'</a>'; ?>
			</div>
			
			<div class="post-right">
				<?php
					if($post["Enable_Comments"]) {
						?><fb:comments-count href="<?php echo $URL; ?>"></fb:comments-count> <?php echo __("comments");
					}
				?>
			</div>
			
			<div class="clear"></div>
				
			<div class="post-content">
				<div class="addthis_toolbox addthis_default_style ">
					<a class="addthis_button_facebook_like" fb:like:layout="button_count" addthis:url="<?php echo $URL; ?>"></a>
					<a class="addthis_button_tweet" addthis:title="<?php echo $post["Title"]; ?>"></a>
				</div>

				<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-5026e83358e73317"></script>

				<?php echo showContent(bbCode($post["Content"], $URL)); ?>
				<br />
				<script type="text/javascript"><!--
					google_ad_client = "ca-pub-4006994369722584";
					/* CodeJobs.biz */
					google_ad_slot = "1672839256";
					google_ad_width = 728;
					google_ad_height = 90;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
			</div>

			<div class="post-social">		
				
			</div>
		</div>
		<br /></br />
		<?php
			if($post["Enable_Comments"]) {
				?><div class="fb-comments" data-href="<?php echo $URL; ?>" data-num-posts="2" data-width="750"></div><?php
			}
		?>
