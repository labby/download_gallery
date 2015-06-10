<?php

/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2006, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// prevent this file from being accessed directly
if (!defined('LEPTON_PATH')) die(header('Location: index.php'));
include_once(LEPTON_PATH.'/modules/download_gallery/functions.php');

// STEP 0:	initialize some variables
$page_id = (int) $page_id;
$section_id = (int) $section_id;

$header = '';
$footer = '';
$file_header = '';
$files_loop = '';
$file_footer = '';
$gloop = '';
$search_layout = '';
$gheader = '';
$gfooter = '';
init_fields($header, $footer, $file_header, $files_loop, $file_footer, $gloop, $search_layout, $gheader, $gfooter);
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_settings (section_id,page_id,header,files_loop,footer,file_header,file_footer,gloop,search_layout,gheader,gfooter)
                 VALUES ('$section_id','$page_id','$header','$files_loop','$footer','$file_header','$file_footer','$gloop','$search_layout','$gheader','$gfooter')");

$image_array	= "jpg,jpeg,jpe,jfif,gif,bmp,dib,png,tif,tiff,wmf,emf,psp";
$movie_array	= "mov,wma";
$music_array	= "mp3,mid,rmi,midi,wav,snd,au,aif,aiff,ra,ram,rm,ogg";
$docs_array	= "doc,dot";
$pres_array	= "ppa,pps,ppt";
$excel_array	= "xla,xlb,xlc,xld,xlk,xll,xlm,xls,xlt,xlv,xlw,xlxml";
$compr_array	= "arj,cab,lzh,tar,tz,zip";
$pdf_array	= "pdf";
$txt_array	= "txt,bat,ini,log";

$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','images','image.gif','$image_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','movies','movie.gif','$movie_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','music','music.gif','$music_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','documents','document.gif','$docs_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','presentations','presentation.gif','$pres_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','spreadsheets','spreadsheet.gif','$excel_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','compressions','compression.gif','$compr_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','pdf','pdf.gif','$pdf_array')");
$database->query("INSERT INTO ".TABLE_PREFIX."mod_download_gallery_file_ext (section_id,page_id,file_type,file_image,extensions) VALUES ('$section_id','$page_id','text','text.gif','$txt_array')");

?>