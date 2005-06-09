<?php
/*

 Copyright (c) 2004 Ampache.org
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
/*

    @header Album Art
This pulls album art out of the file using the getid3 library
and dumps it to the browser as an image mime type.

*/

require('modules/init.php');


$album = new Album($_REQUEST['id']);

// Check db first
$r = $album->get_art($_REQUEST['fast']);

if (isset($r->art)) {
    $art = $r->art;
    $mime = $r->art_mime;
    $found = 1;
}

if (!$found) {
    // Print a transparent gif instead
//    header('Content-type: image/jpg');
//    readfile(conf('prefix') . "/docs/images/blankalbum.jpg");
	header('Content-type: image/gif');
	readfile(conf('prefix') . conf('theme_path') . "/images/blankalbum.gif");
}
else {
    // Print the album art
    $extension = substr($mime,strlen($mime)-3,3);
    header("Content-type: $mime");
    header("Content-Disposition: filename=" . $album->name . "." . $extension);
    echo $art;
}

?>
