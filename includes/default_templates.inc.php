<?php


/*******************************************************************/
/*** Variables for Maps, Images, Densities ..                    ***/
/*** Default map settings.                                       ***/
/*** Don't copy and paste in your config.php                     ***/



/*** GoogleMaps: Strike Overlays ***/
if (!isset($_BO['mapcfg'][0]))
	$_BO['mapcfg'][0] = $_BO['tpl_gmap']['0-15'];

if (!isset($_BO['mapcfg'][1]))
	$_BO['mapcfg'][1] = $_BO['tpl_gmap']['15-120'];
	
if (!isset($_BO['mapcfg'][2]))
	$_BO['mapcfg'][2] = $_BO['tpl_gmap']['2-24h'];

if (!isset($_BO['mapcfg'][-1]))
	$_BO['mapcfg'][-1] = $_BO['tpl_gmap']['manual'];

	
/*** Image Maps ***/
if (!isset($_BO['mapimg'][0]))
	$_BO['mapimg'][0] = $_BO['tpl_imgmap']['europe'];

if (!isset($_BO['mapimg'][1]))
	$_BO['mapimg'][1] = $_BO['tpl_imgmap']['germany_mini'];

if (!isset($_BO['mapimg'][2]))
	$_BO['mapimg'][2] = $_BO['tpl_imgmap']['germany_lkr'];

if (!isset($_BO['mapimg'][3]))
	$_BO['mapimg'][3] = $_BO['tpl_imgmap']['europe_mini'];

if (!isset($_BO['mapimg']['stations_mini']))
{
	if (BO_REGION == 2)
		$_BO['mapimg']['stations_mini'] = $_BO['tpl_imgmap']['oceania_mini_stations'];
	else
		$_BO['mapimg']['stations_mini'] = $_BO['tpl_imgmap']['europe_mini_stations'];
}

if (!isset($_BO['mapimg']['kml_eu2']))
	$_BO['mapimg']['kml_eu2'] = $_BO['tpl_imgmap']['europe_kml_2h'];

if (!isset($_BO['mapimg']['kml_eu24']))
	$_BO['mapimg']['kml_eu24'] = $_BO['tpl_imgmap']['europe_kml_24h'];


/*** Densities ***/
if (!isset($_BO['density'][0]))
	$_BO['density'][0] = $_BO['tpl_density']['europe'];

if (!isset($_BO['density'][1]))
	$_BO['density'][1] = $_BO['tpl_density']['germany'];


/*** Regions ***/
if (!isset($_BO['region']['germany']))
	$_BO['region']['germany'] = $_BO['tpl_region']['germany'];

	
?>