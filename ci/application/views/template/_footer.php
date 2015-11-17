</div> <!-- end section -->
  		
  	<footer>
    <p>&copy;GRIND 2011</p>
  </footer>
</div><!-- end #container -->

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

	$(document).ready(function() {
		$("abbr.timeago").timeago();
	});
</script>

</body>
</html>
<?php echo $this->benchmark->elapsed_time();?>