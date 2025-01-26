<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Voice_Command_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'voice_command_widget';
    }

    public function get_title() {
        return __('Voice Command Button', 'voice-command-plugin');
    }

    public function get_icon() {
        return 'eicon-microphone';
    }

    public function get_categories() {
        return ['basic'];
    }

    public function get_style_depends() {
        return ['voice-command-widget'];
    }

    protected function _register_controls() {
        // Content Tab Controls
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Button Content', 'voice-command-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'voice-command-plugin'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Start Voice Command', 'voice-command-plugin'),
                'placeholder' => __('Enter button text', 'voice-command-plugin'),
            ]
        );

        $this->end_controls_section();

        // Style Tab Controls
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Button Style', 'voice-command-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Text Color', 'voice-command-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .voice-command-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'voice-command-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .voice-command-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'voice-command-plugin'),
                'selector' => '{{WRAPPER}} .voice-command-button',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'voice-command-plugin'),
                'selector' => '{{WRAPPER}} .voice-command-button',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'voice-command-plugin'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .voice-command-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Start Voice Command', 'voice-command-plugin');

        echo '<button id="voice-command-btn" class="voice-command-button">' . esc_html($button_text) . '</button>';
    }

    public function get_script_depends() {
        return ['voice-command-js'];
    }
}

// Register the widget
function register_voice_command_widget($widgets_manager) {
    require_once(__DIR__ . '/voice-command-widget.php');
    $widgets_manager->register_widget_type(new \Voice_Command_Widget());
}
add_action('elementor/widgets/register', 'register_voice_command_widget');
