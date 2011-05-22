<?php
/**
 * Some classes to help in the project.
 *
 * @package WordPress
 * @subpackage Easy Event Promotion
 * @since 1.0
 * @link http://phpcafe.com.br/
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */

if ( !defined('EXT') ) define('EXT', '.php');
if ( !defined('DS') ) define('DS', DIRECTORY_SEPARATOR);

/**
 * Class to load files
 *
 * @package Easy Event Promotion
 * @since 1.0
 * @link http://phpcafe.com.br/
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class Load {

    /**
     * Method to load a file
     *
     * @access public
     * @param string $file file full path
     */
    public static function file( $file )
    {
        $file = ( strpos($file, '.php') !== false ) ? $file : $file.EXT;
        if ( file_exists( $file ) )
            include($file);
    }

    /**
     * Method to load a files array
     *
     * @access public
     * @param array $files array with full files pathes
     */
    public static function files( array $files )
    {
        foreach ( $files as $file )
            self::file( $file );
    }

    /**
     * Method to load a view and pass some vars to the view
     *
     * @access public
     * @param string $view the view full path
     * @param array $params Vars to pass to the view
     */
    public static function view( $view, array $params = array() )
    {
        $view = ( strpos($view, '.php') !== false ) ? $view : $view.EXT;
        if (!file_exists($view))
            return false;

        foreach ( $params as $var => $val )
            $$var = $val;

        include($view);
    }
}

/**
 * Classe to get input values ( $_POST, $_GET, $_REQUEST )
 * 
 * @package Easy Event Promotion
 * @since 1.0
 * @link http://phpcafe.com.br/
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class Input {

    /**
     * Method to get a specific value from a key of an array
     *
     * @access private
     * @param array $array
     * @param string $key Array key
     * @param mixed $default Default value to return if the key doesn't exists in array
     * @return mixed This value can be array, boolean, string, numeric, everything
     */
    private function _getValueOf( array $array, $key, $default )
    {
        return ( array_key_exists($key, $array) ) ? $array[$key] : $default;
    }

    /**
     * Method to get a value from the $_REQUEST key
     *
     * @access public
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function request( $key, $default = false )
    {
        return self::_getValueOf( $_REQUEST, $key, $default );
    }

    /**
     * Method to get a value from the $_POST key
     *
     * @access public
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function post( $key, $default = false )
    {
        return self::_getValueOf( $_POST, $key, $default );
    }

    /**
     * Method to get a value from the $_GET key
     *
     * @access public
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get( $key, $default = false )
    {
        return self::_getValueOf( $_GET, $key, $default );
    }
}

// TODO - criar autoload da classe config para carregar as configurações salvas do banco

/**
 *
 * @package Easy Event Promotion
 * @since 1.0
 * @link http://phpcafe.com.br/
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class Config {

    private static $config = array();

    /**
     * Method to load the default config file
     *
     * @static
     * @access private
     */
    private static function _loadConfig( $config_file = false )
    {
        $config_file = ($config_file) ? $config_file : SVM_PROMOCOES_INCLUDE_PATH.DS.'config'.EXT;

        if (file_exists($config_file)) {
            include($config_file);
            self::$config = $config;
        }
    }

    /**
     * Method
     *
     * @static
     * @access public
     * @param string $config_file
     */
    public static function add( $config_file )
    {
        if (file_exists($config_file)) {
            include($config_file);
            array_merge(self::$config, $config_file);
        }
    }

    /**
     *
     * @static
     * @access public
     * @param string $key
     * @return mixed
     */
    public static function item( $key )
    {
        if ( empty(self::$config) )
            self::_loadConfig();

        return ( array_key_exists($key, self::$config) ) ? self::$config[$key] : false;
    }
}


class Validate {

    /**
     * Prefix for error messages
     * 
     * @var string
     */
    private $_prefix = '<p>';
    /**
     * Suffixx for error messages
     *
     * @var string
     */
    private $_suffix = '</p>';

