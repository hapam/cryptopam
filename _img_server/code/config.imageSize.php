<?php
if(!defined("IMG_QUALITY")) define("IMG_QUALITY", 100);
if(!defined("IMG_GEN_AUTO")) define("IMG_GEN_AUTO", 1);
if(!defined("MASK_ACTIVE")) define("MASK_ACTIVE", 0);
if(!defined("MASK_POSITION")) define("MASK_POSITION", "bottomright");
if(!defined("MASK_IMG")) define("MASK_IMG", "");
if(!defined("MASK_TRANS")) define("MASK_TRANS", 30);
if(!defined("MASK_MARGIN")) define("MASK_MARGIN", 5);
if(!defined("MASK_MIN")) define("MASK_MIN", 200);

global $imageConfigSize;
$imageConfigSize = array(
	"gallery" => array(
		"folder" => "gallery/",
		"mask" => 1,
		"sizes"  => array(
			150	=> array("width" => 150, "height" => 0),
			350	=> array("width" => 350, "height" => 0),
			640	=> array("width" => 640, "height" => 0),
		)
	),
	"backup" => array(
		"folder" => "backup/",
		"mask" => 0,
		"sizes"  => array(
		)
	),
	"crypto" => array(
		"folder" => "crypto/",
		"mask" => 0,
		"sizes"  => array(
			150	=> array("width" => 150, "height" => 0),
		)
	),
);