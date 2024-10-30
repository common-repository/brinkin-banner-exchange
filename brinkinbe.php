<?php
/*
Plugin Name: Brinkin Banner Exchange
Plugin URI: http://banners.brinkin.com/wordpress_plugin
Description: Increase visitors, or make money using the Brinkin Banner Exchange on your blog. Configure it at <a href="options-general.php?page=brinkinbe.php">Settings</a>.
Version: 1.06
Author: Ben Ford
Author URI: http://banners.brinkin.com
*/

/*  Copyright 2009  Ben Ford (email : ben.ford@brinkin.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists("brinkinBE")) {
	class brinkinBE {

		 var $intMaxBanners = 3 ;
		
  		function plugin_action($links, $file) {
      		if ($file == plugin_basename(dirname(__FILE__).'/brinkinbe.php')) {
    			$settings_link = "<a href='options-general.php?page=brinkinbe.php'>" . __('Settings', 'brinkin-banner-exchange') . "</a>"; 
      			array_unshift( $links, $settings_link );
      		}
      		return $links;
    	}
    	
    	function printAdminPage() {
	      
	    	if (is_admin() && isset($_POST['brinkinBEEmail'])) {
	
	          //Once we have received the email, send a get request to banners.brinkin.com to get the ID field
	          $intBrinkinBEID = wp_remote_retrieve_body( wp_remote_get("http://banners.brinkin.com/get_banner_id_from_email?email=".$_POST['brinkinBEEmail']) );
	          
	        	$arrOptions['kill_pages'] 	= $_POST['BrinkinBEKillPages'];
				$arrOptions['kill_home'] 	= $_POST['BrinkinBEKillHome'];
				$arrOptions['kill_attach'] 	= $_POST['BrinkinBEKillAttach'];
				$arrOptions['kill_front'] 	= $_POST['BrinkinBEKillFront'];
				$arrOptions['kill_cat'] 	= $_POST['BrinkinBEKillCat'];
				$arrOptions['kill_tag'] 	= $_POST['BrinkinBEKillTag'];
				$arrOptions['kill_archive'] = $_POST['BrinkinBEKillArchive'];
				
				$arrOptions['post_type'] 	= $_POST['BrinkinBEBannerType'];
				$arrOptions['align']  		= $_POST['BrinkinBEAlign'];
				$arrOptions['position']  	= $_POST['BrinkinBEPos'];
	          
	          if(is_numeric($intBrinkinBEID)) {
	          	//Store Email and User ID
	          	$arrOptions['brinkinBEEmail'] = $_POST['brinkinBEEmail'];
	          	$arrOptions['brinkinBEID'] = $intBrinkinBEID;
	        
	          	update_option('brinkinBE', $arrOptions);
	          	  	
	          	$strResult = __("Settings Updated", 'brinkin-banner-exchange');
	          } else {
	          	$strResult = __("An error occured while trying to update the Brinkin Banner Exchange settings.")."<BR><BR>".$intBrinkinBEID;
	          }
	         		        
				?><div class="updated"><p><strong><?php echo $strResult;?></strong></p> </div><?php
			
			} 
			
			$arrOptions = get_option('brinkinBE');
			
			?>
			<div class="wrap">
				<?php if(function_exists("screen_icon")) { screen_icon(); } ?><h2>Brinkin Banner Exchange</h2>
			
					<p><?php _e('To setup the plugin simply provide your email address for your existing account registered with <A HREF=\'http://banners.brinkin.com/\'>Brinkin Banner Exchange</A>.<BR><BR>To provide plugin feedback or to request a new feature <a href="http://brinkinbanners.uservoice.com">click here</a>.', 'brinkin-banner-exchange'); ?></p>
					
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					
					<h3><? _e('Main Settings', 'brinkin-banner-exchange'); ?></h3>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('Your Email Address', 'brinkin-banner-exchange'); ?></th>
							<td>
								<input type="text" value="<?php echo attribute_escape( $arrOptions['brinkinBEEmail'] ); ?>" name="brinkinBEEmail"/>
							</td>
						</tr>
						<tr><td colspan=2><?php _e('If you have not previously registered with our website, an account will automatically be created for you when you click save changes.', 'brinkin-banner-exchange'); ?></td></tr>
					</table>

					<h3><? _e('Display Settings', 'brinkin-banner-exchange'); ?></h3>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e('Banner size in post:', 'brinkin-banner-exchange') ?></th>
							<td>
								<select name="BrinkinBEBannerType">
								     <option <?php if($arrOptions['post_type'] == 1 || !$arrOptions['post_type']) echo "selected"; ?> value="1">468 X 60 (Banner)</option>
								     <option <?php if($arrOptions['post_type'] == 2) echo "selected"; ?> value="2">300 X 250 (Medium Rectangle)</option>
								     <option <?php if($arrOptions['post_type'] == 3) echo "selected"; ?> value="3">728 X 90 (Leaderboard)</option>
								     <option <?php if($arrOptions['post_type'] == 4) echo "selected"; ?> value="4">120 X 600 (Skyscraper)</option>
								     <option <?php if($arrOptions['post_type'] == 5) echo "selected"; ?> value="5">125 X 125 (Button)</option>
							     </select>
							</td>
						</tr>					
						<tr valign="top">
							<th scope="row"><?php _e('Suppress banners on:', 'brinkin-banner-exchange') ?></th>
							<td>
								<label for="BrinkinBEKillPages" title="<?php _e('Pages are everything other then posts', 'brinkin-banner-exchange') ; ?>">						<input type="checkbox" id="BrinkinBEKillPages" name="BrinkinBEKillPages" value="true" <?php if ($arrOptions['kill_pages']) { echo('checked="checked"'); }?> /> <?php _e('Pages', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillAttach" title="<?php _e('Pages that show attachments', 'brinkin-banner-exchange') ; ?>">								<input type="checkbox" id="BrinkinBEKillAttach" name="BrinkinBEKillAttach" <?php if ($arrOptions['kill_attach']) { echo('checked="checked"'); }?> /> <?php _e('Attachment Page', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillHome" title="<?php _e('Home Page and Front Page are the same for most blogs', 'brinkin-banner-exchange') ; ?>">		<input type="checkbox" id="BrinkinBEKillHome" name="BrinkinBEKillHome" <?php if ($arrOptions['kill_home']) { echo('checked="checked"'); }?> /> <?php _e('Home Page', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillFront" title="<?php _e('Home Page and Front Page are the same for most blogs', 'brinkin-banner-exchange') ; ?>">		<input type="checkbox" id="BrinkinBEKillFront" name="BrinkinBEKillFront" <?php if ($arrOptions['kill_front']) { echo('checked="checked"'); }?> /> <?php _e('Front Page', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillCat" title="<?php _e('Pages that come up when you click on category names', 'brinkin-banner-exchange') ; ?>">			<input type="checkbox" id="BrinkinBEKillCat" name="BrinkinBEKillCat" <?php if ($arrOptions['kill_cat']) { echo('checked="checked"'); }?> /> <?php _e('Category Pages', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillTag" title="<?php _e('Pages that come up when you click on tag names', 'brinkin-banner-exchange') ; ?>">				<input type="checkbox" id="BrinkinBEKillTag" name="BrinkinBEKillTag" <?php if ($arrOptions['kill_tag']) { echo('checked="checked"'); }?> /> <?php _e('Tag Pages', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEKillArchive" title="<?php _e('Pages that come up when you click on year/month archives', 'brinkin-banner-exchange') ; ?>">	<input type="checkbox" id="BrinkinBEKillArchive" name="BrinkinBEKillArchive" <?php if ($arrOptions['kill_archive']) { echo('checked="checked"'); }?> /> <?php _e('Archive Pages', 'brinkin-banner-exchange') ; ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Align banners in post:', 'brinkin-banner-exchange') ?></th>
							<td>
								<label for="BrinkinBEAlignLeft" title="<?php _e('Left', 'brinkin-banner-exchange') ; ?>">		<input type="radio" id="BrinkinBEAlignLeft" name="BrinkinBEAlign" value="left" <?php if ($arrOptions['align'] == "left" || !$arrOptions['align']) { echo('checked="checked"'); }?> /> <?php _e('Left', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEAlignCenter" title="<?php _e('Center', 'brinkin-banner-exchange') ; ?>">	<input type="radio" id="BrinkinBEAlignCenter" name="BrinkinBEAlign" value="center" <?php if ($arrOptions['align'] == "center") { echo('checked="checked"'); }?> /> <?php _e('Center', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEAlignRight" title="<?php _e('Right', 'brinkin-banner-exchange') ; ?>">		<input type="radio" id="BrinkinBEAlignRight" name="BrinkinBEAlign" value="right" <?php if ($arrOptions['align'] == "right") { echo('checked="checked"'); }?> /> <?php _e('Right', 'brinkin-banner-exchange') ; ?></label><BR>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Banner position in post:', 'brinkin-banner-exchange') ?></th>
							<td>
								<label for="BrinkinBEPosTop" title="<?php _e('Start of post', 'brinkin-banner-exchange') ; ?>">					<input type="radio" id="BrinkinBEPosTop" name="BrinkinBEPos" value="top" <?php if ($arrOptions['position'] == "top") { echo('checked="checked"'); }?> /> <?php _e('Start of post', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEPosFirstPara" title="<?php _e('After first paragraph', 'brinkin-banner-exchange') ; ?>">	<input type="radio" id="BrinkinBEPosFirstPara" name="BrinkinBEPos" value="first-paragraph" <?php if ($arrOptions['position'] == "first-paragraph") { echo('checked="checked"'); }?> /> <?php _e('After first paragraph', 'brinkin-banner-exchange') ; ?></label><BR>
								<label for="BrinkinBEPosBottom" title="<?php _e('End of post', 'brinkin-banner-exchange') ; ?>">				<input type="radio" id="BrinkinBEPosBottom" name="BrinkinBEPos" value="bottom" <?php if ($arrOptions['position'] == "bottom" || !$arrOptions['position']) { echo('checked="checked"'); }?> /> <?php _e('End of post', 'brinkin-banner-exchange') ; ?></label><BR>
							</td>
						</tr>
					</table>
					
					<?php 

					//Try To Get Statistics
					if($arrOptions['brinkinBEID'] && $arrOptions['brinkinBEEmail']) {
						$strResult = wp_remote_retrieve_body( wp_remote_get("http://banners.brinkin.com/get_stats_from_email?email=".$arrOptions['brinkinBEEmail']) );
						if($strResult) echo $strResult;
					}
					
					?>
					
					<p class="submit">
						<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'brinkin-banner-exchange') ?>" />
					</p>
					
					</form>	
				
			</div>
			<?php
			
			
    	}//End function printAdminPage()
  	
    	 function handle_content($content) {
    	 	
    	 	$arrOptions = get_option('brinkinBE');
    	 	
    	 	if(!$arrOptions['brinkinBEID']) {
    	 		//Account is not yet setup correctly
    	 		return $content;
    	 	}
    	 	
    	 	if(!$arrOptions['post_type']) $arrOptions['post_type'] = 1;
    	 	
    	 	//Check For Inline Ad's
	      	while(stristr($content, "[Brinkin-Banner]") !== FALSE) {
	      	
	      		if ($GLOBALS['brinkinBannerCount'] >= $this->intMaxBanners) {
	      			//Remove Ad Code
	      			$content = str_ireplace("[Brinkin-Banner]", "", $content);
	      		} else {
	      			//Replace Ad Code With Ad
	      			$content = preg_replace("/(\[Brinkin-Banner\])/i", $this->getAdCode($arrOptions['post_type'], $arrOptions), $content, 1);
	      		}
	      		
	      	}
    	 	
    	 	//Check For Disabled Page Types
	    	if ($arrOptions['kill_pages'] && is_page()) return $content ;
	      	if ($arrOptions['kill_home'] && is_home()) return $content ;
	      	if ($arrOptions['kill_attach'] && is_attachment()) return $content ;
	      	if ($arrOptions['kill_front'] && is_front_page()) return $content ;
	      	if ($arrOptions['kill_cat'] && is_category()) return $content ;
	      	if ($arrOptions['kill_tag'] && is_tag()) return $content ;
	      	if ($arrOptions['kill_archive'] && is_archive()) return $content ;
	      	
		    if ($GLOBALS['brinkinBannerCount'] >= $this->intMaxBanners) return $content ;
		      	          
	        $midtext = $this->getAdCode($arrOptions['post_type'], $arrOptions);
	          
	        $strPosition = $arrOptions['position'];
	        if(!$strPosition) $strPosition = "bottom";
	        
	        if($strPosition == "top") {
	            $content = $midtext.$content;
	        } elseif($strPosition == "bottom") {
		        $content = $content.$midtext;
	        } elseif($strPosition == "first-paragraph") {
	        	$intFirstParagraph = stripos($content, "</p>")+4;
	        	if($intFirstParagraph) {
	        		$content = substr($content, 0, $intFirstParagraph).$midtext.substr($content, $intFirstParagraph);
	        	} else {
	        		$content = $content.$midtext;
	        	}
	        }
		        
		    return $content;
		 }

		 function getAdCode($intBT=1, $arrOptions) {
		 	
		 	$intID = $arrOptions['brinkinBEID'];
		 
		 	//Record Number of Ad's Displayed
		 	$GLOBALS['brinkinBannerCount']++;
		 	
		 	$strResult = '<!-- Post[count: ' . $GLOBALS['brinkinBannerCount'] . '] -->
		 	<div '.($arrOptions['align'] ? "style='text-align: ".$arrOptions['align'].";' " : "").'>
		 	<!-- Start of Brinkin Banner Exchange (http://banners.brinkin.com) Code -->
		 	<script type="text/javascript">
		 	<!--
			var brinkinBannerID = '.$intID.';
			//-->
			</script>
		 	<script type="text/javascript" language="javascript" src="http://code.banners.brinkin.com/banner_'.$intBT.'.js"></script>
		 	<!-- End of Brinkin Banner Exchange Code -->
		 	</div>';
		 	
		 	return $strResult;
		 }
		 
		 function handle_init() {
		 	
		 	load_plugin_textdomain ('brinkin-banner-exchange');
		 	
		 	//Attempt To Get ID
		 	$arrOptions = get_option('brinkinBE');
		 	
		 	if(!$arrOptions['brinkinBEID'] && !isset($_POST['brinkinBEEmail'])) {
				add_action('admin_notices', array($this, 'brinkinBE_warning'));
				return;
			} 
			
		 }
		 
		 function widget_control() {
		  
		 	$arrOptions = get_option('brinkinBE');
		 	
		 	if(!isset($arrOptions['widget_title'])) $arrOptions['widget_title'] = 'Advertising';
		 	
			  ?> <p><label>Title:<br><input class="widefat" name="brinkinBE_Widget_Title" type="text" value="<?php echo $arrOptions['widget_title']; ?>" /></label></p>
			     <p><label>Banner Size:<br>
			     <select name="brinkinBE_Widget_Banner_Size">
				     <option <?php if($arrOptions['widget_type'] == 1) echo "selected"; ?> value="1">468 X 60 (Banner)</option>
				     <option <?php if($arrOptions['widget_type'] == 2) echo "selected"; ?> value="2">300 X 250 (Medium Rectangle)</option>
				     <option <?php if($arrOptions['widget_type'] == 4) echo "selected"; ?> value="4">120 X 600 (Skyscraper)</option>
				     <option <?php if($arrOptions['widget_type'] == 5 || !$arrOptions['widget_type']) echo "selected"; ?> value="5">125 X 125 (Button)</option>
				  	 <option <?php if($arrOptions['widget_type'] == "6x5") echo "selected"; ?> value="6x5">125 X 125 (6 Buttons)</option>
			     </select>
			     </label></p>
			  <?php
			  
			   if (isset($_POST['brinkinBE_Widget_Title']) || isset($_POST['brinkinBE_Widget_Banner_Size'])) {
			   		$arrOptions['widget_title'] = $_POST['brinkinBE_Widget_Title'];
			   	 	$arrOptions['widget_type'] = $_POST['brinkinBE_Widget_Banner_Size'];
			   	 	update_option('brinkinBE', $arrOptions);
			   }
		 	
		 }
		 
		 function handle_widget($args) {
		 
		 	$arrOptions = get_option('brinkinBE');
		 	
			extract($args);
			echo $before_widget;
			echo $before_title . $arrOptions['widget_title'] . $after_title;
			
			if($arrOptions['widget_type'] == "6x5") {
				//Build 2x6 Banner Table
				echo "<table>";
				for($i=0;$i<3;$i++) {
					echo "<tr>";
					for($j=0;$j<2;$j++) {
						echo "<td>".$this->getAdCode(5, $arrOptions)."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			} else {
				echo $this->getAdCode($arrOptions['widget_type'], $arrOptions);
			}
			
			echo $after_widget;
		}
		 
		 function brinkinBE_warning() {
			echo "
			<div id='brinkin-warning' class='updated fade'><p><strong>".__('Brinkin Banner Exchange is almost ready.', 'brinkin-banner-exchange')."</strong> ".sprintf(__('You must provide your email address in the <a href="%1$s">settings page</a> for it to work.', 'brinkin-banner-exchange'), "options-general.php?page=brinkinbe.php")."</p></div>
			";
		}
  	}
}

if (class_exists("brinkinBE")) {
	$objBrinkinBE = new brinkinBE();

	if (!function_exists("brinkinbe_ap")) {
		function brinkinbe_ap() {
			global $objBrinkinBE;
			if (function_exists('add_options_page')) {
				add_options_page('Brinkin Banner Exchange', 'Brinkin Banner Exchange', 9, basename(__FILE__), array(&$objBrinkinBE, 'printAdminPage'));
			}
		}
	}
	
	add_filter('the_content', array($objBrinkinBE, 'handle_content'));
	
	add_filter('plugin_action_links', array($objBrinkinBE, 'plugin_action'), -10, 2);

    add_action('admin_menu', 'brinkinbe_ap');
    
    register_sidebar_widget(__('Brinkin Banner Exchange'),  array($objBrinkinBE, 'handle_widget'));
    
    register_widget_control(__('Brinkin Banner Exchange'), array($objBrinkinBE, 'widget_control'));
	
    add_action('init', array($objBrinkinBE, 'handle_init'));
}

?>