    /**
     * Array with form data to be validate
     *
     * @var array
     */
    protected $_array;
    /**
     * Array with the rules of the form's fields
     *
     * @var array
     */
    protected $rules = array();
    /**
     * Array with the nice names of the form's fields
     *
     * @var array
     */
    protected $fields = array();
    /**
     * Error messages
     *
     * @var string
     */
    protected $error_message = '';
    /**
     * Rules for methods errors
     *
     * @var array
     */
    protected $errors;
    /**
     * Current field that is being validate
     *
     * @var string
     */
    protected $_current_field = '';

    /**
     * Constructor
     *
     * @access public
     * @param array $array ( $_POST, $_GET, $_REQUEST, or another email with data to validate )
     */
    public function __construct( array $array = array() )
    {
        $this->_array = ( !$array ) ? $_POST : $array;
		$this->errors = array(
			'alpha'         => __('The field %s can contain only letters', 'wp-sfw-plugin'),
			'alpha_dash'    => __('The field %s can contain only letters, numbers, dashes or underscores', 'wp-sfw-plugin'),
			'alpha_numeric' => __('The field %s can contain only letters and numbers', 'wp-sfw-plugin'),
			'exact_length'  => __('The field %s can has exact %s characters', 'wp-sfw-plugin'),
			'integer'       => __('The field %s can contain only numbers', 'wp-sfw-plugin'),
			'is_natural'    => __('The field %s can contain only natural numbers', 'wp-sfw-plugin'),
			'is_numeric'    => __('The field %s can contain only numbers', 'wp-sfw-plugin'),
			'matches'       => __('The field %s can be the same as the field %s', 'wp-sfw-plugin'),
			'max_length'    => __("The field %s can't exceed %s characters", 'wp-sfw-plugin'),
			'min_length'    => __("The field %s can't be less than %s characters", 'wp-sfw-plugin'),
			'numeric'       => __('The field %s can contain only numbers', 'wp-sfw-plugin'),
			'required'      => __('The field %s is required', 'wp-sfw-plugin'),
			'valid_base64'  => __("The field %s isn't a valid base 64 encryption", 'wp-sfw-plugin'),
			'valid_email'   => __("The field %s isn't a valid email address", 'wp-sfw-plugin'),
			'valid_ip'      => __("The field %s isn't a valid IP address", 'wp-sfw-plugin'),
			'valid_url'     => __('The field %s must be a valid URL', 'wp-sfw-plugin')
		);
    }

    /**
     * Method to set the prefix and suffix for errors
     *
     * @access public
     * @param string $prefix
     * @param string $suffix
     */
    public function setErrorDelimiters( $prefix = '<p>', $suffix = '</p>' )
    {
        $this->_prefix = $prefix;
        $this->_suffix = $suffix;
    }

    /**
     * Method to set the rules for validation
     *
     * @access public
     * @param mixed $field
     * @param array $rules
     */
    public function setRules( $field, array $rules = array() )
    {
        if ( is_array($field) ) {
            foreach ( $field as $k => $v )
                $this->setRules( $k, $v );

        } else if ( !is_array($field) && !empty($rules) ) {
            $this->rules[$field] = $rules;
        }
    }

    /**
     * Method to set the nice names for array fields
     *
     * @access public
     * @param mixed $field
     * @param mixed $nice_name
     */
    public function setFields( $field, $nice_name = '' )
    {
        if ( is_array($field) ) {
            foreach ( $field as $k => $v )
                $this->setFields( $k, $v );
        } else if ( !is_array($field) && $nice_name ) {
            $this->fields[$field] = $nice_name;
        }
    }

    /**
     * Method to execute the validation
     *
     * @access public
     * @return boolean
     */
    public function run()
    {
        if (empty($this->_array) || empty($this->rules)) {
            $this->error_message .= __('No data was sent by the form to be validade', 'wp-sfw-plugin');
            return false;
        }

        foreach ( $this->_array as $key => $value ) {
            if ( array_key_exists($key, $this->rules) ) {
                $this->_current_field = $key;
                foreach ( $this->rules[$key] as $rule )
                    $this->_doValidation( $rule );
            }
        }

        if (strlen(trim($this->error_message)) > 0)
            return false;

        return true;
    }

