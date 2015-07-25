<?php
/**
*
* Convert Local Url To Link Name [English]
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
	'BOARD_RULE_NUMBER'			=> '[b]Board rule %s[/b]',
	'FAQ_NUMBER'				=> '[b]FAQ num. %s.%s[/b]',
	'POST_BY'					=> 'Post by %s #%s',
));
