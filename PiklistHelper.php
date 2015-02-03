<?php

/**
 * Adds useful validations and such to Piklist as well as
 * expands on it's capabilities
 *
 * Included Validations
 *    > youtube-urls, vimeo-urls
 *    > date-range, number
 *    > group-mismatch, require-group
 *
 * Included Sanitizations
 *    > youtube-id, vimeo-id,
 *    > esc_url
 *
 * @version 0.5.2
 */
class PiklistHelper {
  /**
   * Adds all the included validations and additiions to Piklist.
   * Make sure you call this after including the file: PiklistHelper::Initiate();
   */
  public static function Initiate() {
    if ( !class_exists('Piklist') ) return;

    // Validations & Sanitizations
    add_filter('piklist_validation_rules', array(__CLASS__, 'add_validations'), 11);
    add_filter('sanitization_rules', array(__CLASS__, 'add_sanitizations'), 11);

    // Add constant type support to post types
    add_filter( 'piklist_part_add', array(__CLASS__, 'add_constant_support'), 1, 2 );
  }



  /*****************************************************/
  /************* GENERAL PURPOSE FUNCTIONS *************/
  /*****************************************************/

  /**
   * Parses a piklist group into better format for iteration
   * @since 0.1.0
   * @param array $array
   * @return array
   */
  public static function parse_array($array) {
    if ( empty($array) )
      return array();

    $keys = array_keys($array);
    if ( empty($keys) )
      return array();

    $results = $values = array();
    $count = count($array[$keys[0]]);
    for ($index = 0; $index < $count; $index++) {
      foreach($keys as $key_index => $key) {
        $value = ( isset($array[$key][$index]) ) ? $array[$key][$index] : null;
        if ( is_array($value) && !( isset($value[0][0]) || empty($value[0]) ) ) {
          $values[$key] = self::parse_array($value, true);
        } else
          $values[$key] = $value;
      }

      $results[] = $values;
    }

    return $results;
  }



