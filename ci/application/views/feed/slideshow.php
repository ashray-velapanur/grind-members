<div id="inspiration">
			<div class="slideshow" id="homeSlideshow">
				<div id="slideshowArticleDetails">
					<div id="slideshowArticleTitle"><a href="" target="_blank"></a></div>
					<div id="slideshowArticleSource">a little inspiration from <a href="" title="" target="_blank"></a></div>
					<div id="slideshowArticleCredits">
						<span id="slideshowArticleAuthor"></span> <span id="slideshowArticleDate"></span>
					</div>
				</div>
				
				<div class="slides">
					<ul class="clearfix">
						
						<?php 
						$i=0;
						foreach ($feed->get_items(0,5) as $item): 
						
						?>
						
							<li class="<?php echo $i == 0 ? 'current' : ''; ?>">
								<?php
									$content = $item->get_description();
									$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
									$pattern = '#<img[^>]*>#i';
									preg_match($pattern, $content, $matches);
									if ($matches) {
										echo $matches[0];
									}
								?>
							
								<input type="hidden" class="articleAuthor" value="<?= $item->get_author()->get_email();?>"/>
								<input type="hidden" class="articleTitle" value="<?= $item->get_title();?>"/>
								<input type="hidden" class="articleLink" value="<?= $item->get_permalink();?>"/>
								<input type="hidden" class="creditDate" value="<?= $item->get_date();?>"/>
								<input type="hidden" class="creditUrl" value="<?= $feed->get_permalink(); ?>"/>
								<input type="hidden" class="creditName" value="<?= $feed->get_title(); ?>"/>
							</li>
						
						<?php
						$i=$i+1;
						endforeach;?>
					</ul>
					<div class="prev-slide arrow"></div>
					<div class="next-slide arrow"></div>
				</div>
		  </div>