    /**
     * Methdo to execute the rule validation with the class method
     *
     * @access private
     * @param string $rule rule to validate
     */
    private function _doValidation( $rule )
    {
        $param = false;
        $result = false;
        if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
            $rule	= $match[1];
            $param	= $match[2];
        }

        $method = '_'.$rule;
        if ( method_exists(__CLASS__, $method) )
            $result = $this->$method( $this->_array[$this->_current_field], $param );

        if ( !$result ) {
            if ( array_key_exists( $rule, $this->errors ) ) {
                $error = ( $param ) ? sprintf($this->errors[$rule],$this->fields[$this->_current_field],$param) : sprintf($this->errors[$rule],$this->fields[$this->_current_field]);
                $this->error_message .= $this->_prefix.$error.$this->_suffix;
            }
        }
    }
    /**
     * Method to get Erros messages
     *
     * @access public
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Method to check if field is required
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _required( $str )
    {
        if (!is_array( $str ))
            if (trim( $str ) == '')
                return false;

        return (!is_array($str)) ? ((trim($str) == '') ? false : true) : (!empty($str));
    }

    /**
     * Method to check if field matches with another one
     *
     * @access protected
     * @param string $str
     * @param string $field
     * @return boolean
     */
    protected function _matches( $str, $field )
    {
        if (!isset($this->_array[$field]))
			return false;

		return ($str !== $this->_array[$field]) ? false : true;
    }

    /**
     * Method to check if field supply the min length validation
     *
     * @access protected
     * @param string $str
     * @param string $val
     * @return boolean
     */
    protected function _min_length( $str, $val )
    {
        if (preg_match("/[^0-9]/", $val))
			return false;

		if (function_exists('mb_strlen'))
			return (mb_strlen($str) < $val) ? false : true;

		return (strlen($str) < $val) ? false : true;
    }

    /**
     * Method to check if field supply the max length validation
     *
     * @access protected
     * @param string $str
     * @param string $val
     * @return boolean
     */
    protected function _max_length( $str, $val )
	{
		if (preg_match("/[^0-9]/", $val))
			return false;

		if (function_exists('mb_strlen'))
			return (mb_strlen($str) > $val) ? false : true;

		return (strlen($str) > $val) ? false : true;
	}

    /**
     * Method check if the field has an exact length
     *
     * @access protected
     * @param string $str
     * @param string $val
     * @return boolean
     */
    protected function _exact_length( $str, $val )
	{
		if (preg_match("/[^0-9]/", $val))
			return false;

		if (function_exists('mb_strlen'))
			return (mb_strlen($str) != $val) ? false : true;

		return (strlen($str) != $val) ? false : true;
	}

    /**
     * Method to check if email is valid
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _valid_email( $str )
	{
		return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

//    protected function _valid_emails($str)
//	{
//		if (strpos($str, ',') === falses)
//			return $this->valid_email(trim($str));
//
//		foreach(explode(',', $str) as $email)
//			if (trim($email) != '' && $this->_valid_email(trim($email)) === false)
//				return false;
//
//		return true;
//	}

    /**
     * Method to check if the field is a valid IP
     *
     * @access protected
     * @param string $ip
     * @return boolean
     */
    protected function _valid_ip($ip)
	{
		$ip_segments = explode('.', $ip);

		// Always 4 segments needed
		if (count($ip_segments) != 4)
			return false;

		// IP can not start with 0
		if ($ip_segments[0][0] == '0')
			return false;

		// Check each segment
		foreach ($ip_segments as $segment) {
			// IP segments must be digits and can not be
			// longer than 3 digits or greater then 255
			if ($segment == '' || preg_match("/[^0-9]/", $segment) || $segment > 255 || strlen($segment) > 3)
				return false;
		}

		return true;
	}

    /**
     * Method to check if the field has only alpha characters
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _alpha( $str )
	{
		return (!preg_match("/^([a-z])+$/i", $str)) ? false : true;
	}

    /**
     * Method to check if the field has only alpha and numerics characters
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _alpha_numeric( $str )
	{
		return (!preg_match("/^([a-z0-9])+$/i", $str)) ? false : true;
	}

    /**
     * Method to check if the field has only alpha and dashes characters
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _alpha_dash( $str )
	{
		return (!preg_match("/^([-a-z0-9_-])+$/i", $str)) ? false : true;
	}

    /**
     * Method to check if the field has only numerics characters
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _numeric( $str )
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
	}

    /**
     * Method to check if the field is numeric
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _is_numeric( $str )
	{
		return (!is_numeric($str)) ? false : true;
	}

    /**
     * Method to check if the field is a integer value
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _integer($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]+$/', $str);
	}

    /**
     * Method to check if the field is a natuaral value
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _is_natural( $str )
	{
   		return (bool)preg_match( '/^[0-9]+$/', $str);
	}

    /**
     * Method to if the field is a natural number and no zero value
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function is_natural_no_zero($str)
	{
		if ( !preg_match( '/^[0-9]+$/', $str))
			return false;

		if ($str == 0)
			return false;

		return true;
	}

    /**
     * Method to check if the field is a valid base64 encode value
     *
     * @access protected
     * @param string $str
     * @return boolean
     */
    protected function _valid_base64( $str )
	{
		return (bool)!preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}

    /**
     * Methdo to prepare the url with a valid URL format
     *
     * @param string $str
     * @return mixed
     */
    protected function _prep_url($str = '')
	{
		if ($str == 'http://' || $str == '') {
			$this->_array[$this->_current_field] = '';
			return true;
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
			$str = 'http://'.$str;

		$this->_array[$this->_current_field] = $str;
		return true;
	}

    /**
     * Method to check if the the field is a valid URL
     *
     * @access protected
     * @param string $url
     * @return boolean
     */
    protected function _valid_url($url)
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }
}

