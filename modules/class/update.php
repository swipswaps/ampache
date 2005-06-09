<?php
/*

 Copyright (c) 2001 - 2005 Ampache.org
 All rights reserved.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/*!
	@header Update Class
	@discussion this class handles updating from one version of 
	maintain to the next. Versions are a 6 digit number
	220000
	^
	Major Revision
	
	220000
	 ^
	 Minor Revision

	The last 4 digits are a build number...
	If Minor can't go over 9 Major can go as high as we want
*/

class Update {

	var $key;
	var $value;
	var $versions; // array containing version information

	/*!
		@function Update
		@discussion Constructor, pulls out information about
			the desired key
	*/
	function Update ( $key=0 ) {

		if ($key) {
			$info = $this->get_info();
			$this->key = $key;
			$this->value = $info->value;
			$this->versions = $this->populate_version();
		}

	} // constructor

	/*!
		@function get_info
		@discussion gets the information for the zone
	*/
	function get_info() {
		global $conf;

		$sql = "SELECT * FROM update_info WHERE key='$this->key'";
		$db_results = mysql_query($sql, dbh());

		return mysql_fetch_object($db_results);		

	} //get_info

	/*!
		@function get_version
		@discussion this checks to see what version you are currently running
			because we may not have the update_info table we have to check 
			for it's existance first. 
	*/
	function get_version() {


		/* Make sure that update_info exits */
		$sql = "SHOW TABLES LIKE 'update_info'";
		$db_results = mysql_query($sql, dbh());
		// If no table
		if (!mysql_num_rows($db_results)) {
			
			$version = '310000';		
			
		} // if table isn't found

		else {
			// If we've found the update_info table, let's get the version from it
			$sql = "SELECT * FROM update_info WHERE `key`='db_version'";
			$db_results = mysql_query($sql, dbh());
			$results = mysql_fetch_object($db_results);
			$version = $results->value;
		} 

		return $version;

	} // get_version

	/*!
		@function format_version
		@discussion make the version number pretty
	*/
	function format_version($data) {

		$new_version = substr($data,0,strlen($data) - 5) . "." . substr($data,strlen($data)-5,1) . " Build:" . 
				substr($data,strlen($data)-4,strlen($data));

		return $new_version;

	} // format_version

	/*!
		@function need_update
		@discussion checks to see if we need to update 
			maintain at all
	*/
	function need_update() {

		$current_version = $this->get_version();
		
		if (!is_array($this->versions)) {
			$this->versions = $this->populate_version();
		}
		
		/* 
		   Go through the versions we have and see if
		   we need to apply any updates
		*/
		foreach ($this->versions as $update) {
			if ($update['version'] > $current_version) {
				return true;
			}

		} // end foreach version

		return false;

	} // need_update


	/*!
		@function populate_version
		@discussion just sets an array the current differences
			that require an update
	*/
	function populate_version() {

		/* Define the array */
		$version = array();
	
                /* Version 3.2 Build 0001 */
                $update_string = "- Add update_info table to the database<br />" .
                                 "- Add Now Playing Table<br />" .
                                 "- Add album art columns to album table<br />" . 
				 "- Compleatly Changed Preferences table<br />" . 
				 "- Added Upload table<br />";
                $version[] = array('version' => '320001', 'description' => $update_string);

		$update_string = "- Add back in catalog_type for XML-RPC Mojo<br />" . 
				 "- Add level to access list to allow for play/download/xml-rpc share permissions<br />" .
				 "- Changed access_list table to allow start-end (so we can set full ip ranges)<br />" . 
				 "- Add default_play to preferences to allow quicktime/localplay/stream<br />" .
				 "- Switched Artist ID from 10 --> 11 to match other tables<br />";
		$version[] = array('version' => '320002', 'description' => $update_string);

		$update_string = "- Added a last_seen field user table to track users<br />" .
				 "- Made preferences table key/value based<br />";

		$version[] = array('version' => '320003', 'description' => $update_string);

		$update_string = "- Added play_type to preferences table<br />" .
				 "- Removed multicast,downsample,localplay from preferences table<br />" .
				 "- Dropped old config table which was no longer needed<br />";

		$version[] = array('version' => '320004', 'description' => $update_string);

		$update_string = "- Added type to preferences to allow for site/user preferences<br />";

		$version[] = array('version' => '330000', 'description' => $update_string);

		$update_string = "- Added Year to album table<br />" . 
				 "- Increased length of password field in User table<br />";

		$version[] = array('version' => '330001', 'description' => $update_string);

		$update_string = "- Changed user.access to varchar from enum for more flexibility<br />" .
				 "- Added catalog.private for future catalog access control<br />" . 
				 "- Added user_catalog table for future catalog access control<br />";
		
		
		$version[] = array('version' => '330002', 'description' => $update_string);

		$update_string = "- Added user_preferences table to once and for all fix preferences.<br />" . 
				 "- Moved Contents of preferences into new table, and modifies old preferences table.<br />";

		$version[] = array('version' => '330003', 'description' => $update_string);

		$update_string = "- Changed song comment from varchar255 in order to handle comments longer than 255 chr.<br />" . 
				 "- Added Language and Playlist Type as a per user preference.<br />" . 
				 "- Added Level to Catalog_User table for future use.<br />" . 
				 "- Added gather_types to Catalog table for future use.<br />";
				

		$version[] = array('version' => '330004', 'description' => $update_string);
		
		$update_string = "- Added Theme config option.<br />";

		$version[] = array('version' => '331000', 'description' => $update_string);

		$update_string = "- Added Elipse Threshold Preferences.<br />";

		$version[] = array('version' => '331001', 'description' => $update_string);	


		return $version;

	} // populate_version

