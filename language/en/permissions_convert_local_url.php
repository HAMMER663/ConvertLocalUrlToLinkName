<?php
/**
*
* Convert Local Url To Link Name [English]
*
* @package Convert Local Url To Link Name
* @copyright (c) 2014 hammer663
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_CONVERT_LOCAL_URL'					=> 'Convert Local Url To Link Name',
	'ACP_CONVERT_LOCAL_URL_EXPLAIN'			=> 'URL settings',
	'ACL_A_CLUTLN'							=> 'Can change settings  Convert Local Url To Link Name',
));