/**
 * Class Image extends CodeIgniter Framework Image_lib class
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */
if ( !class_exists('CI_Image_lib') )
	include_once 'ci_image_lib.php';

class Image extends CI_Image_lib {

    /**
     * Constructor
     *
     * @access public
     * @param array $props
     */
    public function __construct($props = array())
    {
        parent::__construct($props);
    }
}

/**
 * Class Upload extends CodeIgniter Framework Upload class
 * implementing the multi upload files
 *
 * @author Samuel Ramon samuelrbo@gmail.com
 */
if ( !class_exists('CI_Upload') )
	include_once 'ci_upload.php';

class Upload extends CI_Upload {

    /**
     * Array with files names
     * @var array
     */
    private $arrFileName = array();

    /**
     * Contructor
     *
     * @access public
     * @param array $props
     */
    public function __construct($props = array())
    {
        parent::__construct($props);
    }

    /**
     * Method to return the files saved names in array
     *
     * @access public
     * @return array
     */
    public function getArrFileName()
    {
        return $this->arrFileName;
    }

    /**
     *
     * @param
     */
    function do_multiple_upload($field = 'userfile')
	{
		// Is $_FILES[$field] set? If not, no reason to continue.
		if ( ! isset($_FILES[$field])) {
			$this->set_error('upload_no_file_selected');
			return false;
		}

		// Is the upload path valid?
		if ( ! $this->validate_upload_path()) // errors will already be set by validate_upload_path() so just return FALSE
			return false;

        $total = count($_FILES[$field]['tmp_name']);
        for ($i=0;$i<$total;$i++) {
            // Was the file able to be uploaded? If not, determine the reason why.
            if ( ! is_uploaded_file($_FILES[$field]['tmp_name'][$i])) {
                $error = ( ! isset($_FILES[$field]['error'][$i])) ? 4 : $_FILES[$field]['error'][$i];

                switch($error) {
                    case 1:	// UPLOAD_ERR_INI_SIZE
                        $this->set_error('upload_file_exceeds_limit');
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE
                        $this->set_error('upload_file_exceeds_form_limit');
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL
                       $this->set_error('upload_file_partial');
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE
                       $this->set_error('upload_no_file_selected');
                        break;
                    case 6: // UPLOAD_ERR_NO_TMP_DIR
                        $this->set_error('upload_no_temp_directory');
                        break;
                    case 7: // UPLOAD_ERR_CANT_WRITE
                        $this->set_error('upload_unable_to_write_file');
                        break;
                    case 8: // UPLOAD_ERR_EXTENSION
                        $this->set_error('upload_stopped_by_extension');
                        break;
                    default :   $this->set_error('upload_no_file_selected');
                        break;
                }

                return false;
            }

            // Set the uploaded data as class variables
            $this->file_temp = $_FILES[$field]['tmp_name'][$i];
            $this->file_size = $_FILES[$field]['size'][$i];
            $this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES[$field]['type'][$i]);
            $this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
            $this->file_name = $this->_prep_filename($_FILES[$field]['name'][$i]);
            $this->file_ext	 = $this->get_extension($this->file_name);
            $this->client_name = $this->file_name;

            // Is the file type allowed to be uploaded?
            if ( ! $this->is_allowed_filetype()) {
                $this->set_error('upload_invalid_filetype');
                return false;
            }

            // if we're overriding, let's now make sure the new name and type is allowed
            if ($this->_file_name_override != '') {
                $this->file_name = $this->_prep_filename($this->_file_name_override);
                $this->file_ext  = $this->get_extension($this->file_name);

                if ( ! $this->is_allowed_filetype(true)) {
                    $this->set_error('upload_invalid_filetype');
                    return false;
                }
            }

            // Convert the file size to kilobytes
            if ($this->file_size > 0)
                $this->file_size = round($this->file_size/1024, 2);

            // Is the file size within the allowed maximum?
            if ( ! $this->is_allowed_filesize()) {
                $this->set_error('upload_invalid_filesize');
                return false;
            }

            // Are the image dimensions within the allowed size?
            // Note: This can fail if the server has an open_basdir restriction.
            if ( ! $this->is_allowed_dimensions()) {
                $this->set_error('upload_invalid_dimensions');
                return false;
            }

            // Sanitize the file name for security
            $this->file_name = $this->clean_file_name($this->file_name);

            // Truncate the file name if it's too long
            if ($this->max_filename > 0)
                $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);

