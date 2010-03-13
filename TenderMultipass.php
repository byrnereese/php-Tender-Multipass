<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * A PHP library for enabling single sign-on for Tender App's Multipass
 * protocol.
 * 
 * @uses Services_JSON
 * @uses mcrypt
 * @license Artistic
 * @author Byrne Reese
 * @copyright Copyright (c) 2010, Byrne Reese
 */
class TenderMultipass {
  private $expires = 5;
  private $site_key;
  private $api_key;
  /**
   * Visit your tender's settings area and enable MultiPass. Then take note
   * of your site key and api key.
   * 
   * @param site_key Your TenderApp site key.
   * @param api_key Your TenderApp API key.
   */
  function __construct($site_key,$api_key) {
    $this->site_key = $site_key;
    $this->api_key  = $api_key;
  }
  /**
   * Sets or gets the expiration TTL (expressed in minutes).
   *
   * @return The number of minutes until expiration.
   * @param min The number of minutes until the multipass will expire. Default: 5.
   */
  function expires( $min = 0 ) {
    if ($min > 0) { $this->expires = $min; }
    return $this->expires; 
  }
  /**
   * Outputs a Multipass token for the array provided. The array is a freeform
   * array that should conform to the requirements set forth by TenderApp with
   * regards to required and recommended values.
   *
   * It is not necessary to pass in the expiration date. This library will
   * generate that for you automatically.
   * 
   * @param data An associative array containing the data to be encoded.
   * @return An encoded multipass token.
   */
  function to_string( $data ) {
    require_once("JSON.php");
    $json = new Services_JSON();

    // error checking for key names
    // unique_id, email required
    $data['expires'] = date('D M d H:i:s e Y',( time() + (60 * $this->expires)) );

    $sso = $json->encode( $data );
    $salted = $this->site_key . $this->api_key;
    $hash = hash('sha1',$salted,true);
    $saltedHash = substr($hash,0,16);
    $iv = "OpenSSL for PHP!";
    for ($i = 0; $i < 16; $i++) {
      $sso[$i] = $sso[$i] ^ $iv[$i];
    }
    $pad = 16 - (strlen($sso) % 16);
    $sso = $sso . str_repeat(chr($pad), $pad);
    
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
    mcrypt_generic_init($cipher, $saltedHash, $iv);
    $enc = mcrypt_generic($cipher,$sso);
    mcrypt_generic_deinit($cipher);
    
    $b64 = base64_encode($enc);
    $b64 = preg_replace('/\n/','',$b64);
    $b64 = preg_replace('/\=*$/','',$b64);
    $b64 = preg_replace('/\+/','-',$b64);
    $b64 = preg_replace('/\//','_',$b64);
    return $b64;
  }
}
?>
