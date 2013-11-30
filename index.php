<?php
/*
 * This is a sample script for using the Battlefield4 class
 * 
 * PHP Version 5.4
 * 
 * 
 * @author: Gregor Ganglberger <gg@grexaut.net>
 * @license: Creative Commons http://creativecommons.org/licenses/by/3.0/
 * @link: http://github.com/GrexAut
 */


require_once 'lib/battlefield4.class.php';

error_reporting(E_ALL);
ini_set("display_errors","1");


/*
 * Settings
 * ServerIP: The address or domain of the server
 * Port: The RCON Port (not the port the gameserver)
 */


//$serverip = "85.114.154.144";
//$port = "10501";
$serverip = "85.114.152.41";
$port = "10801";



/*
 * initialize Battlefield4 class
 */
$bf4 = new Battlefield4();

/*
 * Get connection to the Server
 */
$bf4->connectToServer($serverip,$port);

/*
 * Login to Battlefield 4 Server
 */
$bf4->login("example_rcon");

/*
 * Commands
 */


/*
 *  Show Serverinfo (login not required)
 */
$serverInfo = $bf4->getServerInfo();
echo "<h2>ServerInfo</h2>";
echo "<pre>", print_r($serverInfo), "</pre>";
echo "<hr />";

/*
 * Show all players from server (login not required)
 */
$players = $bf4->getAllPlayers();
echo "<h2>PlayerInfos</h2>";
echo "<pre>", print_r($players), "</pre>";
echo "<hr />";

/*
 * Show currentLevel (login required, but u can use getServerinfo() for showing the currrentLevel (stored in array map)
 */
$currentLevel = $bf4->getCurrentLevel();
echo "<pre>", print_r($currentLevel), "</pre>";

/*
 * Moving a soldier
 */
//$bf4->movePlayer("GrexAut",1,0,true); // not working

/*
 * Show a large on-screen message for a group of players
 */
//$bf4->adminYell("Test",60,"GrexAut"); // not working

/*
 * Send a chat message to a group of players
 */
//$bf4->adminSay("Test","GrexAut"); // not working

/*
 * killing a soldier
 */
//$bf4->killPlayer("GrexAut");

/*
 *  kicking a soldier from server
 */
//$bf4->kickPlayer("GrexAut","Test");






/*
 * At the end of file we don't need the connection anymore, closing...
 */
$bf4->logout();
$bf4->disconnecFromServer();
?>

