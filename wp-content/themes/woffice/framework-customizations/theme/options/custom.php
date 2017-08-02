<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'custom-code' => array(
		'title'   => __( 'Custom Code', 'woffice' ),
		'type'    => 'tab',
		'options' => array(
			'css-box' => array(
				'title'   => __( 'Custom CSS', 'woffice' ),
				'type'    => 'box',
				'options' => array(
					'custom_css' => array(
						'label' => __( 'Type your custom CSS here ', 'woffice' ),
						'type'  => 'textarea',
						'desc' => __('No need to set the tags (&lt;style&gt;&lt;/style&gt;). It is better to change the CSS changes here than editing the theme files ;) or you can create a child theme if you have a lot of changes.','woffice'),
						'help' => __('Without the HTML tags "style" please','woffice'),
					),
				)
			),
            'js-box' => array(
                'title'   => __( 'Custom Javascript', 'woffice' ),
                'type'    => 'box',
                'options' => array(
                    'custom_js' => array(
                        'label' => __( 'Type your custom Jquery/Javascript here ', 'woffice' ),
                        'type'  => 'textarea',
                        'desc' => __('No need to set the tags (&lt;script&gt;&lt;/script&gt;). It will understands the "$", all is wrapped in a Jquery function.','woffice'),
                        'help' => __('Without the HTML tags "script" please','woffice'),
                    ),
                    'minified_js'    => array(
                        'label' => __( 'Use Minified files ?', 'woffice' ),
                        'desc'  => __( 'Whether we use a minified file or not for the theme\'s custom scripts. Needs to be off if you\'ve made changes on your side.', 'woffice' ),
                        'type'         => 'switch',
                        'right-choice' => array(
                            'value' => 'yes',
                            'label' => __( 'Yes', 'woffice' )
                        ),
                        'left-choice'  => array(
                            'value' => 'no',
                            'label' => __( 'No', 'woffice' )
                        ),
                        'value'        => 'yes',
                    ),
                )
            ),
		)
	)
);