	/*!
		@function display_update
		@discussion This displays a list of the needed
			updates to the database. This will actually
			echo out the list...
	*/
	function display_update() {

		$current_version = $this->get_version();
		if (!is_array($this->versions)) {
			$this->versions = $this->populate_version();
		} 

		echo "<ul>\n";

		foreach ($this->versions as $version) {
		
			if ($version['version'] > $current_version) {
				$updated = true;
				echo "<b>Version: " . $this->format_version($version['version']) . "</b><br />";
				echo $version['description'] . "<br />\n"; 
			} // if newer

		} // foreach versions

		echo "</ul>\n";

		if (!$updated) { echo "<p align=\"center\">No Updates Needed [<a href=\"" . conf('web_path') . "\">Return]</a></p>"; }
	} // display_update

	/*!
		@function run_update
		@discussion This function actually updates the db.
			it goes through versions and finds the ones 
			that need to be run. Checking to make sure
			the function exists first.
	*/
	function run_update() {
	
		/* Nuke All Active session before we start the mojo */
		$sql = "DELETE * FROM session";
		$db_results = mysql_query($sql, dbh());

	
		$methods = array();
		
		$current_version = $this->get_version();
		
		$methods = get_class_methods('Update');
		
		if (!is_array($this->versions)) { 
			$this->versions = $this->populate_version();
		}

		foreach ($this->versions as $version) { 


			// If it's newer than our current version
			// let's see if a function exists and run the 
			// bugger
			if ($version['version'] > $current_version) { 
				$update_function = "update_" . $version['version'];
				if (in_array($update_function,$methods)) {
					$this->{$update_function}();
				}

			} 
		
		} // end foreach version

	} // run_update

	/*!
		@function set_version
		@discussion sets a new version takes
			a key and value
	*/
	function set_version($key,$value) {

		$sql = "UPDATE update_info SET value='$value' WHERE `key`='$key'";
		$db_results = mysql_query($sql, dbh());		

	} //set_version

