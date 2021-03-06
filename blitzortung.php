<?php
/*
    MyBlitzortung - a tool for participants of blitzortung.org
	to display lightning data on their web sites.

	Copyright 2011-2012 by Tobias Volgnandt & Blitzortung.org Participants



    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined("BO_VER"))
{
	define("BO_DIR", dirname(__FILE__).'/');
	define("BO_VER", '0.7.7-dev');

	define("BO_PERM_ADMIN", 		1);
	define("BO_PERM_SETTINGS", 		2);
	define("BO_PERM_NOLIMIT", 		4);
	define("BO_PERM_ALERT", 		8);
	define("BO_PERM_ALERT_ALL",		16);
	define("BO_PERM_ALERT_SMS",		32);
	define("BO_PERM_ALERT_URL",		64);
	define("BO_PERM_ARCHIVE", 		128);
	define("BO_PERM_COUNT",	        8);



	//Some default PHP-Options
	ini_set('magic_quotes_runtime', 0);

	//Config var.
	global $_BO, $_BL;
	$_BO = array();
	$_BL = array();

	if (!file_exists(BO_DIR.'config.php'))
		die('Missing config.php! Please run installation first!');

	//Load Config
	require_once 'includes/templates_gmap.inc.php';
	require_once 'includes/templates.inc.php';
	require_once 'config.php';
	require_once 'includes/default_settings.inc.php'; //after config.php!
	require_once 'includes/default_templates.inc.php'; //after config.php!

	//includes #1
	require_once 'includes/functions.inc.php';
	require_once 'includes/data.inc.php';
	require_once 'includes/user.inc.php';
	require_once 'includes/tiles.inc.php';

	//Classes
	require_once 'includes/classes/Db.class.php';
	require_once 'includes/classes/DateTime.class.php';

	
	//Debug Mode?
	if (BO_DEBUG === true)
	{
		error_reporting(E_ALL & ~E_NOTICE);
		ini_set('display_errors', 1);
	}
	elseif (BO_DEBUG === "file")
	{
		error_reporting(E_ALL & ~E_NOTICE);
		ini_set('display_errors', 0);
		set_error_handler("bo_error_handler");
	}
	else
	{
		ini_set('display_errors', 0);
	}


	//timezone
	date_default_timezone_set(BO_TIMEZONE);
	
	//User init (session, cookie, etc...)
	bo_user_init();


	//creating tiles should be very fast, other include files not needed
	if (isset($_GET['tile']))
	{
		if (defined('BO_MAP_DISABLE') && BO_MAP_DISABLE && !(bo_user_get_level() & BO_PERM_NOLIMIT))
			exit('Google Maps disabled');

		if (isset($_GET['tracks']))
			bo_tile_tracks();
		else
			bo_tile();

		exit;
	}
	//phpinfo for admin
	else if ((BO_PERM_ADMIN & bo_user_get_level()) && $_GET['bo_action'] == 'phpinfo')
	{
		phpinfo();
		exit;
	}

	// includes #2
	require_once 'includes/statistics.inc.php';
	require_once 'includes/import.inc.php';
	require_once 'includes/graphs.inc.php';
	require_once 'includes/map.inc.php';
	require_once 'includes/archive.inc.php';
	require_once 'includes/info.inc.php';
	require_once 'includes/alert.inc.php';
	require_once 'includes/image.inc.php';
	require_once 'includes/density.inc.php';

	//Save info wether headers where sent
	$_BO['headers_sent'] = headers_sent();

	//Update with new data from blitzortung.org
	$do_update = false;
	$force_update = false;
	if (isset($_GET['update']))
	{
		if (defined('BO_UPDATE_SECRET') && BO_UPDATE_SECRET && $_GET['secret'] !== BO_UPDATE_SECRET)
			exit('Wrong secret: "<b>'.htmlentities($_GET['secret']).'</b>"  Look in your config.php for "<b>BO_UPDATE_SECRET</b>"');

		$do_update = true;
		$force_update = isset($_GET['force']);

		header("Content-Type: text/plain");
	}
	else if (isset($argv))
	{
		foreach ($argv as $a)
		{
			if ($a == 'update')
				$do_update = true;
			elseif ($a == 'force')
				$force_update = true;
		}
	}

	//load locale after tiles
	bo_load_locale();

	//decisions what to do begins...
	if ($do_update)
	{
		bo_update_all($force_update, strtolower($_GET['only']));
		exit;
	}
	else if (isset($_POST['bo_do_login']))
	{
		//Login
		$login_name   = BoDb::esc(bo_gpc_prepare($_POST['bo_user']));
		$login_pass   = BoDb::esc(bo_gpc_prepare($_POST['bo_pass']));
		$login_cookie = $_POST['bo_login_cookie'] ? true : false;

		if (!bo_user_do_login($login_name, $login_pass, $login_cookie))
			$_BO['login_fail'] = true;
	}
	else if (isset($_GET['bo_logout']))
	{
		//Logout
		bo_user_do_logout();
	}
	else if (isset($_GET['bo_login']) && (!defined('BO_LOGIN_URL') || !BO_LOGIN_URL))
	{
		//login-screen: workaround when no special login-url is specified
		bo_show_login();
		exit;
	}
	else if (!headers_sent())
	{
		if (isset($_GET['bo_icon']))
		{
			bo_icon($_GET['bo_icon']);
			exit;
		}
		else if (isset($_GET['bo_graph'])) {
			bo_graph_raw();
			exit;
		}
		else if (isset($_GET['image']))
		{
			bo_get_image($_GET['image']);
			exit;
		}
		else if (isset($_GET['graph_statistics']))
		{
			bo_graph_statistics();
			exit;
		}
		else if (isset($_GET['density']))
		{
			bo_get_density_image();
			exit;
		}
		elseif (isset($_GET['map']))
		{
			bo_get_map_image();
			exit;
		}
		elseif (isset($_GET['animation']))
		{
			bo_get_map_image_ani();
			exit;
		}
	}

	//Order maps
	if (defined('BO_MAPS_ORDER') && strlen(BO_MAPS_ORDER))
	{
		$order = explode(',',BO_MAPS_ORDER);
		$tmp = array();
		ksort($_BO['mapimg']);

		foreach($order as $id)
		{
			$tmp[$id] = $_BO['mapimg'][$id];
		}

		foreach($_BO['mapimg'] as $id => $data)
		{
			if (!isset($tmp[$id]))
				$tmp[$id] = $_BO['mapimg'][$id];
		}

		$_BO['mapimg'] = $tmp;
	}

	if (isset($_GET['kml']))
	{
		bo_output_kml();
		exit;
	}


}

?>