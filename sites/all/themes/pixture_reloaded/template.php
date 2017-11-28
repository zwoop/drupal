<?php
// Pixture Reloaded by Adaptivethemes.com

function pixture_reloaded_js_alter(&$js) {
  if (arg(0) === 'admin' || strpos($_GET['q'], 'search/google') === 0) {
    return;
  }

  uasort($js, 'drupal_sort_css_js');
  $weight = 0;

  foreach ($js as $name => $javascript) {
    $js[$name]['group'] = -100;
    $js[$name]['weight'] = ++$weight;
    $js[$name]['every_page'] = 1;
    $js[$name]['scope'] = 'footer';
  }
}

function pixture_reloaded_css_alter(&$css) {
  uasort($css, 'drupal_sort_css_js');

  $print = array();
  $weight = 0;
  foreach ($css as $name => $style) {
    $css[$name]['group'] = 0;
    $css[$name]['weight'] = ++$weight;
    $css[$name]['every_page'] = TRUE;

    if ($css[$name]['media'] == 'print') {
      $print[$name] = $css[$name];
      unset($css[$name]);
    }
  }

  $css = array_merge($css, $print);
}

/**
 * Override or insert variables into the html template.
 */
function pixture_reloaded_preprocess_html(&$vars) {
  global $theme_key;

  $theme_name = 'pixture_reloaded';
  $path_to_theme = drupal_get_path('theme', $theme_name);

  // Add a class for the active color scheme
  if (module_exists('color') && function_exists('get_color_scheme_name')) {
    $class = check_plain(get_color_scheme_name($theme_key));
    $vars['classes_array'][] = 'color-scheme-' . drupal_html_class($class);
  }

  // Add class for the active theme
  $vars['classes_array'][] = drupal_html_class($theme_key);

  // Add theme settings classes
  $settings_array = array(
    'box_shadows',
    'body_background',
    'menu_bullets',
    'menu_bar_position',
    'corner_radius',
  );
  foreach ($settings_array as $setting) {
    $vars['classes_array'][] = theme_get_setting($setting);
  }

  // Special case for PIE htc rounded corners, not all themes include this
  if (theme_get_setting('ie_corners') == 1) {
    drupal_add_css($path_to_theme . '/css/ie-htc.css', array(
      'group' => CSS_THEME,
      'browsers' => array(
        'IE' => 'lte IE 8',
        '!IE' => FALSE,
        ),
      'preprocess' => FALSE,
      )
    );
  }
}

/**
 * Override or insert variables into the html template.
 */
function pixture_reloaded_process_html(&$vars) {
  // Hook into color.module
  if (module_exists('color')) {
    _color_html_alter($vars);
  }
}

/**
 * Override or insert variables into the page template.
 */
function pixture_reloaded_process_page(&$vars) {
  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }
}

/**
 * Override or insert variables into the block template.
 */
function pixture_reloaded_preprocess_block(&$vars) {
  if($vars['block']->module == 'superfish' || $vars['block']->module == 'nice_menu') {
    $vars['content_attributes_array']['class'][] = 'clearfix';
  }
}
