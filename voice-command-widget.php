
<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

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
                'label' => __('Tooltip Text', 'voice-command-plugin'),
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
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'voice-command-plugin'),
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

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $size_class = $settings['button_size'];
        ?>

        <div style="display: flex; justify-content: center;">
            <button id="voice-command-btn" class="<?php echo esc_attr($size_class); ?>" style="
                background-color: <?php echo esc_attr($settings['button_color']); ?>;
                border: none;
                border-radius: 50%;
                width: <?php echo ($size_class === 'large' ? '60px' : ($size_class === 'small' ? '40px' : '50px')); ?>;
                height: <?php echo ($size_class === 'large' ? '60px' : ($size_class === 'small' ? '40px' : '50px')); ?>;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            ">

                <span class="tooltip-text" style="
                    visibility: hidden;
                    background-color: black;
                    color: white;
                    text-align: center;
                    border-radius: 5px;
                    padding: 5px;
                    position: absolute;
                    bottom: 120%;
                    left: 50%;
                    transform: translateX(-50%);
                    white-space: nowrap;
                    font-size: 12px;
                ">
                    <?php echo esc_html($settings['button_text']); ?>
                </span>

                <i id="voice-command-icon" class="<?php echo esc_attr($settings['button_icon']['value']); ?>" style="
                    font-size: <?php echo ($size_class === 'large' ? '24px' : ($size_class === 'small' ? '16px' : '20px')); ?>;
                    color: <?php echo esc_attr($settings['icon_color']); ?>;
                "></i>

            </button>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('voice-command-btn');
            const icon = document.getElementById('voice-command-icon');
            const tooltip = btn.querySelector('.tooltip-text');

            if (!btn || !icon) {
                console.error('Voice command button or icon not found in the DOM.');
                return;
            }

            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
                recognition.lang = 'en-US';
                recognition.continuous = true;
                recognition.interimResults = false;

                const startRecognition = () => {
                    recognition.start();
                    console.log('Listening started...');
                    icon.style.color = 'red';
                };

                icon.addEventListener('click', (event) => {
                    event.stopPropagation();
                    if (recognition.running) {
                        recognition.stop();
                        console.log('Stopped listening.');
                        icon.style.color = '<?php echo esc_attr($settings['icon_color']); ?>';
                    } else {
                        startRecognition();
                    }
                });

                recognition.onresult = (event) => {
                    const command = event.results[0][0].transcript.toLowerCase();
                    console.log('Command received:', command);

                    let found = false;
                    vcpLinks.forEach(link => {
                        if (command.includes(link.command.toLowerCase())) {
                            window.location.href = link.url;
                            found = true;
                        }
                    });

                    if (!found) {
                        alert(`Unrecognized command: "${command}"`);
                    }
                };

                recognition.onerror = (event) => {
                    console.error('Recognition error:', event.error);
                    alert('There was an error with voice recognition. Please try again.');
                    icon.style.color = '<?php echo esc_attr($settings['icon_color']); ?>';
                };

                recognition.onend = () => {
                    console.log('Voice recognition ended.');
                    icon.style.color = '<?php echo esc_attr($settings['icon_color']); ?>';
                };
            } else {
                console.error('Speech recognition not supported.');
                btn.style.display = 'none';
                alert('Your browser does not support voice commands.');
            }

            // Tooltip Hover Effect
            btn.addEventListener('mouseover', () => {
                tooltip.style.visibility = 'visible';
            });

            btn.addEventListener('mouseout', () => {
                tooltip.style.visibility = 'hidden';
            });
        });
        </script>

        <?php
    }
}
