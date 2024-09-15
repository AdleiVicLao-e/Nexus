let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isAudioContextUnlocked = false;
let isDialogueOngoing = false; // Flag to prevent triggering another dialogue during ongoing speech

// Create a PIXI application
const app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
});

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

    // Make the model interactive, but prevent dragging
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

    // Add event listeners to the model for interaction without dragging
    live2dModel.on('pointerdown', handleInteraction); // Handle touch/click events

    // Interaction handler: Trigger speech or other actions when clicked or tapped
    function handleInteraction(event) {
        event.stopPropagation(); // Prevent default behavior

        // Prevent triggering a new dialogue if one is ongoing
        if (isDialogueOngoing) {
            console.log("Dialogue is already ongoing. Please wait.");
            return;
        }

        // Unlock AudioContext for iOS if not already unlocked
        unlockAudioContext();

        // Text the VA will say when clicked
        const text = "The sky is blue, the clouds are white, the leaves are green, the sun is bright";

        // Set dialogue as ongoing
        isDialogueOngoing = true;

        // Call the speakText function (handles TTS)
        speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId);
    }
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
function speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    // Unlock AudioContext on iOS if not already unlocked
    unlockAudioContext();

    // Generate audio using speak.js
    generateTTS(text, function (audioData) {
        // Play the generated audio and sync with the model
        playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
    });
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
            isDialogueOngoing = false;
        };

        // Lip sync while the audio is playing
        if (live2dModel) {
            let startTime = Date.now();
            animationIntervalId = setInterval(() => {
                let elapsed = Date.now() - startTime;
                simulateAudioParameterChange();
            }, 150);
        }
    });
}

// Ensure voice list is populated on page load
window.onload = function () {
    populateVoiceList();
};

// Re-populate the voice list when voices are changed
if (typeof window.populateVoiceList === 'function') {
    window.speechSynthesis.onvoiceschanged = window.populateVoiceList;
}
