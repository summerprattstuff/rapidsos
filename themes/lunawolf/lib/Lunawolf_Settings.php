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

	public function settings($settings): array
	{
    if (!$settings) return [
      'block_id' => '',
			'styles' => '',
    ];

		$block_id = isset($settings['block_id']) && $settings['block_id'] ? $settings['block_id'] : '';
		$settings['block_id'] = $block_id;

		$styles = $this->_block_style_settings($settings);

		return [
			'block_id' => $block_id,
			'styles' => $styles ? '<style>' . $styles . '</style>' : '',
		];
	}

	protected function _block_style_settings($settings): string
	{
		$property_names = [
			'spacing_top' => 'margin-top',
			'spacing_bot' => 'margin-bottom'
		];

		$styles = '';

		foreach ($property_names as $key => $value) {
			if (isset($settings[$key]) && is_numeric($settings[$key]))
				$styles .= '#' . $settings['block_id'] . '{' . $value . ':' . $settings[$key] . 'px;}';
		}

		return $styles;
	}
}