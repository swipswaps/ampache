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


/*!
	@header Update Document	
	This document handles updating from one version of Maintain to the next
	if this doc is readable you can't login. stop.update and gone.fishing 
	must also be in place for this doc to work.

*/

/* Start House Keeping */

	// We need this stuff
	$no_session = 1;
	require("modules/init.php");

	// Make a blank update object
	$update = new Update(0);

	// Get the version and format it
	$version = $update->get_version(); 
	
	$conf['script'] = 'update.php';	
	$prefs['font_size'] = 12;
	$prefs['bg_color1'] = "#c0c0c0";
	$prefs['font'] = "Verdana";
	conf($prefs,1);
	

/* End House Keeping */

if ($_REQUEST['action'] == 'update') { 
	
	/* Run the Update Mojo Here */
	$update->run_update();

	/* Get the New Version */
	$version = $update->get_version();

} 
?>
<html>
<head>
<title>Ampache - Update</title>
</head>
<body>
<?php  show_template('style'); ?>
<p class="header1">Ampache - Update</p>
<p>
This page handles all database updates to Ampache starting with 3.2. According to your database your current version is: <?php echo  $update->format_version($version); ; ?>. 
the following updates need to be performed<br /><br />
<?php  $update->display_update(); ?>
</p>
<p>
<form method="post" enctype="multipart/form-data" action="<?php echo  conf('web_path'); ; ?>/update.php?action=update">
<?php  if ($update->need_update()) { ?><input type="submit" value="Update Now!" /> <?php  } ?>
</form>
</body>
</html>