        /*!
                @function update_320001
                @discussion Migration function for 3.2 Build 0001
        */
        function update_320001() {

                // Add the update_info table to the database
                $sql = "CREATE TABLE `update_info` (`key` VARCHAR( 128 ) NOT NULL ,`value` VARCHAR( 255 ) NOT NULL ,INDEX ( `key` ) )";
                $db_results = mysql_query($sql, dbh());

		// Insert the first version info
		$sql = "INSERT INTO update_info (`key`,`value`) VALUES ('db_version','320001')";
		$db_results = mysql_query($sql, dbh());

                // Add now_playing table to database
                $sql = "CREATE TABLE now_playing (" .
                        "id int(11) unsigned NOT NULL auto_increment, " .
                        "song_id int(11) unsigned NOT NULL default '0', " .
                        "user_id int(11) unsigned default NULL, " .
                        "start_time int(11) unsigned NOT NULL default '0', " .
                        "PRIMARY KEY (id) " .
                        ") TYPE=MyISAM";
                $db_results = mysql_query($sql, dbh());

		// Add the upload table to the database
		$sql = "CREATE TABLE upload ( id int(11) unsigned NOT NULL auto_increment, `user` int(11) unsigned NOT NULL," .
			"`file` varchar(255) NOT NULL , `comment` varchar(255) NOT NULL , action enum('add','quarantine','delete') NOT NULL default 'quarantine', " .
			"addition_time int(11) unsigned default '0', PRIMARY KEY  (id), KEY action (`action`), KEY user (`user`) )";
		$db_results = mysql_query($sql, dbh());
		
		/* 
		  Ok we need to compleatly tweak the preferences table 
		  first things first, nuke the damn thing so we can 
		  setup our new mojo
		*/
		$sql = "DROP TABLE `preferences`";
		$db_results = mysql_query($sql, dbh());

		$sql = "CREATE TABLE `preferences` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT , `user` INT( 11 ) UNSIGNED NOT NULL ," .
			"`download` ENUM( 'true', 'false' ) DEFAULT 'false' NOT NULL , `upload` ENUM( 'disabled', 'html', 'gui' ) DEFAULT 'disabled' NOT NULL ," .
			"`downsample` ENUM( 'true', 'false' ) DEFAULT 'false' NOT NULL , `local_play` ENUM( 'true', 'false' ) DEFAULT 'false' NOT NULL ," .
			"`multicast` ENUM( 'true', 'false' ) DEFAULT 'false' NOT NULL , `quarantine` ENUM( 'true', 'false' ) DEFAULT 'true' NOT NULL ," .
			"`popular_threshold` INT( 11 ) UNSIGNED DEFAULT '10' NOT NULL , `font` VARCHAR( 255 ) DEFAULT 'Verdana, Helvetica, sans-serif' NOT NULL ," .
			"`bg_color1` VARCHAR( 32 ) DEFAULT '#ffffff' NOT NULL , `bg_color2` VARCHAR( 32 ) DEFAULT '#000000' NOT NULL , `base_color1` VARCHAR( 32 ) DEFAULT '#bbbbbb' NOT NULL , " .
			"`base_color2` VARCHAR( 32 ) DEFAULT '#dddddd' NOT NULL , `font_color1` VARCHAR( 32 ) DEFAULT '#222222' NOT NULL , " .
			"`font_color2` VARCHAR( 32 ) DEFAULT '#000000' NOT NULL , `font_color3` VARCHAR( 32 ) DEFAULT '#ffffff' NOT NULL , " .
			"`row_color1` VARCHAR( 32 ) DEFAULT '#cccccc' NOT NULL , `row_color2` VARCHAR( 32 ) DEFAULT '#bbbbbb' NOT NULL , " .
			"`row_color3` VARCHAR( 32 ) DEFAULT '#dddddd' NOT NULL , `error_color` VARCHAR( 32 ) DEFAULT '#990033' NOT NULL , " .
			"`font_size` INT( 11 ) UNSIGNED DEFAULT '10' NOT NULL , `upload_dir` VARCHAR( 255 ) NOT NULL , " .
			"`sample_rate` INT( 11 ) UNSIGNED DEFAULT '32' NOT NULL , PRIMARY KEY ( `id` ), KEY user (`user`) )";
		$db_results = mysql_query($sql, dbh());

		$sql = "INSERT INTO preferences (`user`,`font_size`) VALUES ('0','12')";
		$db_results = mysql_query($sql, dbh());

		// Now we need to give everyone some preferences
		$sql = "SELECT * FROM user";
		$db_results = mysql_query($sql, dbh());

		while ($r = mysql_fetch_object($db_results)) { 
			$users[] = $r;
		}

		foreach ($users as $user) { 
			$sql = "INSERT INTO preferences (`user`) VALUES ('$user->id')";
			$db_results = mysql_query($sql, dbh());
		}

                // Add album art columns to album table
                $sql = "ALTER TABLE album ADD art MEDIUMBLOB, ADD art_mime VARCHAR(128)";
                $db_result = mysql_query($sql, dbh());

        } // update_320001

