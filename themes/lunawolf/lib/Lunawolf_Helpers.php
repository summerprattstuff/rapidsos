<?php
class Lunawolf_Helpers {

  private static $instance;

  public function __construct() {
    add_filter('timber/twig/functions', function ($functions) {
      $functions['get_testimonials'] = [
        'callable' => [$this, 'get_testimonials']
      ];
      $functions['get_share_box'] = [
        'callable' => [$this, 'get_share_box']
      ];
      $functions['get_timber_menu'] = [
        'callable' => [$this, 'get_timber_menu']
      ];
      $functions['get_resources'] = [
        'callable' => [$this, 'get_resources']
      ];

      return $functions;
    });
  }

  public static function instance() {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * @param $number
   * @return mixed
   * Usage in template: $videos = get_videos(number)
   */
  public function get_videos($number = 1) {
    $videos = Timber::get_posts([
      'post_type' => 'video',
      'posts_per_page' => $number
    ]);

    return $videos;
  }

  public function get_testimonials($ids = '') {
    $testimonials = Timber::get_posts([
      'post_type' => 'testimonial',
      'post__in' => $ids ?: []
    ]);

    return $testimonials;
  }

  public function get_share_box() {
    $url = esc_url(get_permalink());
    $title = urlencode(html_entity_decode(get_the_title(), ENT_COMPAT, 'UTF-8'));

    $social_networks = array(
      'fb' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
      'x' => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
      'ln' => 'https://www.linkedin.com/shareArticle?url=' . $url . '&title=' . $title,
      'ml' => 'mailto:?subject=' . $title . '&body=' . $url
    );

    $share_buttons = '<ul class="social">';

    $share_icons = [
      'fb' => get_stylesheet_directory_uri() . '/public/social/facebook.png',
      'x' => get_stylesheet_directory_uri() . '/public/social/x.png',
      'ln' => get_stylesheet_directory_uri() . '/public/social/linkedin.png',
      'ml' => get_stylesheet_directory_uri() . '/public/social/mail.png',
    ];

    foreach ($social_networks as $network => $share_url) {
      $img = '<img src="' . $share_icons[$network] . '" alt="' . $network . '" />';

      $share_buttons .= '<li><a href="' . $share_url . '" target="_blank" rel="noopener">' . $img . '</a></li>';
    }

    $share_buttons .= '</ul>';

    return $share_buttons;
  }

  public function get_timber_menu($menu_id) {
    return Timber::get_menu($menu_id);
  }

  public function get_resources($number = 3) {
    return Timber::get_posts([
      'posts_per_page' => $number
    ]);
  }
}