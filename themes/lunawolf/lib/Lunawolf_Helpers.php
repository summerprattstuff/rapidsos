<?php
class Lunawolf_Helpers {

	private static $instance;

	public function __construct() {
		add_filter('timber/twig/functions', function ($functions) {
			$functions['get_testimonials'] = [
				'callable' => [$this, 'get_testimonials']
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
}