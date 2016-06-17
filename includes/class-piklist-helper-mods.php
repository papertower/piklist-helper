<?php
/*
 * Modifies piklist to make life easier
 */
class PiklistHelperMods {
  public static function _construct() {
    // Add constant and variable type support
    add_filter( 'piklist_part_process', array(__CLASS__, 'add_variable_support'), 1, 2 );
  }

  /**
   * Example: [WP_DEBUG]  <-- Returns value of WP_DEBUG constant
   * Example: {my_var}    <-- Returns value of $my_var in scope or global
   * @since 0.1.0
   */
  public static function add_variable_support($part, $folder) {
    if ( !apply_filters('use_piklist_helper_mods', true) ) return $part;

    $apply_constant = function($matches) {
      return constant($matches[1]);
    };

    $apply_variable = function($matches) {
      if ( isset($$matches[1]) ) return $$matches[1];
      if ( isset($GLOBALS[$matches[1]]) ) return $GLOBALS[$matches[1]];
    };

    $keys = array('post_type', 'template', 'taxonomy', 'capability', 'role');
    foreach($keys as $index => $key) {
      if ( empty($part['data'][$key]) ) continue;
      $part['data'][$key] = preg_replace_callback('/\[(\w+)\]/', $apply_constant, $part['data'][$key]);
      $part['data'][$key] = preg_replace_callback('/{(\w+)}/',   $apply_variable, $part['data'][$key]);
    }

    return $part;
  }
}
?>
