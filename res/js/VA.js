let live2dModel;
let mouthMotionData;  // Variable to hold the mouth motion data
let animationIntervalId;  // Variable to store the interval ID
let audioParameterValues = {};  // Object to hold the current values for audio parameters

// Initialize PIXI application and load the Live2D model
const app = new PIXI.Application({
    view: document.getElementById('canvas'),
    autoStart: true,
    resizeTo: window,
    backgroundColor: 0xFFFFFF // White background
});

// Load the Live2D model
PIXI.live2d.Live2DModel.from('./assets/VAmodel/VA Character.model3.json').then(model => {
    live2dModel = model;
    live2dModel.scale.set(0.5); // Adjust the model size
    live2dModel.anchor.set(0.5, 0.5);
    live2dModel.position.set(app.screen.width / 2, app.screen.height / 2);
    app.stage.addChild(live2dModel);

    // Load the motion sync data from the JSON file
    PIXI.Loader.shared
        .add('mouthMotion', './assets/VAmodel/VA Character.motionsync3.json')
        .load((loader, resources) => {
            mouthMotionData = resources.mouthMotion ? resources.mouthMotion.data : null;  // Save the motion data
            applyMotionData(); // Apply motion data after loading
        });
});

// Function to apply motion data
function applyMotionData() {
    if (mouthMotionData) {
        const settings = mouthMotionData.Settings[0];
        const mappings = settings.Mappings;

        // Initialize audio parameter values
        settings.AudioParameters.forEach(param => {
            audioParameterValues[param.Id] = 0; // Ensure audioParameterValues is initialized
        });

        // Function to update model parameters based on audio parameters
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

        // Update model parameters every 150ms
        setInterval(updateModelParameters, 150);
    }
}

// Simulate audio parameter changes (for testing)
function simulateAudioParameterChange() {
    // Example simulation
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// Function to handle the click event for the speak button
function setupSpeakButton() {
    const speakButton = document.getElementById('speakButton');
    if (speakButton) {
        speakButton.addEventListener('click', function () {
            speakText('The sky is blue, the clouds are white, the leaves are green, the sun is bright');
        });
    } else {
        console.error('Speak button not found');
    }
}

// Function to speak the provided text with the selected voice and parameters
function speakText(text) {
    const selectedVoice = window.getSelectedVoice();
    if (!selectedVoice) {
        console.warn("Voice not selected or not available.");
        // Log available voices for debugging
        const voices = window.speechSynthesis.getVoices();
        console.log("Available voices:", voices);
        return;
    }

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.voice = selectedVoice;
    utterance.lang = selectedVoice.lang;

    // Set default values for pitch, rate, and volume
    window.setTTSParameters(utterance);

    utterance.onstart = function () {
        if (live2dModel) {
            let startTime = Date.now();

            // Start the mouth animation when TTS starts
            animationIntervalId = setInterval(() => {
                let elapsed = Date.now() - startTime;
                simulateAudioParameterChange();  // Simulate audio parameter changes
            }, 150);  // Adjust the interval time as necessary
        } else {
            console.error("Live2D model not found");
        }
    };

    utterance.onend = function () {
        clearInterval(animationIntervalId);
        animationIntervalId = null;

        // Reset mouth parameters to default
        if (live2dModel) {
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
        }
    };

    speechSynthesis.speak(utterance);
}

// Initialize voice list and set up button
window.onload = function () {
    if (typeof window.populateVoiceList === 'function') {
        window.populateVoiceList(); // Populate voice list on load
    } else {
        console.error('populateVoiceList function is not defined');
    }

    setupSpeakButton(); // Set up the speak button event listener
};

// Set the voice selection change event to update the selected voice
if (typeof window.populateVoiceList === 'function') {
    window.speechSynthesis.onvoiceschanged = window.populateVoiceList;
}
