<?php
/**
*
* Convert Local Url To Link Name [Russian]
*
* @package Convert Local Url To Link Name
* @copyright (c) 2015 hammer663
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
	'BOARD_RULE_NUMBER'			=> '[b]Пункт правил %s[/b]',
	'FAQ_NUMBER'				=> '[b]FAQ пункт %s.%s[/b]',
	'POST_BY'					=> 'Пост %s #%s',

));
