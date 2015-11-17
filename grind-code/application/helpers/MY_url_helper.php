<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
// ------------------------------------------------------------------------

/**
 * url_Helper  g_url
 *
 * generates a URL (alternative for (site_url) because of wordpress conflict
 *
 * @access	public
 * @param	string, boolean, array
 * @return	string
 */

if( !function_exists( 'g_url' ) )
{
	function g_url($uri)
	{
		$CI =& get_instance();
		
		//$CI->config->load( 'base_url' );
		$g_url = base_url().($uri);
		
		
		return $g_url;
	}
}

if ( ! function_exists('g_anchor'))
{
    function g_anchor($uri = '', $title = '', $attributes = '')
    {
        $title = (string) $title;

        if ( ! is_array($uri))
        {
            $site_url = ( ! preg_match('!^\w+://! i', $uri)) ? base_url().($uri) : $uri;
        }
        else
        {
            $site_url = base_url().$uri;
        }

        if ($title == '')
        {
            $title = $site_url;
        }

        if ($attributes != '')
        {
            $attributes = _parse_attributes($attributes);
        }

        return '<a href="'.$site_url.'">'.$title.'</a>';
    }
} 

?>