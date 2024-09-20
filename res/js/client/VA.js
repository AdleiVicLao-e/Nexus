let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isSpeaking = false; // Track if speech is active
let scriptData; // Store the loaded script data

// Variables to track dragging state
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag

// Create a PIXI application
let app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
}), scriptInfo, script;

// Load the VA script from assets/VAmodel/script.json
fetch('./assets/VAmodel/script.json')
    .then(response => response.json())
    .then(data => {
        scriptData = data; // Store the script data
    })
    .catch(error => console.error('Error loading script:', error));

// Load and display the Live2D model
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
        console.error('Scripts data is not loaded yet.');
        return;
    }
    // Find the script based on artifact ID
    scriptInfo = scriptData.scripts[artifactId];
    if (scriptInfo) {
        script = scriptInfo.script;
    } else {
        console.error('No script found for artifact ID:', artifactId);
    }
}

// Handle tap interaction (trigger speech)
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior

    // If already speaking, ignore further taps
    if (isSpeaking || !script) return;

    // Check if the device is running iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    if (isIOS) {
        speakWithNativeTTS(script); // Use native TTS for iOS
    } else {
        speakWithSpeakJS(script);   // Use speak.js for non-iOS devices
    }
}

// Function to speak using Speech Synthesis API
function speakWithNativeTTS(text) {
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);

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

    isSpeaking = true;
    utterance.onend = function () {
        isSpeaking = false;
    };

    synth.speak(utterance);
}

// Function to speak using Speak.js for non-iOS devices
function speakWithSpeakJS(text) {
    const options = {
        pitch: 0.8,
        rate: 0.8,
        volume: 1.0
    };

    speak(text, options);

    isSpeaking = true;
    speakWorker.onmessage = function(event) {
        isSpeaking = false;
    };
}

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};
