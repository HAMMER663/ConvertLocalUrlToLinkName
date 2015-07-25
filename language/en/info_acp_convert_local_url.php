<?php
/**
*
* Convert Local Url To Link Name [English]
*
* @package Convert Local Url To Link Name
* @copyright (c) 2014 hammer663
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
	'ACP_CONVERT_LOCAL_URL'					=> 'Convert Local Url To Link Name',
	'ACP_CONVERT_LOCAL_URL_EXPLAIN'			=> 'URL settings',

	'ACP_CLUTLN_ENABLE'						=> 'Enable extension Convert Local Url To Link Name',
	'ACP_CLUTLN_ENABLE_EXPLAIN'				=> 'Transform of a internal url to link name before posting',

	'ACP_CLUTLN_ENABLE_BOARDRULES'			=> 'Enable transform Board Rules URL',
	'ACP_CLUTLN_ENABLE_BOARDRULES_EXPLAIN'	=> 'Transform of a Board Rules Url to link name before posting',

	'ACP_CLUTLN_ENABLE_WIKIURL'				=> 'Enable transform  Wiki URL',
	'ACP_CLUTLN_ENABLE_WIKIURL_EXPLAIN'		=> 'Transform of a wiki-url to link name before posting',

	'ACP_CLUTLN_ENABLE_FAQURL'				=> 'Enable transform FAQ URL',
	'ACP_CLUTLN_ENABLE_FAQURL_EXPLAIN'		=> 'Transform of a FAQ Url to link name before posting',

	'ACP_CLUTLN_ENABLE_YOUTUBEURL'			=> 'Enable transform Youtube URL',
	'ACP_CLUTLN_ENABLE_YOUTUBEURL_EXPLAIN'	=> 'Transform of a Youtube-url to bbCode. You must create a BB-code [youtube][/youtube]',

	'ACP_CLUTLN_ENABLE_SEO'					=> 'Enable SEO URL',
	'ACP_CLUTLN_ENABLE_SEO_EXPLAIN'			=> 'Transform of a ext phpBB SEO Ultimate SEO URL by dcz',
	'ACP_CLUTLN_OFF_SEO'					=> 'Disable transform SEO URL',
	'ACP_CLUTLN_SEO_SIMPLE'					=> 'URL SEO SIMPLE',
	'ACP_CLUTLN_SEO_ADVANCED'				=> 'URL SEO ADVANCED',

));
