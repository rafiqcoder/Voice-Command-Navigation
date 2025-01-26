<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

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
        return ['general'];
    }

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
            'button_style',
            [
                'label' => __('Button Style', 'voice-command-plugin'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'flat' => __('Flat', 'voice-command-plugin'),
                    'outlined' => __('Outlined', 'voice-command-plugin'),
                    '3d' => __('3D', 'voice-command-plugin'),
                ],
                'default' => 'flat',
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Button Color', 'voice-command-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0073aa',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'voice-command-plugin'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
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
            Group_Control_Typography::get_type(),
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

        // Define styles based on the selected button style
        $style = '';
        switch ($settings['button_style']) {
            case 'outlined':
                $style = 'background-color: transparent; border: 2px solid ' . esc_attr($settings['button_color']) . ';';
                break;
            case '3d':
                $style = 'box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);';
                break;
            case 'flat':
            default:
                $style = 'background-color: ' . esc_attr($settings['button_color']) . ';';
                break;
        }

        $size_class = $settings['button_size'];

        ?>
        <div style="text-align: <?php echo esc_attr($settings['alignment']); ?>;">
            <button id="voice-command-btn" class="<?php echo esc_attr($size_class); ?>" style="
                <?php echo $style; ?>
                color: <?php echo esc_attr($settings['text_color']); ?>;
                padding: <?php echo ($size_class === 'large' ? '15px 20px' : ($size_class === 'small' ? '5px 10px' : '10px 15px')); ?>;
                border-radius: 5px;
                border: none;
                cursor: pointer;
            ">
                <?php echo esc_html($settings['button_text']); ?>
            </button>
        </div>
        <?php

        if (!empty($settings['custom_css'])) {
            echo '<style>' . $settings['custom_css'] . '</style>';
        }
    }
}