	/*!
		@function update_320002
		@discussion update to alpha 2
	*/
	function update_320002() {

		/* Add catalog_type back in for XML-RPC */
		$sql = "ALTER TABLE `catalog` ADD `catalog_type` ENUM( 'local', 'remote' ) DEFAULT 'local' NOT NULL AFTER `path`";
		$db_results = mysql_query($sql, dbh());

		/* Add default_play to pick between stream/localplay/quicktime */
		$sql = "ALTER TABLE `preferences` ADD `default_play` VARCHAR( 128 ) DEFAULT 'stream' NOT NULL AFTER `popular_threshold`";
		$db_results = mysql_query($sql, dbh());

		/* Should be INT(11) Why not eah? */
		$sql = "ALTER TABLE `artist` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT";
		$db_results = mysql_query($sql, dbh());

		/* Add level to access_list so we can limit playback/download/xml-rpc share */
		$sql = "ALTER TABLE `access_list` ADD `level` SMALLINT( 3 ) UNSIGNED DEFAULT '5' NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Shouldn't be zero fill... not needed */
		$sql = "ALTER TABLE `user` CHANGE `offset_limit` `offset_limit` INT( 5 ) UNSIGNED DEFAULT '00050' NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Let's knock it up a notch 11.. BAM */
		$sql = "ALTER TABLE `user` CHANGE `id` `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT";
		$db_results = mysql_query($sql, dbh());

		/* Change IP --> Start */
		$sql = "ALTER TABLE `access_list` CHANGE `ip` `start` INT( 11 ) UNSIGNED NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Add End */
		$sql = "ALTER TABLE `access_list` ADD `end` INT( 11 ) UNSIGNED NOT NULL AFTER `start`";
		$db_results = mysql_query($sql, dbh());

		/* Update Version */
		$this->set_version('db_version', '320002');

	} // update_320002


	/*!
		@function update_320003
		@discussion updates to the alpha 3 of 3.2
	*/
	function update_320003() { 

		/* Add last_seen to user table */
		$sql = "ALTER TABLE `user` ADD `last_seen` INT( 11 ) UNSIGNED NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* 
		   Load the preferences table into an array 
		   so we can migrate it to the new format
		*/
		$sql = "SELECT * FROM preferences";
		$db_results = mysql_query($sql, dbh());

		$results = array();

		while ($r = mysql_fetch_object($db_results)) { 
			$results[$r->user]['download'] 		= $r->download;
			$results[$r->user]['upload']		= $r->upload;
			$results[$r->user]['downsample']	= $r->downsample;
			$results[$r->user]['local_play']	= $r->local_play;
			$results[$r->user]['multicast']		= $r->multicast;
			$results[$r->user]['quarantine']	= $r->quarantine;
			$results[$r->user]['popular_threshold'] = $r->popular_threshold;
			$results[$r->user]['default_play']	= $r->default_play;
			$results[$r->user]['font']		= $r->font;
			$results[$r->user]['bg_color1']		= $r->bg_color1;
			$results[$r->user]['bg_color2']		= $r->bg_color2;
			$results[$r->user]['base_color1']	= $r->base_color1;
			$results[$r->user]['base_color2']	= $r->base_color2;
			$results[$r->user]['font_color1']	= $r->font_color1;
			$results[$r->user]['font_color2']	= $r->font_color2;
			$results[$r->user]['font_color3']	= $r->font_color3;
			$results[$r->user]['row_color1']	= $r->row_color1;
			$results[$r->user]['row_color2']	= $r->row_color2;
			$results[$r->user]['row_color3']	= $r->row_color3;
			$results[$r->user]['error_color']	= $r->error_color;
			$results[$r->user]['font_size']		= $r->font_size;
			$results[$r->user]['upload_dir']	= $r->upload_dir;
			$results[$r->user]['sample_rate']	= $r->sample_rate;

		} // while preferences

		/* Drop the preferences table so we can start over */
		$sql = "DROP TABLE `preferences`";
		$db_results = mysql_query($sql, dbh()) or die('Query failed: ' . mysql_error());

		/* Create the new preferences table */
		$sql = "CREATE TABLE `preferences` (`key` VARCHAR( 255 ) NOT NULL , `value` VARCHAR( 255 ) NOT NULL , `user` INT( 11 ) UNSIGNED NOT NULL)";
		$db_results = mysql_query($sql, dbh());

		$sql = "ALTER TABLE `preferences` ADD INDEX ( `key` )";
		$db_results = mysql_query($sql, dbh());

		$sql = "ALTER TABLE `preferences` ADD INDEX ( `user` )";
		$db_results = mysql_query($sql, dbh());


		$user = new User();

		/* Populate the mofo! */
		foreach ($results as $key => $data) {

			$user->add_preference('download',$results[$key]['download'],$key);
			$user->add_preference('upload',$results[$key]['upload'], $key);
			$user->add_preference('downsample',$results[$key]['downsample'], $key);
			$user->add_preference('local_play', $results[$key]['local_play'], $key);
			$user->add_preference('multicast', $results[$key]['multicast'], $key);
			$user->add_preference('quarantine', $results[$key]['quarantine'], $key);
			$user->add_preference('popular_threshold',$results[$key]['popular_threshold'], $key);
			$user->add_preference('font', $results[$key]['font'], $key);
			$user->add_preference('bg_color1',$results[$key]['bg_color1'], $key);
			$user->add_preference('bg_color2',$results[$key]['bg_color2'], $key);
			$user->add_preference('base_color1',$results[$key]['base_color1'], $key);
			$user->add_preference('base_color2',$results[$key]['base_color2'], $key);
			$user->add_preference('font_color1',$results[$key]['font_color1'], $key);
			$user->add_preference('font_color2',$results[$key]['font_color2'], $key);
			$user->add_preference('font_color3',$results[$key]['font_color3'], $key);
			$user->add_preference('row_color1',$results[$key]['row_color1'], $key);
			$user->add_preference('row_color2',$results[$key]['row_color2'], $key);
			$user->add_preference('row_color3',$results[$key]['row_color3'], $key);
			$user->add_preference('error_color', $results[$key]['error_color'], $key);
			$user->add_preference('font_size', $results[$key]['font_size'], $key);
			$user->add_preference('upload_dir', $results[$key]['upload_dir'], $key);
			$user->add_preference('sample_rate', $results[$key]['sample_rate'], $key);

		} // foreach preferences 

		/* Update Version */
		$this->set_version('db_version', '320003');

	 } // update_320003

