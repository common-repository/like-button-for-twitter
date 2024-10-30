<?php
/*
Plugin Name: Truelike
Plugin URI: http://truelike.com
Description: The Truelike buttons make it easy for your visitors to "Like" or "Rate" (review) the articles and products on your site in the form of a tweet!  Those reviews are then also published on <a href="http://truelike.com" >Truelike.com</a> where they are ranked against other reviews, and each review provides backlinks to your site.  Sites whose visitors tweet the most rise to the top of the rankings!
Version: 0.9.4
Author: Truelike.com
Author URI: http://truelike.com
Plugin URI: http://truelike.com/getbutton.php

Copyright 2010 Truelike.com (email : support@truelike.com)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    please note that under the GNU GPL license only the code is usable,
    the images are not part of the code and therefore under seperate
    copyrights and licensing.

*/

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

add_action('admin_menu', 'true_like_add_menu');
function true_like_add_menu() {
	$page = add_options_page('Truelike', 'Truelike', 'manage_options', 'true-like', 'true_like_settings_page');
	add_action('admin_print_scripts-'.$page, 'true_like_admin_scripts');	
	add_action('admin_print_styles-'.$page, 'true_like_admin_styles');
}

function true_like_admin_scripts() {
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
	wp_register_script('vertical-tabs', WP_PLUGIN_URL.'/like-button-for-twitter/js/verticaltabs.pack.js');
	wp_enqueue_script('jquery');
	wp_enqueue_script('vertical-tabs');
}

function true_like_admin_styles() {
	wp_enqueue_style('vertical-tabs');
}

add_action('admin_init', 'true_like_admin_init');
function true_like_admin_init() {
	wp_register_style('vertical-tabs', WP_PLUGIN_URL.'/like-button-for-twitter/css/verticaltabs.css');	
	register_setting('true_like_options', 'true_like_options', 'true_like_validate');
}