            // Remove white spaces in the name
            if ($this->remove_spaces == true)
                $this->file_name = preg_replace("/\s+/", "_", $this->file_name);

            /*
             * Validate the file name
             * This function appends an number onto the end of
             * the file if one with the same name already exists.
             * If it returns false there was a problem.
             */
            $this->orig_name = $this->file_name;

            if ($this->overwrite == false) {
                $this->file_name = $this->set_filename($this->upload_path, $this->file_name);

                if ($this->file_name === false)
                    return false;
            }

            /*
             * Move the file to the final destination
             * To deal with different server configurations
             * we'll attempt to use copy() first.  If that fails
             * we'll use move_uploaded_file().  One of the two should
             * reliably work in most environments
             */
            if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name)) {
                if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name)) {
                     $this->set_error('upload_destination_error');
                     return false;
                }
            }

            /*
             * Run the file through the XSS hacking filter
             * This helps prevent malicious code from being
             * embedded within a file.  Scripts can easily
             * be disguised as images or other file types.
             */
            if ($this->xss_clean == true)
                $this->do_xss_clean();

            /*
             * Set the finalized image dimensions
             * This sets the image width/height (assuming the
             * file was an image).  We use this information
             * in the "data" function.
             */
            $this->set_image_properties($this->upload_path.$this->file_name);

            $this->arrFileName[] = $this->file_name;
        }

		return true;
	}
}

/**
 * WPSFW_Help Class
 *
 * @license GPLv3
 * @version 0.1 15th 19:25
 * @author Samuel Ramon samuelrbo@gmail.com
 */
class WPSFW_Help {

	/**
	 * Method to remove a directory and all files or directories inside it.
	 *
	 * @static
	 * @access public
	 * @param string $dirname Full directory path
	 * @return boolean
	 */
	public static function destroy( $dirname )
	{
		if (is_dir($dirname))
			$dir_handle = opendir($dirname);

		if (!$dir_handle)
			return false;

		while($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname . DS . $file))
					unlink($dirname . DS . $file);
				else
					self::destroy( $dirname . DS . $file );
			}
		}

		closedir($dir_handle);
		rmdir($dirname);

		return true;
	}
}