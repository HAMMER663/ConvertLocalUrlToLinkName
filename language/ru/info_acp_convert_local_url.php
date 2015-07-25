<?php
/**
*
* Convert Local Url To Link Name [Russian]
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
	'ACP_CONVERT_LOCAL_URL'					=> 'Преобразование URL в читабельный вид ',
	'ACP_CONVERT_LOCAL_URL_EXPLAIN'			=> 'Настройки URL',

	'ACP_CLUTLN_ENABLE'						=> 'Включить преобразование URL',
	'ACP_CLUTLN_ENABLE_EXPLAIN'				=> 'Разрешить преобразование URL в читабельные ссылки',

	'ACP_CLUTLN_ENABLE_BOARDRULES'			=> 'Преобразовывать URL на правила форума',
	'ACP_CLUTLN_ENABLE_BOARDRULES_EXPLAIN'	=> 'Разрешить преобразовывать URL на правила форума',

	'ACP_CLUTLN_ENABLE_WIKIURL'				=> 'Преобразовывать Wiki-Url',
	'ACP_CLUTLN_ENABLE_WIKIURL_EXPLAIN'		=> 'Разрешить преобразование URL на ресурсы Wiki',

	'ACP_CLUTLN_ENABLE_FAQURL'				=> 'Преобразовывать URL на FAQ',
	'ACP_CLUTLN_ENABLE_FAQURL_EXPLAIN'		=> 'Разрешить преобразование URL на раздел FAQ',

	'ACP_CLUTLN_ENABLE_YOUTUBEURL'				=> 'Преобразовывать URL на YOUTUBE',
	'ACP_CLUTLN_ENABLE_YOUTUBEURL_EXPLAIN'		=> 'Разрешить преобразование URL на сервис YOUTUBE. Необходимо создать ВВ-код [youtube][/youtube]',

	'ACP_CLUTLN_ENABLE_SEO'					=> 'Преобразовывать URL SEO',
	'ACP_CLUTLN_ENABLE_SEO_EXPLAIN'			=> 'Разрешить преобразование URL на seo',
	'ACP_CLUTLN_OFF_SEO'					=> 'Отключить URL SEO',
	'ACP_CLUTLN_SEO_SIMPLE'					=> 'URL SEO SIMPLE',
	'ACP_CLUTLN_SEO_ADVANCED'				=> 'URL SEO ADVANCED',

));