	 /*!
	 	@function update_320004
		@discussion updates to the 320004 
			version of the db
	*/
	function update_320004() { 

		$results = array();

		$sql = "SELECT * FROM preferences WHERE `key`='local_play' AND `value`='true'";
		$db_results = mysql_query($sql, dbh());

		while ($r = mysql_fetch_object($db_results)) { 
			$results[$r->user] = 'local_play';
		}

		$sql = "SELECT * FROM preferences WHERE `key`='downsample' AND `value`='true'";
		$db_results = mysql_query($sql, dbh());

		while ($r = mysql_fetch_object($db_results)) { 
			$results[$r->user] = 'downsample';
		}

		$sql = "SELECT * FROM preferences WHERE `key`='multicast' AND `value`='true'";
		$db_results = mysql_query($sql, dbh());

		while ($r = mysql_fetch_object($db_results)) { 
			$results[$r->user] = 'multicast';
		}

		$sql = "SELECT DISTINCT(user) FROM preferences";
		$db_results = mysql_query($sql, dbh());
		
		while ($r = mysql_fetch_object($db_results)) { 
			if (!isset($results[$r->user])) { 
				$results[$r->user] = 'normal';
			}
		}

		foreach ($results as $key => $value) { 
			$sql = "INSERT INTO preferences (`key`,`value`,`user`) VALUES ('play_type','$value','$key')";
			$db_results = mysql_query($sql, dbh());
		}

		$sql = "DELETE FROM preferences WHERE `key`='downsample'";
		$db_results = mysql_query($sql, dbh());
		
		$sql = "DELETE FROM preferences WHERE `key`='local_play'";
		$db_results = mysql_query($sql, dbh());
		
		$sql = "DELETE FROM preferences WHERE `key`='multicast'";
		$db_results = mysql_query($sql, dbh());

		$sql = "DROP TABLE `config`";
		$db_results = mysql_query($sql, dbh());

		/* Update Version */
		$this->set_version('db_version', '320004');

	} // update_320004

