<?php
class Lunawolf_Settings {

	private static $instance;

	public function __construct() {
		// Prevent creating multiple instances (Singleton pattern)
	}

	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function settings($settings, $count): array
	{
    if (!$settings) return [
      'block_id' => 'block-' . $count,
			'styles' => '',
      'bg_color' => '',
      'bg_opacity' => '',
      'text_color' => ''
    ];

		$block_id = isset($settings['block_id']) && $settings['block_id'] ? $settings['block_id'] : 'block-' . $count;
		$settings['block_id'] = $block_id;

		$styles = $this->_block_style_settings($settings);

    $bg_color   = isset($settings['bg_color']) && $settings['bg_color'] ? $settings['bg_color'] : '';
    $bg_opacity = isset($settings['bg_opacity']) && ($settings['bg_opacity'] || $settings['bg_opacity'] === 0) ? $settings['bg_opacity'] : '';
    $text_color = isset($settings['text_color']) && $settings['text_color'] ? $settings['text_color'] : '';
    $section_animation = isset($settings['section_animation']) && $settings['section_animation'] ? $settings['section_animation'] : '';
    $animation_type = isset($settings['animation_type']) && $settings['animation_type'] ? $settings['animation_type'] : '';
    $animation_duration = isset($settings['animation_duration']) && $settings['animation_duration'] ? $settings['animation_duration'] : '';
    $animation_delay = isset($settings['animation_delay']) && is_numeric($settings['animation_delay']) ? $settings['animation_delay'] : 0;

		return [
			'block_id' => $block_id,
			'styles' => $styles ? '<style>' . $styles . '</style>' : '',
			'bg_color' => $bg_color,
			'bg_opacity' => $bg_opacity,
			'text_color' => $text_color,
			'section_animation' => $section_animation,
			'animation_type' => $animation_type,
			'animation_duration' => $animation_duration,
			'animation_delay' => $animation_delay,
		];
	}

	protected function _block_style_settings($settings): string
	{
		$property_names = [
			'margin_top' => 'margin-top',
			'margin_bottom' => 'margin-bottom',
			'padding_top' => 'padding-top',
			'padding_bottom' => 'padding-bottom',
		];

		$styles = '';

		foreach ($property_names as $key => $value) {
			if (isset($settings[$key]) && is_numeric($settings[$key]))
				$styles .= '#' . $settings['block_id'] . '{' . $value . ':' . $settings[$key] . 'px;}';
		}

		return $styles;
	}
}