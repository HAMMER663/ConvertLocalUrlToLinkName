<?php
/**
*
* @package Convert Local Url To Link Name
* @copyright (c) 2015 HAMMER663
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace hammer663\ConvertLocalUrlToLinkName\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $phpbb_root_path;
	protected $php_ext;

	/* @var \phpbbseo\usu\core */
	protected $core;

	/**
	* Constructor
	*/

	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext, \phpbbseo\usu\core $core = null)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->core = $core;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.message_parser_check_message'	=>	'convert_local_url_to_link_name',
			'core.permissions'					=>	'add_permission',
		);
	}

	/**
	* Convert Local Url To Link Name
	*
	* @return null
	* @access public
	*/
	public function convert_local_url_to_link_name($event)
	{
		$this->user->add_lang_ext('hammer663/ConvertLocalUrlToLinkName', 'convert_local_url');

		$allow_bbcode = $event['allow_bbcode'];
		$allow_magic_url = $event['allow_magic_url'];

		$allow_enable = $this->config['clutln_enable'];
		$allow_rules = $this->config['clutln_allow_rules'];
		$allow_wiki = $this->config['clutln_allow_wiki'];
		$allow_faq = $this->config['clutln_allow_faq'];
		$allow_youtube = $this->config['clutln_allow_youtube'];
		$allow_allow_seo = $this->config['clutln_allow_seo'];

		$message = $event['message'];

		if ($allow_bbcode && $allow_magic_url && $allow_enable)
		{
			$board_url = generate_board_url();
			$firstchar = '(^|[\n\t (>.])';
			$lastchar = '([\w\#$%&~\-;:=,?@+]*)';
			$viewtopic_url = str_replace('\://', '\://(www\.)*', $firstchar . '(' . preg_quote($board_url . '/viewtopic.' . $this->php_ext . '?') . ')');
			$viewforum_url = str_replace('\://', '\://(www\.)*', $firstchar . '(' . preg_quote($board_url . '/viewforum.' . $this->php_ext . '?') . ')');
			$viewprofile_url = str_replace('\://', '\://(www\.)*', $firstchar . '(' . preg_quote($board_url . '/memberlist.' . $this->php_ext . '?') . 'mode=viewprofile&amp;)');
			$forums_auth = array();
			$matches0 = array();
//			preg_match_all('#' . $viewtopic_url . '(f)=([0-9]+)&amp;(t|p)=([0-9]+)' . $lastchar . '#si', $message, $matches0[]);
//			preg_match_all('#' . $viewtopic_url . '(p)=([0-9]+)' . $lastchar . '#si', ' ' . $message, $matches0[]);
			preg_match_all('#' . $viewtopic_url . '(f|t)=([0-9]+)&amp;(t|p)=([0-9]+)' . $lastchar . '#si', $message, $matches0[]);
			preg_match_all('#' . $viewtopic_url . '(t|p)=([0-9]+)' . $lastchar . '#si', ' ' . $message, $matches0[]);			
			preg_match_all('#' . $viewforum_url . '(f)=([0-9]+)' . $lastchar . '#si', ' ' . $message, $matches0[]);
			preg_match_all('#' . $viewprofile_url . '(u)=([0-9]+)' . $lastchar . '#si', ' ' . $message, $matches0[]);

			$last_char_patt = '/([\),]*)$/';
			foreach ($matches0 as $matches)
			{
				foreach ($matches[0] as $k => $str)
				{
					if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@is', $message))
					{
						continue;
					}
					$topic_title = '';
					$topic_post_id = (int) $matches[4+1][$k];
					$type_url = $matches[3+1][$k];
					switch ($type_url)
					{
						case 'f':
							switch ($matches[5+1][$k])
							{
								case 'p':
									$topic_post_id = (int) $matches[6+1][$k];
									$type_url = 'p';
								break;
								case 't':
									$topic_post_id = (int) $matches[6+1][$k];
									$sql = 'SELECT topic_title, topic_type, forum_id
										FROM ' . TOPICS_TABLE . ' t
										WHERE t.topic_id = ' . $topic_post_id;

								break;
								default:
									$sql = 'SELECT forum_name as topic_title, forum_id
										FROM ' . FORUMS_TABLE . '
										WHERE forum_id = ' . $topic_post_id;
								break;
							}
						break;
						// Canonical URL
						case 't':
							switch ($matches[5+1][$k])
							{
								case 'p':
									$topic_post_id = (int) $matches[6+1][$k];
									$type_url = 'p';
								break;
								default:
									$topic_post_id = (int) $matches[4+1][$k];
									$sql = 'SELECT topic_title, topic_type, forum_id
										FROM ' . TOPICS_TABLE . '
										WHERE topic_id = ' . $topic_post_id;
								break;
							}
						break;
						case 'u':
							$sql = 'SELECT username
								FROM ' . USERS_TABLE . '
								WHERE user_id = ' . $topic_post_id;
						break;
					}
					if ($type_url == 'p')
					{
						$sql = 'SELECT t.topic_title, t.topic_type, t.forum_id, p.post_subject, p.post_id, u.username
							FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
							LEFT JOIN ' . USERS_TABLE . ' u on u.user_id = p.poster_id
							WHERE p.post_id = ' . $topic_post_id . '
								AND p.topic_id = t.topic_id';
							//	echo $sql;
					}

					if (!empty($topic_post_id) && ($result = $this->db->sql_query($sql)))
					{
						$row = $this->db->sql_fetchrow($result);
						switch ($type_url)
						{
							case 'u':
								$topic_title = $row['username'];
							break;
							case 'f':
							case 't':
							case 'p':
								if (!isset($forums_auth[$row['forum_id']]))
								{
									$forums_auth[$row['forum_id']] = $this->auth->acl_gets('f_list', 'f_read', $row['forum_id']);
								}
								if ($forums_auth[$row['forum_id']] || $row['topic_type'] == POST_GLOBAL)
								{
									$topic_title = (!empty($row['post_subject'])) ? $row['post_subject'] : $row['topic_title'];
								}
							break;
						}
						$this->db->sql_freeresult($result);
					}

					if (!empty($topic_title))
					{
						$internal_url = substr($str, strlen($matches[1][$k]));
						$internal_url = preg_replace('/f=[0-9](&amp;)/', '', $internal_url); // Canonical - destroy f=*
						$internal_url = preg_replace('/(&amp;)sid=[0-9a-f]{32}/', '', $internal_url);
						if (preg_match($last_char_patt, $str, $math))
						{
							$internal_url = preg_replace($last_char_patt, '', $internal_url);
						}
						$topic_title = ($type_url == 'p') ? $topic_title . ' (' . $this->user->lang('POST_BY', $row['username'], $row['post_id']) . ')' : $topic_title;
						$message = str_replace($str, $matches[1][$k] . '[url=' . trim($internal_url) . ']' . $topic_title . '[/url]' . ((isset($math[1])) ? $math[1] : ''), ' ' . $message);
					}
				}
			}

			// Board Rules Url to Link name
			if ($allow_rules)
			{
				$rules_url = generate_board_url() . '/rules';
				$firstchar = '(^|[\n\t (>.])';
				$lastchar = '(\?)?(sid=[0-9a-f]{32})*(\#rule|\#cat)([\d\.]+)([,\)])*';
				$rules_url = str_replace('\://', '\://(www\.)*', $firstchar . '(' . preg_quote($rules_url) . ')');
				$matches = array();
				preg_match_all('#' . $rules_url . $lastchar . '#si', $message, $matches);
				foreach ($matches[0] as $k => $str)
				{
					if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@i', $message))
					{
						continue;
					}
					$rule_num = $matches[7][$k];
					$rule_url = $matches[2][$k] .$matches[4][$k] . $matches[6][$k] . $rule_num;
					$rule_url_name = sprintf($this->user->lang['BOARD_RULE_NUMBER'], $rule_num);
					$message = str_replace($str, $matches[1][$k] . '[url=' . trim($rule_url) . ']' . $rule_url_name . '[/url]' . $matches[8][$k], ' ' . $message);
				}
			}

			// Wiki-Url to Link Name
			if ($allow_wiki)
			{
				// pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
				$message = str_replace(array('[code]', '[/code]'), array('[code] ', ' [/code]'), ' ' . $message);
				$wiki_urls = array(
						'(^|[\n\t (>.])(http[s]?\:\/\/(wiki)\.phpbb\.com', 		//$phpbbwiki_url
						'(^|[\n\t (>.])(http[s]?\:\/\/(wiki)\.phpbbguru\.net', 	//$phpbbwiki_url
						'(^|[\n\t (>.])(http[s]?\:\/\/(\S+)\.wikipedia\.org', 	//$wikipedia_url
						'(^|[\n\t (>.])(http[s]?\:\/\/(\S+)\.wikiquote\.org',	//$wikiquote_url
						'(^|[\n\t (>.])(http[s]?\:\/\/(\S+)\.wikibooks\.org',	//$wikibooks_url
						'(^|[\n\t (>.])(http[s]?\:\/\/(www\.)*sportswiki\.ru',	//$sportswiki
						'(^|[\n\t (>.])(http[s]?\:\/\/(www\.)*lurkmore\.ru',	//lurkmore
						'(^|[\n\t (>.])(http[s]?\:\/\/(www\.)*lurkmore\.to',	//$lurkmore
						'(^|[\n\t (>.])(http[s]?\:\/\/(www\.)*lurkmo\.re',	//$lurkmore
						);

				$last_char_patt = '/([\),]*)$/';
				$matches = array();
				foreach ($wiki_urls as $url_pattern)
				{
					preg_match_all('#' . $url_pattern . '(\S*)\/)([\S$+]*)#si', $message, $matches);
					foreach ($matches[0] as $k => $str)
					{
						if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@is', $message))
						{
							continue;
						}
						$topic_title = $matches[5][$k];
						if (!empty($topic_title))
						{
							if (preg_match('/\%([A-Z0-9]{2})/i', $topic_title))
							{
								$topic_title = rawurldecode($topic_title);
								if (empty($topic_title))
								{
									$topic_title = urldecode($matches[5][$k]);
								}
								if (empty($topic_title))
								{
									$topic_title = $matches[5][$k];
								}
							}
							$wiki_url = substr($str, strlen($matches[1][$k]));
							if (preg_match($last_char_patt, $str, $math))
							{
								if (strpos($wiki_url, '(') === false && strpos($wiki_url, ')') === false)
								{
									$wiki_url = preg_replace($last_char_patt, '', $wiki_url);
									$topic_title = preg_replace($last_char_patt, '', $topic_title);
								}
								else
								{
									$pos = strpos($math[1], ')');
									if ($pos !== false)
									{
										$math[1] = substr_replace($math[1], '', $pos, 1);
									}
								}
							}
							$wiki_url = str_replace($topic_title, rawurlencode($topic_title), $wiki_url);
							$wiki_url = str_replace(array('%3A', '%2C', '%23'), array(':', ',', '#'), $wiki_url);
							$topic_title = str_replace('_', ' ', $topic_title);
							if ($tok = strtok($topic_title, '#'))
							{
								$topic_title = $tok;
								if ($sec_tok = strtok('#'))
								{
									if (preg_match('/\.([A-Z0-9]{2})/i', $sec_tok))
									{
										$sec_tok = str_replace('.', '%', $sec_tok);
									}
									$topic_title .= ': ' . urldecode($sec_tok);
								}
							}
							$message = substr_replace($message, $matches[1][$k] . '[url=' . trim($wiki_url) . ']' . $topic_title . '[/url]' . ((isset($math[1])) ? $math[1] : ''), strpos($message, $str), strlen($str));
						}
					}
				}
				// Remove our padding from the string..
				$message = substr(str_replace(array('[code] ', ' [/code]'), array('[code]', '[/code]'), $message), 1);
			}

			//FAQ Url to Link name
			if ($allow_faq)
			{
				$faqs_url = generate_board_url() . '/faq.' . $this->php_ext;
				$firstchar = '(^|[\n\t (>.])';
				$lastchar = '(sid=[0-9a-f]{32})*((\#f([\d]+))+(r([\d]+))*)([,\)])*';
				$faqs_url = str_replace('\://', '\://(www\.)*', $firstchar . '(' . preg_quote($faqs_url) . ')');
				$matches = array();
				preg_match_all('#' . $faqs_url . $lastchar . '#si', $message, $matches);
				foreach ($matches[0] as $k => $str)
				{
					if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@is', $message))
					{
						continue;
					}
					$faq_url = $matches[1][$k] . $matches[2][$k] . $matches[5][$k];
					$faq_url_name = sprintf($this->user->lang['FAQ_NUMBER'], $matches[7][$k], $matches[9][$k]);
					$message = str_replace($str, $matches[1][$k] . '[url=' . trim($faq_url) . ']' . $faq_url_name . '[/url]' . $matches[10][$k], ' ' . $message);
				}
			}

			// YouTube link
			if ($allow_youtube)
			{
				$matches0 = array();
				preg_match_all('#(^|[\n\t (>.])(http[s]*://(?:www\.)?youtube\.com)/(v)/([0-9a-z\-\_\+]+)([\S$+]*)#i', $message, $matches0[]);
				preg_match_all('#(^|[\n\t (>.])(http[s]*://(?:www\.)?youtube\.com/watch\?)(v)=([0-9a-z\-\_\+]+)([\S$+]*)#i', $message, $matches0[]);
				preg_match_all('#(^|[\n\t (>.])(http[s]*://(?:www\.)?youtube\.com/watch\?)([feature=].*?)&amp;(v)=([0-9a-z\-\_\+]+)([\S$+]*)#i', $message, $matches0[]);
				preg_match_all('#(^|[\n\t (>.])(http[s]*://(youtu\.be)/)([0-9a-z\-\_\+]+)([\S$+]*)#i', $message, $matches0[]);
				foreach ($matches0 as $matches)
				{
					foreach ($matches[0] as $k => $str)
					{
						if (preg_match('@(\[code(?:=([a-z]+))?\])' . preg_quote($str) . '(\[\/code\])@is', $message))
						{
							continue;
						}
						for ($i = 1, $sizeofm = sizeof($matches); $i < $sizeofm; ++$i)
						{
							if ($matches[$i][$k] === 'v' || $matches[$i][$k] === 'youtu.be')
							{
								$video_id = $matches[$i + 1][$k];
								$laststr = $matches[sizeof($matches) - 1][$k];
								if (preg_match('/([&feature=].*?)/i', $laststr))
								{
									$laststr = '';
								}
								$message = str_replace($str, $matches[1][$k] . '[youtube]' . $video_id . '[/youtube]' . $laststr, $message);
								break;
							}
						}
					}
				}
			}

			// phpBB SEO Ultimate SEO URL (Simple mode)
			if ($allow_allow_seo == 1)
			{
				$last_char_patt = '/([\),]*)$/';
			//	$parse_url  = '(^|[\n\t (>.])' . preg_quote(generate_board_url()) . '/(\w*\/)*(topic|post|forum|member|\-u)([0-9]+)([\w\-+]*)\.html';
				$seo_ext = '(' . preg_quote($this->core->seo_ext['forum']) . '|' . preg_quote($this->core->seo_ext['topic']) . '|' . preg_quote($this->core->seo_ext['post']) . '|' . preg_quote($this->core->seo_ext['user']) . ')';
				$parse_url  = '(^|[\n\t (>.])' . preg_quote(generate_board_url()) . '/(\S*)(topic|post|forum|member|\-u)([0-9]+)(-([0-9]+))?' . $seo_ext;   //([\w\-+]+)

				$parse_url = str_replace('\://', '\://(www\.)*', $parse_url);
				$matches = array();
				preg_match_all('#' . $parse_url . '([\w\#$%&~\-;:=,?@\[\]+]*)#si', ' ' . $message, $matches);
				$forums_auth = array();
				foreach ($matches[0] as $k => $str)
				{
					if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@is', $message))
					{
						continue;
					}

					$topic_title = '';
					$type_url = $matches[4][$k];
					$topic_post_id = (int) $matches[5][$k];

					// Forums without id
					if (!in_array($type_url, array('topic', 'post', 'forum', 'member', '-u')))
					{
						$type_url = $matches[3][$k];
						$this->core->get_forum_id($topic_post_id, $type_url);
						if (!empty($topic_post_id))
						{
							$type_url = 'forum';
						}
						else
						{
							$type_url = '';
						}
					}

					switch ($type_url)
					{
						case 'topic':
							$sql = 'SELECT topic_title, topic_type, forum_id
								FROM ' . TOPICS_TABLE . '
								WHERE topic_id = ' . $topic_post_id;
						break;
						case 'post':
							$sql = 'SELECT t.topic_title, t.topic_type, t.forum_id, p.post_subject, p.post_id, u.username
								FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
								LEFT JOIN ' . USERS_TABLE . ' u on u.user_id = p.poster_id
								WHERE p.post_id = ' . $topic_post_id . '
									AND p.topic_id = t.topic_id';
						break;
						case 'forum':
							$sql = 'SELECT forum_name as topic_title, forum_id
								FROM ' . FORUMS_TABLE . '
								WHERE forum_id = ' . $topic_post_id;
						break;
						case '-u':
						case 'member':
							$sql = 'SELECT username
								FROM ' . USERS_TABLE . '
								WHERE user_id = ' . $topic_post_id;
						break;
					}
					if (!empty($topic_post_id) && ($result = $this->db->sql_query($sql)))
					{
						$row = $this->db->sql_fetchrow($result);
						switch ($type_url)
						{
							case 'forum':
							case 'topic':
							case 'post':
								if (!isset($forums_auth[$row['forum_id']]))
								{
									$forums_auth[$row['forum_id']] = $this->auth->acl_gets('f_list', 'f_read', $row['forum_id']);
								}
								if ($forums_auth[$row['forum_id']] || $row['topic_type'] == POST_GLOBAL)
								{
									$topic_title = (!empty($row['post_subject'])) ? $row['post_subject'] : $row['topic_title'];
								}
							break;
							case '-u':
							case 'member':
								$topic_title = $row['username'];
							break;
						}
						$this->db->sql_freeresult($result);
					}

					if (!empty($topic_title))
					{
						$internal_url = substr($str, strlen($matches[1][$k]));
						$internal_url = preg_replace('/(&amp;)sid=[0-9a-f]{32}/', '', $internal_url);
						if (preg_match($last_char_patt, $str, $math))
						{
							$internal_url = preg_replace($last_char_patt, '', $internal_url);
						}
						$topic_title = ($type_url == 'post') ? $topic_title . ' (' . $this->user->lang('POST_BY', $row['username'], $row['post_id']) . ')' : $topic_title;
						$message = str_replace($str, $matches[1][$k] . '[url=' . trim($internal_url) . ']' . $topic_title . '[/url]' . ((isset($math[1])) ? $math[1] : ''), ' ' . $message);
					}
				}
			}

			// phpBB SEO Ultimate SEO URL (Advanced mode)
			if ($allow_allow_seo == 2)
			{
				$seo_ext = '(' . preg_quote($this->core->seo_ext['forum']) . '|' . preg_quote($this->core->seo_ext['topic']) . '|' . preg_quote($this->core->seo_ext['post']) . '|' . preg_quote($this->core->seo_ext['user']) . ')';
				$parse_url  = '(^|[\n\t (>.])' . preg_quote(generate_board_url()) . '/(\S*)(\-t|post|\-f|member|\-u)([0-9]+)(-([0-9]+))?' . $seo_ext;   //([\w\-+]+)
				$parse_url = str_replace('\://', '\://(www\.)*', $parse_url);
				$matches0 = array();
				preg_match_all('#' . $parse_url . '([\w\#$%&~\-;:=,?@\[\]+]*)#si', ' ' . $message, $matches0[]);

				// Forums without id
				$seo_ext = '(' . preg_quote($this->core->seo_ext['forum']) . '|\.html))?';
				$parse_url  = '(^|[\n\t (>.])' . preg_quote(generate_board_url()) . '/([a-z0-9_-]+)/?(page([0-9]+)' . $seo_ext;
				$parse_url = str_replace('\://', '\://(www\.)*', $parse_url);
				preg_match_all('#' . $parse_url . '([\#$%&~\-;:=,?@\[\]+]*)#si', ' ' . $message, $matches0[]);

				$forums_auth = array();
				$last_char_patt = '/([\),]*)$/';

				foreach ($matches0 as $matches)
				{
					foreach ($matches[0] as $k => $str)
					{
						if (preg_match('@(\[code(?:=([a-z]+))?\].*?)' . preg_quote($str) . '(.*?\[\/code\])@is', $message))
						{
							continue;
						}
						$topic_title = '';
						$type_url = $matches[4][$k];
						$topic_post_id = (int) $matches[5][$k];
						// Forums without id
						if (!in_array($type_url, array('-t', 'post', '-f', 'member', '-u')))
						{
							$type_url = $matches[3][$k];
							$this->core->get_forum_id($topic_post_id, $type_url);
							if (!empty($topic_post_id))
							{
								$type_url = '-f';
							}
							else
							{
								$type_url = '';
							}
						}
						switch ($type_url)
						{
							case '-t':
								$sql = 'SELECT topic_title, topic_type, forum_id
									FROM ' . TOPICS_TABLE . '
									WHERE topic_id = ' . $topic_post_id;
							break;
							case 'post':
								$sql = 'SELECT t.topic_title, t.topic_type, t.forum_id, p.post_subject, p.post_id, u.username
									FROM ' . TOPICS_TABLE . ' t, ' . POSTS_TABLE . ' p
									LEFT JOIN ' . USERS_TABLE . ' u on u.user_id = p.poster_id
									WHERE p.post_id = ' . $topic_post_id . '
										AND p.topic_id = t.topic_id';
							break;
							case '-f':
								$sql = 'SELECT forum_name as topic_title, forum_id
									FROM ' . FORUMS_TABLE . '
									WHERE forum_id = ' . $topic_post_id;
							break;
							case '-u':
							case 'member':
								$sql = 'SELECT username
									FROM ' . USERS_TABLE . '
									WHERE user_id = ' . $topic_post_id;
							break;

						}

						if (!empty($topic_post_id) && ($result = $this->db->sql_query($sql)))
						{
							$row = $this->db->sql_fetchrow($result);
							switch ($type_url)
							{
								case '-f':
								case '-t':
								case 'post':
									if (!isset($forums_auth[$row['forum_id']]))
									{
										$forums_auth[$row['forum_id']] = $this->auth->acl_gets('f_list', 'f_read', $row['forum_id']);
									}
									if ($forums_auth[$row['forum_id']] || $row['topic_type'] == POST_GLOBAL)
									{
										$topic_title = (!empty($row['post_subject'])) ? $row['post_subject'] : $row['topic_title'];
									}
								break;
								case '-u':
								case 'member':
									$topic_title = $row['username'];
								break;
							}
							$this->db->sql_freeresult($result);
						}

						if (!empty($topic_title))
						{
							$internal_url = preg_replace('/(&amp;)sid=[0-9a-f]{32}/', '', substr($str, strlen($matches[1][$k])));
							if (preg_match($last_char_patt, $str, $math))
							{
								$internal_url = preg_replace($last_char_patt, '', $internal_url);
							}
							$topic_title = ($type_url == 'post') ? $topic_title . ' (' . $this->user->lang('POST_BY', $row['username'], $row['post_id']) . ')' : $topic_title;
							$replacement = $matches[1][$k] . '[url=' . trim($internal_url) . ']' . $topic_title . '[/url]' . (isset($math[1]) ? $math[1] : '');
							$message = preg_replace("@$str@", $replacement, ' ' . $message);
						}
					}
				}
			}
			//
		}
		$event['message'] = $message;
	}
	
	/**
	 * Add permissions
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['a_clutln'] = array('lang' => 'ACL_A_CLUTLN', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
//////////////
}