	/*!
		@function update_330000
		@discussion updates to 3.3 Build 0
	*/
	function update_330000() { 

		/* Add Type to preferences */
		$sql = "ALTER TABLE `preferences` ADD `type` VARCHAR( 128 ) NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Set Type on current preferences */
		$sql = "UPDATE `preferences` SET type='user'";
		$db_results = mysql_query($sql, dbh());

		/* Add New Preferences */
		$new_prefs[] = array('key' => 'local_length', 'value' => libglue_param('local_length'));
		$new_prefs[] = array('key' => 'site_title', 'value' => conf('site_title'));
		$new_prefs[] = array('key' => 'access_control', 'value' => conf('access_control'));
		$new_prefs[] = array('key' => 'xml_rpc', 'value' => conf('xml_rpc'));
		$new_prefs[] = array('key' => 'lock_songs', 'value' => conf('lock_songs'));
		$new_prefs[] = array('key' => 'force_http_play', 'value' => conf('force_http_play'));
		$new_prefs[] = array('key' => 'http_port', 'value' => conf('http_port'));
		$new_prefs[] = array('key' => 'do_mp3_md5', 'value' => conf('do_mp3_md5'));
		$new_prefs[] = array('key' => 'catalog_echo_count', 'value' => conf('catalog_echo_count'));
		$new_prefs[] = array('key' => 'no_symlinks', 'value' => conf('no_symlinks'));
		$new_prefs[] = array('key' => 'album_cache_limit', 'value' => conf('album_cache_limit'));
		$new_prefs[] = array('key' => 'artist_cache_limit', 'value' => conf('artist_cache_limit'));
		$new_prefs[] = array('key' => 'memory_limit', 'value' => conf('memory_limit'));
		$new_prefs[] = array('key' => 'refresh_limit', 'value' => conf('refresh_interval'));
		
		foreach ($new_prefs as $pref) { 
			$sql = "INSERT INTO `preferences` (`key`,`value`,`type`) VALUES ('".$pref['key']."','".$pref['value']."','system')";
			$db_results = mysql_query($sql, dbh());
		}
		

		/* Update Version */
		$this->set_version('db_version','330000');


	} // update_330000


	/*!
		@function update_330001
		@discussion adds year to album and tweaks
			the password field in session
	*/
	function update_330001() { 
		
		/* Add Year to Album Table */
		$sql = "ALTER TABLE `album` ADD `year` INT( 4 ) UNSIGNED NOT NULL AFTER `prefix`";
		$db_results = mysql_query($sql, dbh());

		/* Alter Password Field */
		$sql = "ALTER TABLE `user` CHANGE `password` `password` VARCHAR( 64 ) NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Update Version */
		$this->set_version('db_version', '330001');

	} // update_330001

	/*!
		@function update_330002
		@discussion changes user.access from enum to a 
			varchr field
	*/
	function update_330002() { 

		/* Alter user table */
		$sql = "ALTER TABLE `user` CHANGE `access` `access` VARCHAR( 64 ) NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Add private option to catalog */
		$sql = "ALTER TABLE `catalog` ADD `private` INT( 1 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `enabled`";
		$db_results = mysql_query($sql, dbh());

		/* Add new user_catalog table */
		$sql = "CREATE TABLE `user_catalog` ( `user` INT( 11 ) UNSIGNED NOT NULL , `catalog` INT( 11 ) UNSIGNED NOT NULL )";
		$db_results = mysql_query($sql, dbh());

		/* Update Version */
		$this->set_version('db_version', '330002');

	} // update_330002

