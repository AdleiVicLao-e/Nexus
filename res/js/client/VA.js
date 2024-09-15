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

// Initialize SpeechSynthesis API
const synth = window.speechSynthesis;

// Unlock AudioContext on user interaction (for iOS support)
function unlockAudioContext() {
    if (!isAudioContextUnlocked && audioContext.state !== 'running') {
        audioContext.resume().then(() => {
            console.log("Audio context resumed.");
            isAudioContextUnlocked = true;
        }).catch(error => {
            console.error("Error unlocking AudioContext:", error);
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
    if ('speechSynthesis' in window) {
        console.log('Using speechSynthesis API for TTS.');

        // Create a new SpeechSynthesisUtterance object with the text
        const utterance = new SpeechSynthesisUtterance(text);

        // Optionally set voice, rate, pitch, etc.
        const voices = synth.getVoices();
        utterance.voice = voices.find(voice => voice.lang === 'en-US') || voices[0];
        utterance.rate = 1; // Speed (0.1 to 10)
        utterance.pitch = 1; // Pitch (0 to 2)

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

            // Mark the dialogue as finished
            isDialogueOngoing = false;
        };

        // Speak the text
        synth.speak(utterance);
        console.log('Speaking:', text);
    } else {
        alert('Your browser does not support Speech Synthesis.');
    }
}

// Ensure voice list is populated on page load
window.onload = function () {
    populateVoiceList();
};

// Function to populate the list of voices
function populateVoiceList() {
    if ('speechSynthesis' in window) {
        const voices = speechSynthesis.getVoices();
        console.log('Available voices:', voices);
    }
}
