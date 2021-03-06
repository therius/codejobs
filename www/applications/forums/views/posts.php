<?php 
	if (is_array($posts)) {
		$count = count($posts) - 1;
		?>
		<div id="forum-content">
			<?php
			$forum = segment(1, islang());
			foreach ($posts as $post) {
				if ($post["ID_Parent"] === 0) {
					$URL = path("forums/". segment(1, isLang()) ."/". $post["ID_Post"] ."/". $post["Slug"]);		
					$in  = ($post["Tags"] !== "") ? __("in") : null;
					?>
					<div class="post">
						<div class="post-title">
							<a href="<?php echo $URL; ?>" title="<?php echo stripslashes($post["Title"]); ?>">
								<?php echo stripslashes($post["Title"]); ?>
							</a>
						</div>

						<div class="post-left">
							<?php echo __("Published") ." ". howLong($post["Start_Date"]) ." $in ". exploding($post["Tags"], "forums/tag/") ." " . __("by") . ' <a href="'. path("forums/author/". $post["Author"]) .'">'. $post["Author"] .'</a>'; ?>
						</div>

						<div class="clear"></div>

						<div class="post-content">
							<div class="social">
								<div class="addthis_toolbox addthis_default_style ">
									<a class="addthis_button_tweet" tw:via="codejobs" addthis:title="<?php echo stripslashes($post["Title"]); ?>" tw:url="<?php echo $URL; ?>"></a>
								</div>

								<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
								<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-5026e83358e73317"></script>
							</div>
							<?php echo showContent($post["Content"], $URL); ?>
							<br />

							<?php 
								echo display('<p>'. getAd("728px") .'</p>', 4);
							?>
						</div>
					</div>
					<?php
				} else {
					?>
					<a name="<?php echo 'id'. $post["ID_Post"]; ?>"></a>

					<div class="comments">
						<div class="comments-author">
							<img src="<?php echo $post["Avatar"] ?>" style="max-width: 70px;" class="dotted"/>
						</div>

						<div class="comments-content">
						<?php
							$authorUrl = path("forums/". $forum ."/author/". $post["Author"]);
						?>
							<p class="comment-data"><?php echo "<a href='". $authorUrl ."'>". $post["Author"] ." </a> ". __("Published") ." ". howLong($post["Start_Date"]); ?>
						
						<?php
							if (SESSION("ZanUserPrivilegeID")){
								$URLEdit   = path("forums/". $forum ."/editComment/". $post["ID_Post"]);
								$URLDelete = path("forums/". $forum ."/delete/". $post["ID_Post"]);
								$confirm   = " return confirm('Do you want to delete this post?') ";

								if (SESSION("ZanUserPrivilegeID") <= 3 or SESSION("ZanUserPrivilegeID") == $post["ID_User"]) {
									echo '| <a href="'. $URLEdit .'">'. __("Edit") .'</a> | <a href="'. $URLDelete .'" onclick="'. $confirm .'">'. __("Delete") .'</a>';
								}
							}
							?>
							</p>
							<p class="comment-post"><?php echo showContent($post["Content"]); ?></p>
						</div>
					</div>
				<?php
				}
			}
			?>
		</div>

		<div id="comment-alert"></div>
		
		<?php
			echo isset($pagination) ? $pagination : null;
			if (SESSION("ZanUser")) {			
		?>
				<div class="comments-editor">	
					<input id="needcontent" type="hidden" value="<?php echo __("You need to write the content..."); ?>" />
					<textarea id="editor" class="ckeditor" name="comment" style="height:200px"></textarea> <br />
					<input id="fid" type="hidden" value="<?php echo segment(2, isLang()); ?>" />
					<input id="fname" type="hidden" value="<?php echo $forum; ?>" />
					<input id="avatar" type="hidden" value="<?php echo $post["Avatar"]; ?>" />
					<input id="cpublish" class="btn btn-success" name="save" type="submit" value="<?php echo __("_Comment"); ?>" />
				</div>
		<?php
			} else {
		?>
				<div class="no-connected"><?php echo __('You need to <a href="'. path("users/login") .'">login</a> or <a href="'. path("users/register") .'">create</a> an account to comment this topic'); ?></div>
		<?php
			}
		}
		
		echo $ckeditor;