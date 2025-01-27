<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

class Voice_Command_Widget extends \Elementor\Widget_Base {

    // Widget name
    public function get_name() {
        return 'voice_command_widget';
    }

    // Widget title
    public function get_title() {
        return __('Voice Command Button', 'voice-command-plugin');
    }

    // Widget icon
    public function get_icon() {
        return 'eicon-microphone';
    }

    // Widget category
    public function get_categories() {
        return ['general'];
    }

    // Widget controls
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'voice-command-plugin'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'voice-command-plugin'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Start Voice Command', 'voice-command-plugin'),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Button Background Color', 'voice-command-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} #voice-command-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'voice-command-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} #voice-command-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_size',
            [
                'label' => __('Button Size', 'voice-command-plugin'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'small' => __('Small', 'voice-command-plugin'),
                    'medium' => __('Medium', 'voice-command-plugin'),
                    'large' => __('Large', 'voice-command-plugin'),
                ],
                'default' => 'medium',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'voice-command-plugin'),
                'selector' => '{{WRAPPER}} #voice-command-btn',
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label' => __('Alignment', 'voice-command-plugin'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'voice-command-plugin'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'voice-command-plugin'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'voice-command-plugin'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_icon',
            [
                'label' => __('Icon', 'voice-command-plugin'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-microphone',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label' => __('Icon Position', 'voice-command-plugin'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'before' => __('Before Text', 'voice-command-plugin'),
                    'after' => __('After Text', 'voice-command-plugin'),
                ],
                'default' => 'before',
            ]
        );

        $this->add_control(
            'custom_css',
            [
                'label' => __('Custom CSS', 'voice-command-plugin'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => __('Add custom CSS styles for this button.', 'voice-command-plugin'),
            ]
        );

        $this->end_controls_section();
    }

protected function render() {
    $settings = $this->get_settings_for_display();

    // Render the icon HTML
    $icon_html = '';
    if (!empty($settings['button_icon']['value'])) {
        $icon_html = \Elementor\Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']);
    }

    // Determine the button size class
    $size_class = $settings['button_size'];

    // Add custom CSS if provided
    if (!empty($settings['custom_css'])) {
        echo '<style>' . $settings['custom_css'] . '</style>';
    }

    ?>
    <div style="text-align: <?php echo esc_attr($settings['alignment']); ?>;">
        <button id="voice-command-btn" class="<?php echo esc_attr($size_class); ?>" style="
            background-color: <?php echo esc_attr($settings['button_color']); ?>;
            color: <?php echo esc_attr($settings['text_color']); ?>;
            padding: <?php echo ($size_class === 'large' ? '15px 20px' : ($size_class === 'small' ? '5px 10px' : '10px 15px')); ?>;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        " >
            <?php if ($settings['icon_position'] === 'before') echo $icon_html; ?>
            <span><?php echo esc_html($settings['button_text']); ?></span>
            <?php if ($settings['icon_position'] === 'after') echo $icon_html; ?>
        </button>
    </div>
    
    <?php
}

