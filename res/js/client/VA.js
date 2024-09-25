let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isAudioContextUnlocked = false;
let isSpeaking = false; // Track if speech is active
let scriptData; // Store the loaded script data
let script;

// Variables to track dragging state
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag
let speakWorker;

try {
    speakWorker = new Worker('res/js/client/speakWorker.js');
} catch(e) {
    console.log('speak.js warning: no worker support');
}

// Create a PIXI application
let app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
}), scriptInfo;

// Load the VA script from assets/VAmodel/script.json
fetch('./assets/VAmodel/script.json')
    .then(response => response.json())
    .then(data => {
        scriptData = data; // Store the script data
        scriptInfo = scriptData.scripts[1302];
        script = scriptInfo.script;
    })
    .catch(error => console.error('Error loading script:', error));

// Load and display the Live2D model
let audioContext = null; // Moved initialization to be inside the unlockAudioContext function

// Unlock AudioContext on user interaction (for iOS support)
function unlockAudioContext() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)(); // Create AudioContext when unlocking
    }

    if (!isAudioContextUnlocked && audioContext.state !== 'running') {
        audioContext.resume().then(() => {
            console.log("Audio context resumed.");
            isAudioContextUnlocked = true;
        });
    }
}

PIXI.live2d.Live2DModel.from('./assets/VAmodel/VA Character.model3.json').then(model => {
    live2dModel = model;

    live2dModel.scale.set(0.15);
    live2dModel.anchor.set(0.5, 0.5);
    live2dModel.position.set(app.screen.width / 2, app.screen.height / 2);

    // Make the model interactive
    live2dModel.interactive = true;
    live2dModel.buttonMode = true; // Change cursor to pointer on hover

    app.stage.addChild(live2dModel);

    // Load mouth motion data
    PIXI.Loader.shared
        .add('mouthMotion', './assets/VAmodel/VA Character.motionsync3.json')
        .load((loader, resources) => {
            mouthMotionData = resources.mouthMotion ? resources.mouthMotion.data : null;
            applyMotionData();
        });

    // Event listeners for tap interaction
    live2dModel.on('pointerdown', onPointerDown);
    live2dModel.on('pointerup', onPointerUp);
    live2dModel.on('pointerupoutside', onPointerUp);
});

// Synchronize lip movement based on the motion data
function applyMotionData() {
    if (mouthMotionData) {
        const settings = mouthMotionData.Settings[0];
        const mappings = settings.Mappings;

        settings.AudioParameters.forEach(param => {
            audioParameterValues[param.Id] = 0;
        });

        function updateModelParameters() {
            for (let audioParamId in audioParameterValues) {
                const value = audioParameterValues[audioParamId];
                const mapping = mappings.find(m => m.Id === audioParamId);
                if (mapping) {
                    mapping.Targets.forEach(target => {
                        live2dModel.internalModel.coreModel.setParameterValueById(target.Id, target.Value * value);
                    });
                }
            }
        }
        // Update the mouth motion parameters at regular intervals
        setInterval(updateModelParameters, 150);
    }
}