function true_like_settings_page() { ?>
    <div class="wrap">
		<script type="text/javascript">
			$(document).ready(function(){
				$("#textExample").verticaltabs({speed: 500,slideShow: false});
			});
			var categoryList = new Array;
			categoryList['activity'] = ['ANY']; categoryList['event'] = ['ANY']; categoryList['sport'] = ['sports']; categoryList['bar'] = ['food_drink']; categoryList['cafe'] = ['food_drink']; categoryList['company'] = ['business_finance']; categoryList['hotel'] = ['places']; categoryList['restaurant'] = ['food_drink']; categoryList['cause'] = ['causes']; categoryList['sports_league'] = ['sports']; categoryList['sports_team'] = ['sports']; categoryList['band'] = ['entertainment']; categoryList['government'] = ['politics']; categoryList['non_profit'] = ['causes']; categoryList['school'] = ['education']; categoryList['university'] = ['education']; categoryList['actor'] = ['entertainment']; categoryList['athlete'] = ['sports']; categoryList['author'] = ['entertainment']; categoryList['director'] = ['entertainment']; categoryList['musician'] = ['entertainment']; categoryList['politician'] = ['politics']; categoryList['public_figure'] = ['ANY']; categoryList['city'] = ['places']; categoryList['country'] = ['places']; categoryList['landmark'] = ['places']; categoryList['state_province'] = ['places']; categoryList['album'] = ['entertainment']; categoryList['auto_product'] = ['automotive']; categoryList['beer'] = ['food_drink']; categoryList['book'] = ['ANY']; categoryList['clothing_jewelry'] = ['fashion']; categoryList['computers'] = ['technology']; categoryList['drink'] = ['food_drink']; categoryList['electronics'] = ['technology']; categoryList['food'] = ['food_drink']; categoryList['game'] = ['games']; categoryList['video_game'] = ['games']; categoryList['hardware'] = ['home_family']; categoryList['health_beauty'] = ['health_beauty']; categoryList['home_garden'] = ['home_family']; categoryList['magazine'] = ['ANY']; categoryList['movie'] = ['entertainment']; categoryList['product'] = ['ANY']; categoryList['recipe'] = ['food_drink']; categoryList['song'] = ['entertainment']; categoryList['spirits'] = ['food_drink']; categoryList['sports_equipment'] = ['sports']; categoryList['tv_show'] = ['entertainment']; categoryList['wine'] = ['food_drink']; categoryList['auto'] = ['automotive']; categoryList['motorcycle'] = ['automotive']; categoryList['recreational'] = ['automotive']; categoryList['truck_suv'] = ['automotive']; categoryList['vehicle_other'] = ['automotive']; categoryList['article'] = ['ANY']; categoryList['blog'] = ['ANY']; categoryList['image'] = ['ANY']; categoryList['video'] = ['ANY']; categoryList['page'] = ['ANY']; categoryList['website'] = ['ANY'];

			function updateCategoryDropdown(sender) {
				var objectType = sender.value;

				if (categoryList[objectType] == "ANY") {
					document.getElementById(sender.id+'_category').style.visibility = "visible";
				} else {
					document.getElementById(sender.id+'_category').value = categoryList[objectType];
					document.getElementById(sender.id+'_category').style.visibility = "hidden";
				}
			}
		</script>
		<?php
		//delete_option('true_like_options');
		$true_like_options = get_option('true_like_options');
		if(!$true_like_options) {
			$true_like_options = array();
			$true_like_options[0][ID] = 0;
			$true_like_options[0][Title] = "Global Settings";
			$true_like_options[0][Type] = "Like";
			$true_like_options[0][DataType] = "article";
			$true_like_options[0][DataCategory] = "0";
			$true_like_options[0][Position] = 1;
			$true_like_options[0][Hashtag] = "";
		}
		$categories = get_categories('parent=0');
		$options = array();
		$options[0] = $true_like_options[0];
		$i = 1;
		foreach($categories as $category) {
			$options[$i][ID] 		= $category->cat_ID;
			$options[$i][Title] 	= $category->cat_name;
			$status = true;
			foreach($true_like_options as $true_like_option) {
				if($true_like_option[ID] == $category->cat_ID) {
					$status = false;
					$options[$i][Type] 					= $true_like_option[Type];
					$options[$i][DataType] 			= $true_like_option[DataType];
					$options[$i][DataCategory] 	= $true_like_option[DataCategory];
					$options[$i][Position] 			= $true_like_option[Position];
					$options[$i][Hashtag] 			= $true_like_options[0][Hashtag]; 	// Hashtag is global
				}
			}
			if($status == true) { 
				$options[$i][Type] 						= $true_like_options[0][Type];
				$options[$i][DataType] 				= 0; // Use the Global Setting (even if the global gets changed)
				$options[$i][DataCategory] 		= 0;
				$options[$i][Position] 				= 0; 
				$options[$i][Hashtag] 				= $true_like_options[0][Hashtag];
			}
			$i++;
		}
		?>
		<h2><a href="http://truelike.com/"><img src="http://truelike.com/css/images/logo.gif" /></a></h2>
		<form method="post" action="options.php" name="true_like_form">
			<?php settings_fields('true_like_options'); ?>
			<div class="verticalslider" id="textExample">
				<ul class="verticalslider_tabs">
					<?php foreach($options as $option) { ?>
						<li><a href="javascript:;"><?php echo $option[Title]; ?></a></li>
					<?php } ?>
				</ul>
				<ul class="verticalslider_contents">
					<?php
					$i = 0;
					foreach($options as $option) { ?>
						<li>
							<table>
								<tr>
									<td>Button Type</td>
									<td>:</td>
									<td>
										<input type="hidden" name="true_like_options[<?php echo $i; ?>][ID]" value="<?php echo $option[ID]; ?>" />
										<input type="hidden" name="true_like_options[<?php echo $i; ?>][Title]" value="<?php echo $option[Title]; ?>" />
										<select name="true_like_options[<?php echo $i; ?>][Type]" id="item_<?php echo $option[ID]; ?>_type" title="We recommend Like buttons for blog posts and articles, and Rate buttons for products or product reviews">
											<option value="Like">Like</option>
											<option value="Rate">Rate</option>
										</select>
										<script type="text/javascript">
											var selObj = document.getElementById('item_<?php echo $option[ID]; ?>_type');
											var selected = "<?php echo $option[Type]; ?>";
											for(var i = 0; i < selObj.options.length; i++) {
												if(selObj.options[i].value == selected) {
													selObj.selectedIndex = i;
												}
											}
										</script>
									</td>
								</tr>
								<tr>
									<td>Page/Item Type</td>
									<td>:</td>
									<td>
										<select name="true_like_options[<?php echo $i; ?>][DataType]" id="item_<?php echo $option[ID]; ?>_data_type" 
											onChange="updateCategoryDropdown(this)" title="Please select the most appropriate type to describe what each of your posts is about">
											<?php if ($i > 0) { ?>
												<option value="0">Use global setting</option>
											<?php } ?>
											<option value="none"></option>
											<option value="none">----- activity -----</option>
											<option value="activity">Activities</option>
											<option value="event">Events</option>
											<option value="sport">Sports</option>
											<option value="none"></option>
											<option value="none">----- business -----</option>
											<option value="bar">Bars</option>
											<option value="cafe">Cafes</option>
											<option value="company">Companies</option>
											<option value="hotel">Hotels</option>
											<option value="restaurant">Restaurants</option>
											<option value="none"></option>
											<option value="none">----- group -----</option>
											<option value="cause">Causes</option>
											<option value="sports_league">Sports Leagues</option>
											<option value="sports_team">Sports Teams</option>
											<option value="none"></option>
											<option value="none">----- organization -----</option>
											<option value="band">Bands</option>
											<option value="government">Governmental</option>
											<option value="non_profit">Non-Profits</option>
											<option value="school">Schools</option>
											<option value="university">Universities</option>
											<option value="none"></option>
											<option value="none">----- person -----</option>
											<option value="actor">Actors</option>
											<option value="athlete">Athletes</option>
											<option value="author">Authors</option>
											<option value="director">Directors</option>
											<option value="musician">Musicians</option>
											<option value="politician">Politician</option>
											<option value="public_figure">Public Figures</option>
											<option value="none"></option>
											<option value="none">----- place -----</option>
											<option value="city">Cities</option>
											<option value="country">Countries</option>
											<option value="landmark">Landmarks</option>
											<option value="state_province">States and Provinces</option>
											<option value="none"></option>
											<option value="none">----- product -----</option>
											<option value="album">Albums</option>
											<option value="auto_product">Auto Products</option>
											<option value="beer">Beer</option>
											<option value="book">Books</option>
											<option value="clothing_jewelry">Clothing and Jewelry</option>
											<option value="computers">Computers</option>
											<option value="drink">Drinks</option>
											<option value="electronics">Electronics</option>
											<option value="food">Food</option>
											<option value="game">Games</option>
											<option value="video_game">Games, Video</option>
											<option value="hardware">Hardware</option>
											<option value="health_beauty">Health and Beauty</option>
											<option value="home_garden">Home and Garden</option>
											<option value="magazine">Magazines</option>
											<option value="movie">Movies</option>
											<option value="product">Products</option>
											<option value="recipe">Recipes</option>
											<option value="song">Songs</option>
											<option value="spirits">Spirits</option>
											<option value="sports_equipment">Sports Equipment</option>
											<option value="tv_show">TV Shows</option>
											<option value="wine">Wine</option>
											<option value="none"></option>
											<option value="none">----- vehicle -----</option>
											<option value="auto">Autos</option>
											<option value="motorcycle">Motorcycles</option>
											<option value="recreational">Recreational Vehicle</option>
											<option value="truck_suv">Trucks and SUVs</option>
											<option value="vehicle_other">Vehicles (Other)</option>
											<option value="none"></option>
											<option value="none">----- website -----</option>
											<option value="article">Articles</option>
											<option value="blog">Blogs</option>
											<option value="image">Images</option>
											<option value="video">Videos</option>
											<option value="page">Web Pages</option>
											<option value="website">Websites</option>
										</select>
										<script type="text/javascript">
											var selObj = document.getElementById('item_<?php echo $option[ID]; ?>_data_type');
											var selected = "<?php echo $option[DataType]; ?>";
											for(var i = 0; i < selObj.options.length; i++) {
												if(selObj.options[i].value == selected) {
													selObj.selectedIndex = i;
												}
											}
										</script>
									</td>
								</tr>
								<tr>
									<td>Page/Item Category</td>
									<td>:</td>
									<td>
										<select name="true_like_options[<?php echo $i; ?>][DataCategory]" id="item_<?php echo $option[ID]; ?>_data_type_category" style="visibility:hidden;">
											<?php if ($i > 0) { ?>
												<option value="0">Use global setting</option>
											<?php } ?>
											<option value="none">Category</option>
											<option value="automotive">Automotive</option>
											<option value="business_finance">Business and Finance</option>
											<option value="causes">Causes</option>
											<option value="education">Education</option>
											<option value="entertainment">Entertainment</option>
											<option value="fashion">Fashion</option>
											<option value="food_drink">Food and Drink</option>
											<option value="games">Games</option>
											<option value="health_beauty">Health and Beauty</option>
											<option value="home_family">Home and Family</option>
											<option value="humor">Humor</option>
											<option value="lifestyle">Lifestyle</option>
											<option value="news_media">News and Media</option>
											<option value="places">Places</option>
											<option value="politics">Politics</option>
											<option value="science_nature">Science and Nature</option>
											<option value="sports">Sports</option>
											<option value="technology">Technology</option>
											<option value="weird">Weird</option>
										</select>
										<script type="text/javascript">
											updateCategoryDropdown(document.getElementById('item_<?php echo $option[ID]; ?>_data_type'))
											var selObj = document.getElementById('item_<?php echo $option[ID]; ?>_data_type_category');
											var selected = "<?php echo $option[DataCategory]; ?>";
											for(var i = 0; i < selObj.options.length; i++) {
												if(selObj.options[i].value == selected) {
													selObj.selectedIndex = i;
												}
											}
										</script>
									</td>
								</tr>
								<tr>
									<td>Button Position</td>
									<td>:</td>
									<td>
										<select name="true_like_options[<?php echo $i; ?>][Position]" id="item_<?php echo $option[ID]; ?>_position">
											<?php if ($i > 0) { ?>
												<option value="0">Use global setting</option>
											<?php } ?>
											<option value="1">Top right</option>
											<option value="2">Top left</option>
											<option value="3">Bottom right</option>
											<option value="4">Bottom left</option>
										</select>
										<script type="text/javascript">
											var selObj = document.getElementById('item_<?php echo $option[ID]; ?>_position');
											var selected = "<?php echo $option[Position]; ?>";
											for(var i = 0; i < selObj.options.length; i++) {
												if(selObj.options[i].value == selected) {
													selObj.selectedIndex = i;
												}
											}
											if(selObj.selectedIndex != 0) {
												selObj.style.visibility = "visible";
											}
										</script>
									</td>
								</tr>
								<?php if ($i == 0) { ?>
								<tr>
									<td>Hashtag (optional)</td>
									<td>:</td>
									<td>
										#<input type="text" name="true_like_options[0][Hashtag]" id="item_<?php echo $option[0]; ?>_hashtag" value="<?php echo $option[Hashtag]; ?>" title="This hashtag will appear in your visitors' tweets when they use the Truelike button.">
									</td>
								</tr>
								<?php } ?>
							</table>
						</li>
					<?php
						$i++;
					} ?>
				</ul>
				<div class="clear"></div>
			</div> 
			<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" /></p>
		</form>
    </div>
<?php
}

