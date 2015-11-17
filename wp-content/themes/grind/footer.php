    </div><!--end #main -->
  
    <footer>
      <nav>
        <ul class="clearfix">
          <li>Navigation</li>
          <li>Join us</li>
          <li>Find us</li>
          <li class="blog">Blog</li>
        </ul>
      </nav>
      <a href="#" class="toggle"></a>
      <div id="footer-drawer" class="clearfix">
        <section id="login" class="col">
          <ul>
  		      <a href="<?php echo wp_logout_url(); ?>" title="Logout">Logout</a><br />
  		      <?php wp_nav_menu( array('menu' => 'footer-nav' )); ?>
          </ul>
        </section>
        <section id="join" class="col">
          <ul>
            <li class="twitter"><a href="http://twitter.com/#!/grindspaces" target="_blank"><span>Listen: </span>Follow us on Twitter</a></li>
            <!--<li class="facebook"><a href="#" target="_blank"><span>Join: </span>Find us on Facebook</a></li>
            <li class="email-list"><a href="#" target="_blank"><span>Subscribe: </span>Email list</a></li>-->
          </ul>
        </section>
        <section id="find" class="col">
          <p>
            <a href="https://maps.google.com/maps?q=419+park+ave+south+new+york+10016&ie=UTF8&hq=&hnear=419+Park+Ave+S,+New+York,+10016&ll=40.743876,-73.984112&spn=0.005471,0.012274&z=17&iwloc=A" target="_blank" title="See on Google Map"><img src="<?= get_bloginfo('stylesheet_directory'); ?>/img/map.png" width="179" height="138" alt=""></a>
          </p>
          <p>
            Grind<br>
            419 Park Ave South<br>
            Second floor<br>
            New York, NY 10016<br>
            T: +1 646 558 3250<br>
            <a href="mailto:workliquid@grindspaces.com">workliquid@grindspaces.com</a>
            Hours: M - F 8 am until midnight
          </p>
        </section>
        <section id="blog" class="col">
		  
		  <?php 
		  
          $xml = simplexml_load_file('http://workliquid.grindspaces.com/blog/rss.xml');
          $i = 0;
          ?>
          
          <?php foreach($xml->channel->item as $item): ?>
            <?php if($i < 3): ?>
                <?php
                    $title_array = explode('(', $item->author);
                    $title = substr($title_array[1], 0, -1);
                ?>
                <p>
                    <span class="post"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></span>
                    <span class="date-author"><?php echo date('m/d/Y', strtotime($item->pubDate)); ?>. By <?php echo $title; ?></span>
                </p>
            <?php endif; ?>
            <?php $i++; ?>
          <?php endforeach; ?>
            
          <a href="http://workliquid.grindspaces.com/blog" class="btn">Visit Blog</a>
          
		
        </section>

        <ul id="partners" class="clearfix">
          <li>A collaboration by</li>
          <li><a class="ir p1" href="http://www.cocollective.com" target="_blank">CO:</a></li>
          <li><a class="ir p2" href="http://www.behance.net" target="_blank">Behance</a></li>
          <li><a class="ir p3" href="http://www.coolhunting.com" target="_blank">Cool Hunting</a></li>
          <li><a class="ir p4" href="http://breakfastny.com" target="_blank">Breakfast</a></li>
          <li><a class="ir p5" href="http://magicandmight.com" target="_blank">Magic+Might</a></li>
        </ul>
      </div>
    </footer>
    
    
    
    <p id="foot"><?php if (function_exists('bte_bc_tag')) { bte_bc_tag(); } ?></p>
    
  </div><!-- end #container -->

  	
  
  <?php wp_footer(); ?>
  
  <script>
  
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-23061703-1']);
    _gaq.push(['_setDomainName', 'grindspaces.com']);
    _gaq.push(['_setAllowHash', false]);
    _gaq.push(['_trackPageview']);
  
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  
  </script>
  <script src="<?= get_bloginfo('stylesheet_directory'); ?>/js/plugins.js"></script>
  <script src="<?= get_bloginfo('stylesheet_directory'); ?>/js/script.js"></script>

</body>
</html>