function simulateAudioParameterChange() {
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// TTS logic for speaking text and syncing with the model
function speakText(script) {
    console.log("Starting speakText function with script:", script);

    // Generate audio using speak.js
    generateTTS(script, function (audioData) {
        if (audioData) {
            console.log("Received valid audio data from worker, playing TTS.");
            // Play the generated audio and sync with the model
            playTTS(audioData);
        } else {
            console.error("No audio data received from worker.");
        }
    });
}

// Function to generate TTS using the worker
function generateTTS(script, callback) {
    if (!speakWorker) {
        console.error('SpeakWorker is not initialized');
        return;
    }

    console.log("Posting message to speakWorker for TTS generation");

    const message = {
        text: script,
        amplitude: 200,
        wordgap: 1,
        pitch: 90,
        speed: 185,
        voice: 'vi',
        base64: true
    };

    // Send the TTS request to the worker
    speakWorker.postMessage(message);

    // Listen for the worker response
    speakWorker.onmessage = function(event) {
        const audioData = event.data;
        console.log("Received message from speakWorker:", audioData ? audioData.substring(0, 30) + '...' : "No audio data");

        if (audioData && typeof audioData === 'string') {
            callback(audioData);
        } else {
            console.error('Invalid audio data received from worker', audioData);
        }
    };

    // Add error handling for worker messages
    speakWorker.onerror = function(error) {
        console.error('Error in speakWorker:', error.message);
    };
}

// Function to play audio in the browser and synchronize it with the model
function playTTS(audioData) {
    console.log("Attempting to play TTS audio");

    // Convert base64 to ArrayBuffer
    const binaryString = window.atob(audioData);
    const len = binaryString.length;
    const bytes = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }

    // Decode audio data and handle playback
    audioContext.decodeAudioData(bytes.buffer)
        .then(buffer => {
            const source = audioContext.createBufferSource();
            source.buffer = buffer;
            source.connect(audioContext.destination);
            source.start(0);

            console.log("Audio is playing, syncing lip motion");

            // Start lip sync when audio starts
            source.onended = function() {
                console.log("Audio playback ended, stopping lip sync");
                clearInterval(animationIntervalId);
                animationIntervalId = null;

                // Reset mouth movement
                if (live2dModel) {
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
                }

                isSpeaking = false; // Mark speaking as finished
            };

            // Lip sync while the audio is playing
            if (live2dModel) {
                animationIntervalId = setInterval(simulateAudioParameterChange, 150);
                console.log("Lip sync started with interval ID:", animationIntervalId);
            }
        })
        .catch(error => {
            console.error('Error decoding audio data:', error);
        });
}

// Function to speak using Speech Synthesis API for iOS
function speakWithNativeTTS(script) {
    console.log("Starting speakWithNativeTTS function with script:", script);

    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(script);

    const voices = synth.getVoices();
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    let selectedVoice;

    if (isIOS) {
        selectedVoice = voices.find(voice => voice.name === 'Fred');
        console.log("Selected Fred voice for iOS");
    }

    if (!selectedVoice) {
        selectedVoice = voices.find(voice => voice.lang === 'en-US');
        console.log("Selected default en-US voice");
    }

    if (selectedVoice) {
        utterance.voice = selectedVoice;
    }

    // Lip sync while the speech is happening
    utterance.onstart = function() {
        console.log("Speech started, beginning lip sync");
        animationIntervalId = setInterval(simulateAudioParameterChange, 150);
    };

    // Stop lip sync when speech ends
    utterance.onend = function() {
        console.log("Speech ended, stopping lip sync");
        clearInterval(animationIntervalId);
        animationIntervalId = null;

        // Reset the mouth movement
        if (live2dModel) {
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
        }

        isSpeaking = false; // Mark speaking as finished
    };

    synth.speak(utterance);
}

// Track the starting point when the user taps
function onPointerDown(event) {
    startPoint = event.data.global;
    isDragging = false;
}

// Event handler for pointer up (tap detection)
function onPointerUp(event) {
    const currentPoint = event.data.global;
    const distance = Math.sqrt(
        Math.pow(currentPoint.x - startPoint.x, 2) + Math.pow(currentPoint.y - startPoint.y, 2)
    );

    // If the drag distance is less than the threshold, consider it a tap
    if (!isDragging && distance < dragThreshold) {
        handleTap(event);
    }

    // Reset dragging state
    isDragging = false;
}

function handleArtifact(artifactId) {
    if (!scriptData) {
        return;
    }
    // Find the script based on artifact ID
    scriptInfo = scriptData.scripts[artifactId];
    if (scriptInfo) {
        script = scriptInfo.script;
    }
}

// Handle tap interaction (trigger speech)
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior

    // If already speaking, ignore further taps
    if (isSpeaking) return;
    unlockAudioContext();

    // Check if the device is running iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    if (isIOS) {
        speakWithNativeTTS(script); // Use native TTS for iOS
    } else {
        isSpeaking = true; // Mark speaking as active
        speakText(script); // Use the TTS worker for non-iOS devices
    }
}

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};