function true_like_section_text() {
	return '';
}

function true_like_validate($input) {
	return $input;
}

add_filter('the_content', 'true_like_filter_content');
function true_like_filter_content($content) {
	global $post;
	$true_like_options 					= get_option('true_like_options');
	$option 								= array();
	
	if($true_like_options) {
	
		$option[ID] 							= $true_like_options[0][ID];
		$option[Type] 						= $true_like_options[0][Type];
		$option[DataType] 					= $true_like_options[0][DataType];
		$option[DataCategory] 			= $true_like_options[0][DataCategory];
		$option[Position] 					= $true_like_options[0][Position];
		$option[Hashtag] 					= $true_like_options[0][Hashtag];
		
		if(is_category()) {
			$category = get_query_var('cat');
			foreach($true_like_options as $true_like_option) {
				if($true_like_option[ID] == $category) {
					$option[ID] 							= $true_like_option[ID];
					$option[Type] 						= $true_like_option[Type];
					$option[DataType] 					= $true_like_option[DataType];
					$option[DataCategory] 			= $true_like_option[DataCategory];
					$option[Position] 					= $true_like_option[Position];
					$option[Hashtag] 					= $true_like_options[0][Hashtag];
				}
			}
		} else if(is_single()) {
			$categories 	= get_the_category($post->ID);
			$category 		= $categories[0]->cat_ID;
			foreach($true_like_options as $true_like_option) {
				if($true_like_option[ID] == $category) {
					$option[ID] 							= $true_like_option[ID];
					$option[Type] 						= $true_like_option[Type];
					$option[DataType] 					= $true_like_option[DataType];
					$option[DataCategory] 			= $true_like_option[DataCategory];
					$option[Position] 					= $true_like_option[Position];
					$option[Hashtag] 					= $true_like_options[0][Hashtag];  
				}
			}
		}
	} else {
		$option[ID] 							= 0;
		$option[Type] 						= "Like";
		$option[DataType] 					= "article";
		$option[DataCategory] 			= "";
		$option[Position] 					= 1;
		$option[Hashtag] 					= "";
	}		

	// If these are set to use Global Settings, look up the global setting
	if($option[Position] == 0) {
		$option[Position] = $true_like_options[0][Position];
	}
	if($option[DataType] == 0) {
		$option[DataType] = $true_like_options[0][DataType];
		$option[DataCategory] = $true_like_options[0][DataCategory];
	}
	
	$buttonCode = '<a href="http://truelike.com/review" class="'.(($option[Type] == "Rate") ? "tlc-rate-button" : "tlc-like-button").'" data-text="'.get_the_title($post->ID).'" data-counturl="'.get_permalink($post->ID).'" data-type="'.$option[DataType].'" data-category="'.$option[DataCategory].'" data-hashtag="'.$option[Hashtag].'">Like</a>';
	
	if($option[Position] == 1) {
		return "<p class='truelike' style='text-align: right;'>".$buttonCode."</p>".$content;
	} else if($option[Position] == 2) {
		return "<p class='truelike' style='text-align: left;'>".$buttonCode."</p>".$content;
	} else if($option[Position] == 3) {
		return $content."<p class='truelike' style='text-align: right;'>".$buttonCode."</p>";
	} else if($option[Position] == 4) {
		return $content."<p class='truelike' style='text-align: left;'>".$buttonCode."</p>";
	} else {
		return "<p class='truelike' style='text-align: right;'>".$buttonCode."</p>".$content;
	}
}

add_action('wp_footer', 'true_like_wp_footer');
function true_like_wp_footer() {
	echo '<script type="text/javascript" src="http://truelike.com/js/buttons.js"></script>';
}
?>