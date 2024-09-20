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

// Simulate mouth movement based on random audio parameter values (for testing purposes)
function simulateAudioParameterChange() {
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// TTS logic for speaking text and syncing with the model
function speakText(script, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    // Generate audio using speak.js
    generateTTS(script, function (audioData) {
        // Play the generated audio and sync with the model
        playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
    });
}

function generateTTS(script, callback) {
    if (!speakWorker) {
        console.error("speakWorker has not been initialized.");
        return;
    }

    const message = {
        text: script,
        amplitude: 200,   // Volume (0-200)
        wordgap: 1,       // Gap between words
        pitch: 60,        // Pitch (0-100)
        speed: 185,       // Speed (words per minute)
        voice: 'en',      // Language code
        base64: true      // Ensure this is set to true to get base64-encoded output
    };

    speakWorker.postMessage(message);

    speakWorker.onmessage = function (event) {
        callback(event.data);
    };
}

// Function to play audio in the browser and synchronize it with the model
function playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    // Decode base64 audio data to binary
    let binaryString = window.atob(audioData);
    let len = binaryString.length;
    let bytes = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }

    // Decode the audio data into an audio buffer
    audioContext.decodeAudioData(bytes.buffer, function (buffer) {
        // Create an audio source
        let source = audioContext.createBufferSource();
        source.buffer = buffer;
        source.connect(audioContext.destination);
        source.start(0);

        // Start lip sync when audio starts
        source.onended = function () {
            clearInterval(animationIntervalId);
            animationIntervalId = null;

            // Reset the mouth movement
            if (live2dModel) {
                live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
                live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
            }

            // Mark the dialogue as finished
            isSpeaking = false;
        };

        // Lip sync while the audio is playing
        if (live2dModel) {
            animationIntervalId = setInterval(() => {
                simulateAudioParameterChange();
            }, 150);
        }
    }).then(function (r) {
    });
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
    alert("qr scanned");
    if (!scriptData) {
        alert('Scripts data is not loaded yet.');
        return;
    }
    // Find the script based on artifact ID
    scriptInfo = scriptData.scripts[artifactId];
    if (scriptInfo) {
        script = scriptInfo.script;
    } else {
        alert('No script found for artifact ID: ' + artifactId);
    }
}

// Handle tap interaction (trigger speech)
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior

    // If already speaking, ignore further taps
    if (isSpeaking) return;

    // Check if the device is running iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    // const text = "The sky is blue, the clouds are white, the leaves are green, the sun is bright";
    unlockAudioContext();
    isSpeaking = true;
    if (isIOS) {
        speakWithNativeTTS(script, live2dModel, simulateAudioParameterChange, animationIntervalId); // Use native TTS for iOS
    } else {
        speakText(script, live2dModel, simulateAudioParameterChange, animationIntervalId);
    }
}

// Function to speak using Speech Synthesis API
function speakWithNativeTTS(script, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(script);

    const voices = synth.getVoices();
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    let selectedVoice;

    if (isIOS) {
        selectedVoice = voices.find(voice => voice.name === 'Fred');
    }

    if (!selectedVoice) {
        selectedVoice = voices.find(voice => voice.lang === 'en-US');
    }

    if (selectedVoice) {
        utterance.voice = selectedVoice;
    }


    // Lip sync while the speech is happening
    utterance.onstart = function() {
        animationIntervalId = setInterval(simulateAudioParameterChange, 150);
    };

    // Stop lip sync when speech ends
    utterance.onend = function() {
        clearInterval(animationIntervalId);
        animationIntervalId = null;

        // Reset the mouth movement
        if (live2dModel) {
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
        }
    };
    utterance.onend = function () {
        isSpeaking = false;
    };

    synth.speak(utterance);
}

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};
