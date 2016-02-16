<?php
class Colabs_Log {

  var $log_file;
  var $fp;

  /**
   * The single instance of the class
   */
  protected static $_instance = null;
  
  /**
   * Constructor
   */
  function __construct() {
    $this->log_file = get_template_directory() . '/log/goodliving_log.txt';
    $this->fp = null;
  }

  /**
   * Main Instance
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new Colabs_Log;
    }
    return self::$_instance;
  }
  
  /**
   * write message to the log file
   */
  public function write_log( $message ){
    //$logging_enabled = get_option('colabs_enable_log');
    $logging_enabled = false;
    if ($logging_enabled == 'true') {
      // if file pointer doesn't exist, then open log file
      if (!$this->fp) $this->open_log();

      // define script name
      $script_name = basename($_SERVER['PHP_SELF']);
      $script_name = substr($script_name, 0, -4);

      // define current time
      $time = date_i18n('H:i:s');

      // write current time, script name and message to the log file
      fwrite($this->fp, "$time ($script_name) $message\n");
    }
  }
  
  /**
   * open log file
   */
  function open_log(){
    // define log file path and name
    $lfile = $this->log_file;
    // open log file for writing only; place the file pointer at the end of the file
    // if the file does not exist, attempt to create it
    $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
  }
  
  /**
   * clear log file
   */
  function clear_log(){
    $lfile = $this->log_file;
    $fp = @fopen($lfile, 'w');
    @fclose($fp);
  }

}

function Colabs_Log() {
  return Colabs_Log::instance();
}

// Global for backwards compatibility.
$GLOBALS['colabs_log'] = Colabs_Log();
