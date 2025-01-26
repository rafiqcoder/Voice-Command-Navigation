document.addEventListener('DOMContentLoaded',() => {
    const btn = document.getElementById('voice-command-btn');

    if (!btn) {
        console.error('Voice command button not found in the DOM.');
        return;
    }
    console.log(vcpLinks);
    if (!Array.isArray(vcpLinks)) {
        console.error('vcpLinks is not an array. Please check the backend implementation.');
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
            btn.textContent = 'Listening...';
            btn.disabled = true;

            setTimeout(() => {
                recognition.stop();
                console.log('Stopped listening after 10 seconds.');
                btn.textContent = 'Start Voice Command';
                btn.disabled = false;
            },10000);
        };

        startRecognition();

        btn.addEventListener('click',() => {
            recognition.stop();
            btn.textContent = 'Stopped. Restarting...';
            btn.disabled = true;

            setTimeout(() => {
                startRecognition();
            },500);
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

            btn.textContent = 'Start Voice Command';
            btn.disabled = false;
        };

        recognition.onerror = (event) => {
            console.error('Recognition error:',event.error);
            alert('There was an error with voice recognition. Please try again.');
            btn.textContent = 'Start Voice Command';
            btn.disabled = false;
        };

        recognition.onend = () => {
            console.log('Voice recognition ended.');
            btn.textContent = 'Start Voice Command';
            btn.disabled = false;
        };
    } else {
        console.error('Speech recognition not supported.');
        btn.style.display = 'none';
        alert('Your browser does not support voice commands.');
    }
});
