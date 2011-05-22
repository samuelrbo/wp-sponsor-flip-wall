<?php

/**
 * Sponsor's class
 *
 * @license GPLv3
 * @version 0.1
 * @since 2011 May 15th 18:58
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class Sponsor {

	/**
	 * @static
	 * @var string
	 */
	public static $default_img;

	/**
	 * @var integer
	 */
	private $id_sponsor;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $img;
	/**
	 * @var string
	 */
	private $link;
	/**
	 * @var string
	 */
	private $status = 'active';

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 *
	 * @global wpdb $wpdb
	 * @param array $data 
	 */
	public function __construct( array $data = array() )
	{
		global $wpdb;
		
		foreach ( $data as $key => $val )
			if ( property_exists($this, $key) )
				$this->$key = $val;

		self::$default_img = WP_SFW_IMAGES_DIR . 'default.png';
		$this->db = $wpdb;
	}

	/**
	 * Method to get sponsor ID
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id_sponsor;
	}

	/**
	 * Method to set sponsor ID
	 *
	 * @param integer $id
	 */
	public function setId($id)
	{
		$this->id_sponsor = $id;
	}

	/**
	 * Method to get sponsor name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Method to set sponsor name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Method to get sponsor description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Method to set sponsor description
	 *
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getImgDir( $url = true )
	{
		if ( !$this->img && $url )
			return self::$default_img;

		return ( $url ) ? WP_SFW_IMG_URL."/{$this->img}" : WP_SFW_IMG_DIR.$this->img;
	}

	/**
	 * Methdo to get the sponsor image ( Full url path )
	 *
	 * @return string
	 */
	public function getImg() {
		if ( !$this->img && $url )
			return self::$default_img;

		return $this->img;
	}

	/**
	 * Method to set the sponsor image ( only the name, the path
	 * that it will be used is ./uploads/sponsors_img/ )
	 *
	 * @param string $img
	 */
	public function setImg($img) {
		$this->img = $img;
	}

	/**
	 * Method to get the sponsor link
	 *
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Method to set the sponsor link
	 *
	 * @param string $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}

	/**
	 * Method to get sponsor status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return ( is_null($this->status) ) ? 'active' : $this->status;
	}

	/**
	 * Method to set sponsor status
	 *
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
}

