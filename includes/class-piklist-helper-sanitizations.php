<?php
/**
 * Included Sanitizations
 *    > youtube-id, vimeo-id,
 *    > esc_url, external-url
 */
class PiklistHelperSanitizations {
  public static function _construct() {
    add_filter('piklist_sanitization_rules', array(__CLASS__, 'add_sanitizations'), 11);
  }

  /**
   * Not to be called directly.
   * Provides new types of sanitization
   */
  public static function add_sanitizations($rules) {
    return array_merge($rules, array(
      'youtube-id'    => array(
        'callback'      => array(__CLASS__, 'sanitize_youtube_id')
      ),
      'vimeo-id'    => array(
        'callback'      => array(__CLASS__, 'sanitize_vimeo_id')
      ),
      'esc_url'     => array(
        'callback'      => array(__CLASS__, 'sanitize_esc_url')
      ),
      'external-url'     => array(
        'callback'      => array(__CLASS__, 'sanitize_external_url')
      )
    ));
  }

  public static function sanitize_youtube_id($value, $field) {
    $pattern = '/youtu(?>be\.com|\.be)\/(?>watch\?v=|embed\/)?([[:alnum:]_-]+)$/i';
    return preg_match($pattern, $value, $matches) ? $matches[1] : $value;
  }

  public static function sanitize_vimeo_id($value, $field) {
    $pattern = '/(?>player\.)?vimeo\.com\/(?>video\/)?(\d+)$/i';
    return preg_match($pattern, $value, $matches) ? $matches[1] : $value;
  }

  public static function sanitize_esc_url($value, $field) {
    return esc_url($value);
  }

  public static function sanitize_external_url($value, $field, $options) {
    $options = wp_parse_args($options, array(
      'scheme'  => 'http'
    ));
    return parse_url($value, PHP_URL_SCHEME) === null
      ? "{$options['scheme']}://$value" : $value;
  }
}
?>
