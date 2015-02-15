<?php
/*
 * Plugin Name: Piklist Helper
 * Plugin URI: https://github.com/JasonTheAdams/PiklistHelper
 * Plugin Type: Piklist
 * Description: A helper for adding additional filters, validations, and functionality to Piklist
 * Version: 0.6.0
 * Author: Jason Adams
 * Author URI: https://github.com/JasonTheAdams/
 * License: MIT
 */

add_action('init', function() {
  // Check for Piklist
  if(is_admin()) {
    include_once('includes/class-piklist-checker.php');
    if (!piklist_checker::check(__FILE__)) return;
  }
});

// Functions to be called directly
if ( !class_exists('PiklistHelper') ) {
  require_once('includes/class-piklist-helper.php');
}

// Addtional and improved validations
if ( !class_exists('PiklistHelperValidations') ) {
  require_once('includes/class-piklist-helper-validations.php');
  PiklistHelperValidations::_construct();
}

// Addtional and improved sanitizations
if ( !class_exists('PiklistHelperSanitizations') ) {
  require_once('includes/class-piklist-helper-sanitizations.php');
  PiklistHelperSanitizations::_construct();
}

// Modifications to Piklist to make life easier
if ( !class_exists('PiklistHelperMods') ) {
  require_once('includes/class-piklist-helper-mods.php');
  PiklistHelperMods::_construct();
}

?>