	/*!
		@function update_330003
		@discussion adds user_preference and modifies the 
			existing preferences table
	*/
	function update_330003() { 

		/* Add new user_preference table */
		$sql = "CREATE TABLE `user_preference` ( `user` INT( 11 ) UNSIGNED NOT NULL , `preference` INT( 11 ) UNSIGNED NOT NULL, `value` VARCHAR( 255 ) NOT NULL )";
		$db_results = mysql_query($sql, dbh()); 

		/* Add indexes */ 
		$sql = "ALTER TABLE `user_preference` ADD INDEX ( `user` )";
		$db_results = mysql_query($sql, dbh());

		$sql = "ALTER TABLE `user_preference` ADD INDEX ( `preference` )";
		$db_results = mysql_query($sql, dbh());

		/* Pull and store all preference information */
		$sql = "SELECT * FROM preferences";
		$db_results = mysql_query($sql, dbh());
		
		$results = array();
		
		while ($r = mysql_fetch_object($db_results)) { 
			$results[] = $r;
		}


		/* Re-combobulate preferences table */
        
			/* Drop the preferences table so we can start over */
			$sql = "DROP TABLE `preferences`";
			$db_results = mysql_query($sql, dbh()) or die('Query failed: ' . mysql_error());

			/* Insert new preference table */
			$sql = "CREATE TABLE `preferences` ( `id` INT ( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR ( 128 ) NOT NULL, `value` VARCHAR ( 255 ) NOT NULL," . 
				" `description` VARCHAR ( 255 ) NOT NULL, `level` INT ( 11 ) UNSIGNED NOT NULL DEFAULT '100', `type` VARCHAR ( 128 ) NOT NULL, `locked` SMALLINT ( 1 ) NOT NULL Default '1'" . 
				", PRIMARY KEY ( `id` ) )"; 
			$db_results = mysql_query($sql, dbh()) or die("Query failed: " . mysql_error());

			/* Create Array of Preferences */
			$new_prefs = array();

			$new_prefs[] = array('name' => 'download', 'value' => '0', 'description' => 'Allow Downloads', 'level' => '100', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'upload', 'value' => '0', 'description' => 'Allow Uploads', 'level' => '100', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'quarantine', 'value' => '1', 'description' => 'Quarantine All Uploads', 'level' => '100', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'popular_threshold', 'value' => '10', 'description' => 'Popular Threshold', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'font', 'value' => 'Verdana, Helvetica, sans-serif', 'description' => 'Interface Font', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'bg_color1', 'value' => '#ffffff', 'description' => 'Background Color 1', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'bg_color2', 'value' => '#000000', 'description' => 'Background Color 2', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'base_color1', 'value' => '#bbbbbb', 'description' => 'Base Color 1', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'base_color2', 'value' => '#dddddd', 'description' => 'Base Color 2', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'font_color1', 'value' => '#222222', 'description' => 'Font Color 1', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'font_color2', 'value' => '#000000', 'description' => 'Font Color 2', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'font_color3', 'value' => '#ffffff', 'description' => 'Font Color 3', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'row_color1', 'value' => '#cccccc', 'description' => 'Row Color 1', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'row_color2', 'value' => '#bbbbbb', 'description' => 'Row Color 2', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'row_color3', 'value' => '#dddddd', 'description' => 'Row Color 3', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'error_color', 'value' => '#990033', 'description' => 'Error Color', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'font_size', 'value' => '10', 'description' => 'Font Size', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'upload_dir', 'value' => '/tmp', 'description' => 'Upload Directory', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'sample_rate', 'value' => '32', 'description' => 'Downsample Bitrate', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'refresh_limit', 'value' => '0', 'description' => 'Refresh Rate for Homepage', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'local_length', 'value' => '900', 'description' => 'Session Expire in Seconds', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'site_title', 'value' => 'For The Love of Music', 'description' => 'Website Title', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'lock_songs', 'value' => '0', 'description' => 'Lock Songs', 'level' => '100', 'locked' => '1', 'type' => 'system');
			$new_prefs[] = array('name' => 'force_http_play', 'value' => '1', 'description' => 'Forces Http play regardless of port', 'level' => '100', 'locked' => '1', 'type' => 'system');
			$new_prefs[] = array('name' => 'http_port', 'value' => '80', 'description' => 'Non-Standard Http Port', 'level' => '100', 'locked' => '1', 'type' => 'system');
			$new_prefs[] = array('name' => 'catalog_echo_count', 'value' => '100', 'description' => 'Catalog Echo Interval', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'no_symlinks', 'value' => '0', 'description' => 'Don\'t Follow Symlinks', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'album_cache_limit', 'value' => '25', 'description' => 'Album Cache Limit', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'artist_cache_limit', 'value' => '50', 'description' => 'Artist Cache Limit', 'level' => '100', 'locked' => '0', 'type' => 'system');
			$new_prefs[] = array('name' => 'play_type', 'value' => 'stream', 'description' => 'Type of Playback', 'level' => '25', 'locked' => '0', 'type' => 'user');
			$new_prefs[] = array('name' => 'direct_link', 'value' => '1', 'description' => 'Allow Direct Links', 'level' => '100', 'locked' => '0', 'type' => 'user');
			
			foreach ($new_prefs as $prefs) { 

				$sql = "INSERT INTO preferences (`name`,`value`,`description`,`level`,`locked`,`type`) VALUES ('" . $prefs['name'] . "','" . $prefs['value'] ."','". $prefs['description'] ."','" . $prefs['level'] ."','". $prefs['locked'] ."','" . $prefs['type'] . "')";
				$db_results = mysql_query($sql, dbh());
	
			} // foreach prefs


		/* Re-insert Data into preferences table */

		$user = new User();
		$users = array();

		foreach ($results as $old_pref) { 
			// This makes sure that true/false yes no get turned into 0/1
			$temp_array = fix_preferences(array('old' => $old_pref->value));
			$old_pref->value = $temp_array['old'];
			$user->add_preference($old_pref->key,$old_pref->value,$old_pref->user);
			$users[$old_pref->user] = 1;
		} // end foreach old preferences

		/* Fix missing preferences */
		foreach ($users as $userid => $data) { 
			$user->fix_preferences($userid);
		} // end foreach user

		/* Update Version */
                $this->set_version('db_version', '330003');

	} // update_330003

