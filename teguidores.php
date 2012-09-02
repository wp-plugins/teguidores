<?php
/*
Plugin Name: Teguidores
Plugin URI: http://wordpring.com
Description: Muestra de forma visual tus seguidores de Twitter, Facebook y RSS
Author: Miguel
Version: 1.1
Author URI: http://twitter.com/Rdemiguel_

*/

/*  Copyright 2012 Miguel Recober  (email : redemiguel@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Obtener Seguidores Facebook //

function obtener_fans_de_facebook($fb_id){
       $count = get_transient('fan_count');
   
         $count = 0;
         $data = wp_remote_get('http://api.facebook.com/restserver.php?method=facebook.fql.query&query=SELECT%20fan_count%20FROM%20page%20WHERE%20page_id='.$fb_id.'');
   if (is_wp_error($data)) {
         return 'Error obteniendo los fans';
   }else{
         $count = strip_tags($data[body]);
   }
set_transient('fan_count', $count, 60*60*24); // cache cada 24 horas
echo $count;
}

// Lectores RSS //

function diww_fb_count ($fb_user) {

	$fburl="https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=". $fb_user;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $fburl);
	$stored = curl_exec($ch);
	curl_close($ch);
	$grid = new SimpleXMLElement($stored);
	$rsscount = $grid->feed->entry['circulation']+0;
	return number_format($rsscount);

}

function diww_fb_count_run($feed) {

	$fb_subs = diww_fb_count ($feed);
	$fb_option = "diww_fb_sub_value";
	$fb_subscount = get_option($fb_option);
	if (is_null($fb_subs)) { return $fb_subscount; }
	else {update_option($fb_option, $fb_subs); return $fb_subs;}

}

function diww_fb_sub_value($feed) {

	echo diww_fb_count_run($feed);

}
// Obtener Twitter
function obtener_seguidores_de_twitter ($usuario_twitter) {

	$url="http://twitter.com/users/show.xml?screen_name=". $usuario_twitter;
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_URL, $url);
     $data = curl_exec($ch);

     curl_close($ch);
     $xml = new SimpleXMLElement($data);
     $tw_fol_count = intval($xml->followers_count);
     if ($tw_fol_count == 0) { echo '30'; }
     else { echo number_format($tw_fol_count); } 

}


  
  function widget_añadirtwitterfacebookrss_init() 
	  {
	  /* Widget */
	  /* ---------------------------- */
	 
	  function añadirtwitterfacebookrss()
	  {
		  
		  
		  
		  include("visual.php");
		  
		 
		  
	  }
	  
	  /* -------------------------- */
	  /* Fin Widget */
	  
	  function widget_añadirtwitterfacebookrss($args) 
	  {
	  
		  $options = get_option('widget_añadirtwitterfacebookrss');
		  $title = empty($options['title']) ? __('Teguidores') : $options['title'];
			
		  extract($args);
		  echo $before_widget;
		  echo $before_title;
		  echo $title;
		  echo $after_title;
		  añadirtwitterfacebookrss();
		  echo $after_widget;
	  }  
	  
	 
	  
	  function widget_añadirtwitterfacebookrss_control()
	  {
	  
		
		$options = $newoptions = get_option('widget_añadirtwitterfacebookrss');
		
		
		if ( $_POST['widget_añadirtwitterfacebookrss-submit'] ) 
		{
			
			$newoptions['title'] = strip_tags(stripslashes($_POST['widget_añadirtwitterfacebookrss-title']));
		}
				
		
		if ( $options != $newoptions ) 
		{
			$options = $newoptions;
			update_option('widget_añadirtwitterfacebookrss', $options);
		}
						
		$title = attribute_escape($options['title']);

		echo '<p><label for="añadirtwitterfacebookrss-title">';
		echo 'Title: <input style="width: 250px;" id="widget_añadirtwitterfacebookrss-title" name="widget_añadirtwitterfacebookrss-title" type="text" value="';
		echo $title;
		echo '" />';
		echo '</label></p>';
		echo '<input type="hidden" id="widget_añadirtwitterfacebookrss-submit" name="widget_añadirtwitterfacebookrss-submit" value="1" />';
	  }
	  
	  
    register_sidebar_widget('Teguidores', 'widget_añadirtwitterfacebookrss');
	

    register_widget_control('Teguidores', 'widget_añadirtwitterfacebookrss_control');
	
  }
    
  add_action('plugins_loaded', 'widget_añadirtwitterfacebookrss_init');

?>