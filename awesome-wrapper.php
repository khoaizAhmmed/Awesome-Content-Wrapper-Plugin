<?php
/* 
* Plugin Name: Awesome Content Wrapper
* Description: awesome Wrapper - see the magic.
* Version: 1.0
* Author: khoaiz
* Author URI: https://www.khoaiz.com
*/

// add_filter('the_content', 'addToEndPost');

// function addToEndPost($content)
// {
//   if (is_page() && is_main_query()) {

//     return $content . '<b>My name is khoaiz</b>';
//   }
//   return $content;
// }

class WordCountAndTimePlugin
{

  function __construct()
  {
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifWarp'));
  }

  function ifWarp($content)
  {
    if ((is_main_query() and is_single()) and (get_option('wcp_word_count', '1') or get_option('wcp_character_count', '1') or get_option('wcp_read_time', '1'))) {
      return $this->createHTML($content);
    }
    return $content;
  }

  function createHTML($content)
  {
    $html = '<h3>' . esc_html(get_option('wcp_headline', "Post Statistics")) . '</h3><p>';
    if(get_option('wcp_word_count','1') OR get_option('wcp_read_time')){
      $wordCount = str_word_count(strip_tags($content));
    }

    if(get_option('wcp_word_count','1')){
      $html .= 'The post has '. $wordCount .' words.<br>';
    }

    if(get_option('wcp_character_count','1')){
      $html .= 'The post has '. strlen(strip_tags($content)) .' characters.<br>';
    }

    if(get_option('wcp_read_time','1')){
      $html .= 'The post will take about '. ceil($wordCount/255) .' minute(s) to read.<br>';
    }

      $html.= '</p>';
    if (get_option('wcp_location', '0') == '0') {
      return  $html . $content;
    }
    return $content . $html;
  }
  function settings()
  {
    add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');

    // Location field

    add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

    // Post Header

    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headerHTML'), 'word-count-settings-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

    // Word Count


    add_settings_field('wcp_word_count', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_word_count'));
    register_setting('wordcountplugin', 'wcp_word_count', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    // Character count

    add_settings_field('wcp_character_count', 'Character count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_character_count'));
    register_setting('wordcountplugin', 'wcp_character_count', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    // Read time 

    add_settings_field('wcp_read_time', 'Read time ', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_read_time'));
    register_setting('wordcountplugin', 'wcp_read_time', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
  }


  function sanitizeLocation($input)
  {
    if ($input != '0' and $input != '1') {
      add_settings_error("wcp_location", 'wcp_location_error', 'Display location must be either beginning or end');
      return get_option('wcp_location');
    }
    return $input;
  }

  function locationHTML()
  {
?>
    <select name="wcp_location">
      <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
    </select>
  <?php
  }

  function headerHTML()
  {
  ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
  <?php
  }
  /*


  function wordcountHTML(){
    ?>
    <input type="checkbox" name="wcp_word_count" value="1" <?php checked(get_option('wcp_word_count'), '1') ?> >
    <?php
  }

  
  function charactercountHTML(){
    ?>
    <input type="checkbox" name="wcp_character_count" value="1" <?php checked(get_option('wcp_character_count'), '1') ?> >
    <?php
  }

  function readtimeHTML(){
    ?>
    <input type="checkbox" name="wcp_read_time" value="1" <?php checked(get_option('wcp_read_time'), '1') ?> >
    <?php
  }
 */
  function checkboxHTML($args)
  {
  ?>
    <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
  <?php
  }

  function adminPage()
  {
    add_options_page('Word count setting', 'Word count', 'manage_options', 'word-count-settings-page', array($this, 'ourHTML'));
  }
  function ourHTML()
  {
  ?> <div class="warp">
      <h1>Word Count Settings</h1>
      <form action="options.php" method="POST">
        <?php
        settings_fields('wordcountplugin');
        do_settings_sections('word-count-settings-page');
        submit_button();
        ?>

      </form>
    </div> <?php
          }
        }

        $WordCountAndTimePlugin = new WordCountAndTimePlugin;