	/*! 
		@function update_330004
		@discussion changes comment from varchar to text
			and also adds a few preferences options and
			adds the per db art functions
	*/
	function update_330004() { 

		/* Change comment field in song */
		$sql = "ALTER TABLE `song` CHANGE `comment` `comment` TEXT NOT NULL";
		$db_results = mysql_query($sql, dbh());

		/* Add Extra Preferences */
		$sql = "INSERT INTO `preferences` ( `id` , `name` , `value` , `description` , `level` , `type` , `locked` ) VALUES ('', 'lang', 'en_US', 'Language', '100', 'user', '0')";
		$db_results = mysql_query($sql, dbh());

		$sql = "INSERT INTO `preferences` ( `id` , `name` , `value` , `description` , `level` , `type` , `locked` ) VALUES ('', 'playlist_type','m3u','Playlist Type','100','user','0')";
		$db_results = mysql_query($sql, dbh());

		/* Add Gathertype to Catalog for future use */
		$sql = "ALTER TABLE `catalog` ADD `gather_types` VARCHAR( 255 ) NOT NULL AFTER `sort_pattern`";
		$db_results = mysql_query($sql, dbh());

		/* Add level to user_catalog for future use */
		$sql = "ALTER TABLE `user_catalog` ADD `level` SMALLINT( 3 ) DEFAULT '25' NOT NULL AFTER `catalog`";
		$db_results = mysql_query($sql, dbh());

		/* Fix existing preferences */
		$sql = "SELECT id FROM user";
		$db_results = mysql_query($sql, dbh());

		$user = new User(0);

		while ($results = mysql_fetch_array($db_results)) { 
			$user->fix_preferences($results[0]);
		}

                /* Update Version */
                $this->set_version('db_version', '330004');
				
	} // update_330004

	/*!
		@function update_331000
		@discussion this updates is for 3.3.1 it adds 
			the theme preference.
	*/
	function update_331000() { 


		/* Add new preference */
		$sql = "INSERT INTO `preferences` (`id`,`name`,`value`,`description`,`level`,`type`,`locked`) VALUES ('','theme_name','classic','Theme','0','user','0')";
		$db_results = mysql_query($sql, dbh());

		/* Fix existing preferecnes */
		$sql = "SELECT DISTINCT(user) FROM user_preference";
		$db_results = mysql_query($sql, dbh());

		$user = new User(0);
		
		while ($results = mysql_fetch_array($db_results)) { 
			$user->fix_preferences($results[0]);
		}

		/* Update Version */
		$this->set_version('db_version','331000');

	} // update_331000

	/*!
		@function update_331001
		@discussion this adds a few more user preferences
	*/
	function update_331001() { 

		/* Add new preference */
		$sql = "INSERT INTO `preferences` (`id`,`name`,`value`,`description`,`level`,`type`,`locked`) VALUES ('','ellipse_threshold_album','27','Album Ellipse Threshold','0','user','0')";
		$db_results = mysql_query($sql, dbh());

		$sql = "INSERT INTO `preferences` (`id`,`name`,`value`,`description`,`level`,`type`,`locked`) VALUES ('','ellipse_threshold_artist','27','Artist Ellipse Threshold','0','user','0')";
		$db_results = mysql_query($sql, dbh());
		
		$sql = "INSERT INTO `preferences` (`id`,`name`,`value`,`description`,`level`,`type`,`locked`) VALUES ('','ellipse_threshold_title','27','Title Ellipse Threshold','0','user','0')";
		$db_results = mysql_query($sql, dbh());
		
                /* Fix existing preferecnes */
                $sql = "SELECT DISTINCT(user) FROM user_preference";
                $db_results = mysql_query($sql, dbh());
                
                $user = new User(0);
                
                while ($results = mysql_fetch_array($db_results)) {
                        $user->fix_preferences($results[0]);
                }       
                
                /* Update Version */
                $this->set_version('db_version','331001');	

	} // update_331001

} // end update class

?>
