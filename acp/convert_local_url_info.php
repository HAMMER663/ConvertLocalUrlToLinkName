<?php
/**
*
* @package Convert Local Url To Link Name
* @copyright (c) 2014 hammer663
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace hammer663\ConvertLocalUrlToLinkName\acp;

class convert_local_url_info
{
	function module()
	{
		return array(
			'filename'	=> '\hammer663\ConvertLocalUrlToLinkName\acp\convert_local_url_module',
			'title'		=> 'ACP_CONVERT_LOCAL_URL',
			'version'	=> '0.3.0',
			'modes'		=> array(
				'config_clutln'		=> array('title' => 'ACP_CONVERT_LOCAL_URL_EXPLAIN', 'auth' => 'ext_hammer663/ConvertLocalUrlToLinkName && acl_a_clutln', 'cat' => array('ACP_CONVERT_LOCAL_URL_EXPLAIN')),
			),
		);
	}
}