  /*****************************************************/
  /************ SANITIZING FILTER FUNCTION *************/
  /*****************************************************/
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
      )
    ));
  }

  /*****************************************************/
  /************ VALIDATION FILTER FUNCTION *************/
  /*****************************************************/

  /**
   * Filter only, not to be called directly.
   * Provides new types of validation types
   */
  public static function add_validations($rules) {
    return array_merge($rules, array(
      'youtube-url'     => array(
        'rule'            => '/youtu(?>be\.com|\.be)\/(?>watch\?v=|embed\/)?([[:alnum:]_-]+)$/i',
        'message'         => __('is not a valid youtube url')
      ),
      'vimeo-url'       => array(
        'rule'            => '/(?>player\.)?vimeo\.com\/(?>video\/)?(\d+)$/i',
        'message'         => __('is not a valid vimeo share url')
      ),
      'zip-code'        => array(
        'rule'            => '/^\d{5}(?:-\d{4})?$/',
        'message'         => __('is not a valid US zip code')
      ),
      'video-url'       => array(
        'callback'        => array(__CLASS__, 'check_video_url'),
      ),
      'number'          => array(
        'callback'        => array(__CLASS__, 'check_number'),
      ),
      'date-range'      => array(
        'callback'        => array(__CLASS__, 'check_date_range'),
      ),
      'group-mismatch'  => array(
        'callback'        => array(__CLASS__, 'check_group_mismatch')
      ),
      'require-group'   => array(
        'callback'        => array(__CLASS__, 'check_group_requirement')
      )
    ));
  }



  /*****************************************************/
  /*********** SANITIZING CALLBACK FUNCTIONS ***********/
  /*****************************************************/
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

  /*****************************************************/
  /*********** VALIDATION CALLBACK FUNCTIONS ***********/
  /*****************************************************/

  /**
   * For validation use. Do not call directly.
   * 
   * Checks that a url is either a youtube or vimeo url
   * @since 0.5.1
   */
  public static function check_video_url($index, $value, $options, $field, $fields) {
    if ( $field['add_more'] && empty($value) ) return true;

    $url = parse_url($value, PHP_URL_HOST);
    if ( false === $url ) return __('is not a valid video url');

    switch(strtolower(str_ireplace('www.', '', $url))) {
      case 'youtube.com':
      case 'youtu.be':
        return preg_match('/youtu(?>be\.com|\.be)\/(?>watch\?v=|embed\/)?([[:alnum:]_-]+)$/i', $value)
          ? true : __('is not a valid youtube url');
      case 'vimeo.com':
        return preg_match('/(?>player\.)?vimeo\.com\/(?>video\/)?(\d+)$/i', $value)
          ? true : __('is not a valid vimeo url');
    }

    return __('is not a valid vimeo or youtube url');
  }

  /**
   * For validation use. Do not call directly.
   *
   * Checks that a field is numeric with the following options:
   *    integer:  number must be an integer
   *    positive: number must be positive
   *    negative: number must be negative
   *
   * Example: 'options' => array('integer', 'positive');
   *
   * @since 0.1.0
   */
  public static function check_number($index, $value, $options, $field, $fields) {
    if ( !is_numeric($value) )
      return __('must be a number');

    if ( !empty($options) ) {
      foreach($options as $key => $option) {
        switch($option) {
          case 'integer':
            if ( (int) $value != $value )
              return __('must be an integer');
            break;

          case 'positive':
            if ( $value <= 0 )
              return __('must be a positive number');
            break;

          case 'negative':
            if ( $value >= 0 )
              return __('must be a negative number');
            break;
        }
      }
    }

    return true;
  }

  /**
   * For validation use. Do not call directly.
   *
   * Checks a group of datepickers with field names 'start-date' and 'end-date'
   * to make sure the start-date is before the end-date and the two dates
   * are in valid formats (recognized by strtotime function).
   *
   * @since 0.1.0
   */
  public static function check_date_range($values, $fields, $options) {
    if ( !is_array($values) || !isset($values[0]) )
      return __('is intended to be used for a group of datepickers');

    $dates = $values[0];

    if ( !isset($dates['end-date']) || !isset($dates['start-date']) )
      return __('requires start-date and end-date fields to work');

    if ( empty($dates['end-date']) )
      return __('must have an end date');

    if ( empty($dates['start-date']) )
      return __('must have a start date');

    $start_date = strtotime($dates['start-date']);
    $end_date = strtotime($dates['end-date']);

    if ( false === $start_date )
      return __('must have a valid date format for the start date');

    if ( false === $end_date )
      return __('must have a valid date format for the end date');

    if ( $start_date >= $end_date )
      return __('must have a start date before the end date');

    return true;
  }

  /**
   * For validation use. Do not call directly.
   *
   * Checks a group of fields to ensure that no two values are the same within
   * the group.
   *
   * @since 0.2.0
   */
  public static function check_group_mismatch($index, $value, $options, $field, $fields) {
    if ( !is_array($values) || !isset($values[0]) )
      return __('is intended to be used for a group of text or select fields');

    $fields = $values[0];

    $duplicates = array();
    foreach($fields as $key => $value) {
      if (++$duplicates[$value] > 1 )
        return __('cannot have duplicate values');
    }

    return true;
  }

  /**
   * For validation use. Do not call directly.
   *
   * Checks a group of fields to make sure that either none or all of the fields are filled
   *
   * @since 0.3.0
   */
  public static function check_group_requirement($index, $value, $options, $field, $fields) {
    if ( !is_array($values) || !isset($values[0]) )
      return __('is intended to be used for a group');

    $is_empty = true;
    foreach($values[0] as $key => $value) {
      if ( $is_empty ) {
        $is_empty = empty($value);
          
      } else if (empty($value)) {
        return __('must have all the fields filled');
      }
    }

    return true;
  }



  /*****************************************************/
  /************ PIKLIST EXTENSION FUNCTIONS ************/
  /*****************************************************/

  /**
   * Filter Only, not to be called directly.
   * Adds constant support to the post type. To use wrap
   * the name in square brackets. Example: [POST_CONSTANT]
   *
   * @since 0.1.0
   */
  public static function add_constant_support($data) {
    if ( !empty($data['type']) ) {
      $data['type'] = preg_replace_callback('/\[(\w+)\]/', array(__CLASS__, 'apply_match_constant'), $data['type']);
      $data['type'] = preg_replace_callback('/{(\w+)}/', array(__CLASS__, 'apply_match_variable'), $data['type']);
    }

    if ( !empty($data['template']) ) {
      $data['template'] = preg_replace_callback('/\[(\w+)\]/', array(__CLASS__, 'apply_match_constant'), $data['template']);
      $data['template'] = preg_replace_callback('/{(\w+)}/', array(__CLASS__, 'apply_match_variable'), $data['template']);
    }

    return $data;
  }

  /**
   * Callback for add_constant_support
   * @since 0.5.0
   */
  public static function apply_match_constant($matches) {
    return constant($matches[1]);
  }

  /**
   * Callback for add_constant_support
   * @since 0.5.0
   */
  public static function apply_match_variable($matches) {
    if ( isset($$matches[1]) ) return $$matches[1];
    if ( isset($GLOBALS[$matches[1]]) ) return $GLOBALS[$matches[1]];
  }
}

?>
