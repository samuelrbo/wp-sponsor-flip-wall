<?php
include_once 'sponsor.php';

/**
 * Sponsor's class
 *
 * @license GPLv3
 * @version 0.1 15th 19:25
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class SponsorDAO {

	/**
	 * @var wpdb
	 */
	private $db;
	/**
	 * @var string
	 */
	private $table;

	/**
	 * @var string
	 */
	private $img_directory;

	/**
	 * Constructor
	 *
	 * @access public
	 * @global wpdb $wpdb
	 */
	public function __construct()
	{
		global $wpdb;
		
		$this->db = $wpdb;
		$this->table = $this->db->prefix."sponsor_flip";
		// The Sponsor-Filp-Wall plugin folder directory
		$this->img_directory = WP_CONTENT_DIR.DS.'uploads'.DS.'sponsors_img';
	}

	/**
	 * Create the plugin table in WP database
	 *
	 * @access public
	 * @global wpdb $wpdb
	 * @return void
	 */
	public static function createTable()
	{
		global $wpdb;
		// Check if the plugin table already exists
		if ( $wpdb->get_var("SHOW TABLES LIKE {$wpdb->prefix}sponsor_flip") != "{$wpdb->prefix}sponsor_flip" ) {
			// Creating the plugin table if ir not exists
			$wpdb->query("
			CREATE TABLE {$wpdb->prefix}sponsor_flip (
				id_sponsor BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(150) NOT NULL,
				img VARCHAR(40) DEFAULT NULL,
				description TEXT DEFAULT NULL,
				link TEXT DEFAULT NULL,
				status ENUM('active','inactive') DEFAULT 'active'
			);");
			
			// Create the WP upload folder if it doen't exist OR
			if ( !is_dir( WP_UPLOAD_DIR ) ) mkdir( WP_UPLOAD_DIR, 0777 );
			// change the folder permissions if it isn't writeable
			elseif ( !is_writeable( $upload_directory ) ) chmod( WP_UPLOAD_DIR, 0777 );

			// Create the plugin folder if it doesn't exists OR
			if ( !is_dir( WP_SFW_IMG_DIR ) ) mkdir( WP_SFW_IMG_DIR, 0777 );
			// change the folder permissions if it isn't writeable
			elseif ( !is_writable( WP_SFW_IMG_DIR ) ) chmod ( WP_SFW_IMG_DIR, 0777 );
		}
	}

	/**
	 * Remove the plugin database table and folder with all files
	 * if it's set in the plugin configuration
	 *
	 * @access public
	 * @global wpdb $wpdb
	 */
	public static function removeTable()
	{
		global $wpdb;
		// Get the option to remove plugin table
		$remove_table = get_option('wp_sfw_remove_tables', '1');
		// Get the option to remove plugin filder/imgs
		$remove_folder = get_option('wp_sfw_remove_folders', '1');

		// Remove table if it's OK
		if ( $remove_table == '1' )
			$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sponsor_flip;");

		// Remove folder/imgs if it's OK
		if ( $remove_folder == '1' ) WPSFW_Help::destroy( get_option('wp_sfw_img_folder', WP_SFW_IMG_DIR) );
	}

	/**
	 * Return the plugin table name
	 *
	 * @access public
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Return the plugin image directory
	 *
	 * @access public
	 * @return string
	 */
	public function getImgDirectory()
	{
		return $this->img_directory;
	}

	/**
	 * Get a specific sponsor from database. If it doesn't exists return false
	 *
	 * @access public
	 * @param integer $id Sponsor ID
	 * @return Sponsor
	 */
	public function get( $id )
	{
		$data = $this->db->get_row( $this->db->prepare("
			SELECT *
			FROM {$this->table}
			WHERE id_sponsor = %s
		", array( $id ) ), ARRAY_A );

		
		return new Sponsor( $data );
	}

	/**
	 * Method to get an array of sponsors, this will depends of the parameters
	 *
	 * @access public
	 * @param string $order_by
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $status Can be 'active' or 'inactive'
	 * @return array
	 */
	public function getAll( $order_by = 'id_sponsor', $limit = false, $offset = false, $status = false )
	{
		$sponsors = array();
		$binds = array();

		$sql = "
				SELECT *
				FROM {$this->table}
				";

		if ( $status && ( $status == 'active' || $status == 'inactive' ) ) {
			$sql .= "WHERE status = %s
				";
			$binds[] = $status;
		}

		if ( $limit ) {
			$sql .= "LIMIT %s
				";
			$binds[] = $limit;

			if ( $offset ) {
				$sql .= "OFFSET %s
				";
				$binds[] = $offset;
			}
		}

		$result = $this->db->get_results( $this->db->prepare( $sql, $binds ), ARRAY_A );
		foreach ( $result as $r )
			$sponsors[] = new Sponsor( $r );

		return $sponsors;
	}

	/**
	 *
	 * @access public
	 * @param Sponsor $sponsor
	 * @return Sponsor
	 */
	public function save( Sponsor &$sponsor )
	{
		$image = ( $sponsor->getImg() != Sponsor::$default_img ) ? $sponsor->getImg() : '';
		$data = array(
			'name' => $sponsor->getName(),
			'description' => $sponsor->getDescription(),
			'img' => $image,
			'link' => $sponsor->getLink(),
			'status' => $sponsor->getStatus()
		);

		if ( !$sponsor->getId() ) {
			if( !$this->db->insert( $this->table, $data ) )
				throw new Exception( __("An error acurred! The sponsor can't be saved") );


			$sponsor->setId( $this->db->insert_id );
		} else {
			if ( !$this->db->update( $this->table, $data, array( 'id_sponsor' => $sponsor->getId() ) ) )
				throw new Exception( __("An error acurred! The sponsor's data can't be update") );
		}

		return $sponsor;
	}

	/**
	 * Method to get the number of the sponsors saved in database
	 * ( everyone or by the sponors status )
	 *
	 * @access public
	 * @param mixed $status String( 'active' OR 'inactive' ) Boolea( false )
	 * @return integer Number of total rows
	 */
	public function total_rows( $status = false )
	{
		$binds = array();
		$sql = "
			SELECT COUNT(*)
			FROM {$this->table}
			";

		if ( $status ) {
			$sql .= "WHERE status = %s
			";
			$binds[] = $status;
		}

		return $this->db->get_var( $this->db->prepare( $sql, $binds ) );
	}

	/**
	 * Method to remove a sponsor from database
	 *
	 * @access public
	 * @param Sponsor $sponsor 
	 */
	public function remove( Sponsor $sponsor )
	{
		$this->db->query("DELETE FROM {$this->table} WHERE id_sponsor = {$sponsor->getId()};");
	}
}

