<?php
/**
 * Functions to be called directly
 */
class PiklistHelper {
  /**
   * Parses a piklist group into better format for iteration
   * @since 0.1.0
   * @param array $array
   * @return array
   */
  public static function parse_array($array) {
    // If using a version of Piklist with this function rolled
    // into the core, use that instead
    if ( method_exists('Piklist', 'object_format') )
      return piklist::object_format($array);

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
}
?>
