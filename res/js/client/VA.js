// va.js

// Global variables
let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isSpeaking = false; // Track if speech is active

// Speech synthesis variables
let synth = window.speechSynthesis;
let voices = [];
let selectedVoice = null;

// Initialize the PIXI application
const app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
});

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

    // Event listeners for tap interaction (optional, can be removed if not needed)
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
                        live2dModel.internalModel.coreModel.setParameterValueById(
                            target.Id,
                            target.Value * value
                        );
                    });
                }
            }
        }

        // Update the mouth motion parameters at regular intervals
        setInterval(updateModelParameters, 100);
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

// Variables to track dragging state (for touch interaction)
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag

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

// Handle tap interaction (trigger speech) - Optional
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior

    // If already speaking, ignore further taps
    if (isSpeaking) return;

    // For testing purposes, you can have the VA speak a default text
    const testText = "Hello! This is a test speech.";
    speakVA(testText);
}

// Load available voices
function loadVoices() {
    voices = synth.getVoices();
    // Choose a default voice, prioritize male English voice
    selectedVoice =
        voices.find(voice => voice.name.includes('Google US English') && voice.name.includes('Male')) ||
        voices.find(voice => voice.lang === 'en-US' && voice.name.includes('Male')) ||
        voices.find(voice => voice.lang === 'en-US') ||
        voices[0];
}

// Ensure voices are loaded
if (synth.onvoiceschanged !== undefined) {
    synth.onvoiceschanged = loadVoices;
} else {
    loadVoices();
}

// Function to speak text using the SpeechSynthesis API
function speakVA(text) {
    if (isSpeaking) {
        console.warn("VA is already speaking.");
        return;
    }

    if (!text) {
        console.error("No text provided for the VA to speak.");
        return;
    }

    let utterance = new SpeechSynthesisUtterance(text);

    // Set voice parameters
    utterance.voice = selectedVoice;
    utterance.pitch = 1.0; // Adjust pitch (0 to 2)
    utterance.rate = 1.0;  // Adjust rate (0.1 to 10)
    utterance.volume = 1.0; // Adjust volume (0 to 1)

    utterance.onstart = () => {
        isSpeaking = true;
        console.log("VA started speaking.");
        startMouthMovement();
    };

    utterance.onend = () => {
        isSpeaking = false;
        console.log("VA finished speaking.");
        stopMouthMovement();
    };

    // Handle errors
    utterance.onerror = (event) => {
        isSpeaking = false;
        console.error("SpeechSynthesis error:", event.error);
        stopMouthMovement();
    };

    // Speak the text
    synth.speak(utterance);
}

// Start mouth movement by updating audio parameters
function startMouthMovement() {
    if (mouthMotionData) {
        // Use regular updates based on motion sync data
        if (!animationIntervalId) {
            animationIntervalId = setInterval(simulateAudioParameterChange, 100); // Update every 100ms
        }
    }
}

// Stop mouth movement
function stopMouthMovement() {
    if (animationIntervalId) {
        clearInterval(animationIntervalId);
        animationIntervalId = null;
    }
}

// Function to fetch the VA script based on artifact name
function getVAScript(artifactName) {
    fetch('assets/VAmodel/script.json')
        .then(response => response.json())
        .then(scriptData => {
            if (scriptData[artifactName]) {
                const scriptText = scriptData[artifactName];
                speakVA(scriptText);
            } else {
                console.error('No script found for artifact:', artifactName);
            }
        })
        .catch(error => console.error('Error fetching VA script:', error));
}

// Expose getVAScript globally, so it can be called from other scripts
window.getVAScript = getVAScript;

// Optional: Stop VA speaking function
function stopVASpeaking() {
    if (isSpeaking) {
        synth.cancel();
        isSpeaking = false;
        stopMouthMovement();
        console.log("VA speech stopped.");
    }
}
