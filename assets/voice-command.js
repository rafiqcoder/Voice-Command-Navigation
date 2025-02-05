document.addEventListener('DOMContentLoaded',() => {
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
            icon.style.color = 'red'; // Change icon color when listening
        };

        icon.addEventListener('click',(event) => {
            event.stopPropagation(); // Prevent click event from bubbling
            if (recognition.running) {
                recognition.stop();
                console.log('Stopped listening.');
                icon.style.color = ''; // Reset icon color
            } else {
                startRecognition();
            }
        });

        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.toLowerCase();
            console.log('Command received:',command);

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
            console.error('Recognition error:',event.error);
            alert('There was an error with voice recognition. Please try again.');
            icon.style.color = ''; // Reset icon color
        };

        recognition.onend = () => {
            console.log('Voice recognition ended.');
            icon.style.color = ''; // Reset icon color
        };
    } else {
        console.error('Speech recognition not supported.');
        btn.style.display = 'none';
        alert('Your browser does not support voice commands.');
    }

    // Tooltip Hover Effect
    btn.addEventListener('mouseover',() => {
        tooltip.style.visibility = 'visible';
    });

    btn.addEventListener('mouseout',() => {
        tooltip.style.visibility = 'hidden';
    });
});
