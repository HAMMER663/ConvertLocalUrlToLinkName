<?php
/**
*
* @package Convert Local Url To Link Name
* @copyright (c) 2014 HAMMER663
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace hammer663\ConvertLocalUrlToLinkName\migrations\v0xx;


namespace hammer663\ConvertLocalUrlToLinkName\migrations\v0xx;

class v_0_2_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['clutln_version']) && version_compare($this->config['clutln_version'], '0.2.0', '>=');
	}

	static public function depends_on()
	{
		return array('\hammer663\ConvertLocalUrlToLinkName\migrations\v0xx\v_0_1_0');
	}

	public function update_data()
	{
		return array(
			// Update exisiting configs
			array('config.update', array('clutln_version', '0.2.0')),
			array('config.add', array('clutln_allow_youtube', '0')),
		);
	}
}
