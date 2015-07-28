<?php
/**
 *
 * Photos extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace Aurelienazerty\Photos\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface {
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user $user */
	protected $user;
	
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface    $db DBAL object
	 * @param \phpbb\config\config	$config	Config object
	 * @param \phpbb\user	$user	user object
	 * @param \phpbb\template\template $template template object
	 * \phpbb\event\dispatcher_interface $dispatcher dispatcher object
	 * @return \Aurelienazerty\Photos\event\listener
	 * @access public
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\user $user, \phpbb\template\template $template, \phpbb\event\dispatcher_interface $dispatcher) {
		$this->user = $user;
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
		$this->dispatcher = $dispatcher;
	}

	static public function getSubscribedEvents() {
		return array(
			'core.index_modify_page_title'				=> array('afficher_photos', 2710),
		);
	}
	
	public function afficher_photos($event) {
		
		$this->user->add_lang_ext('Aurelienazerty/Photos', 'photos');
		$lesPhotos = fonctionGetLastCommentaireForUser($this->user->data["user_id"], 10, false);
		$lesPhotos = commentaireForUser($lesPhotos, $this->user->data["user_id"]);
		
		$tpl_loopname = 'recent_photos';
		
		foreach ($lesPhotos['lignesMessage'] as $photo) {
			if ($photo['compteNonLu']) {
				$folder_alt = 'UNREAD_POST';
				$folder_type = 'topic_unread';
				
				$contexte = $photo['contexte'];
				
				$tpl_ary = array(
					'LAST_POST_AUTHOR_FULL'	=> get_username_string('full', $photo['user_id'], $photo['username'], $photo['user_colour']),
					'U_VIEW_PHOTO'					=> $photo['link'],
					'PHOTO_TITLE' 					=> $photo['nom'],
					'LAST_POST_TIME'				=> $this->user->format_date($photo['date']),
					'TOPIC_FOLDER_IMG_ALT'	=> $this->user->lang[$folder_alt],
					'TOPIC_IMG_STYLE'				=> $folder_type,
					'NEWEST_POST_IMG'				=> $this->user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
					'PHOTO_CONTEXTE_URL'		=> replace_mod_rewrite($contexte[1]['href']),
					'PHOTO_CONTEXTE'				=> $contexte[1]['txt'],
				);
				
				//$vars = array('photo', 'tpl_ary');
				//extract($this->dispatcher->trigger_event('Aurelienazerty.Photos.modify_tpl_ary', compact($vars)));
	
				$this->template->assign_block_vars($tpl_loopname, $tpl_ary);
			}
		}
	}

	
}
