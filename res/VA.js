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
PIXI.live2d.Live2DModel.from('./VAmodel/VA Character.model3.json').then(model => {
    live2dModel = model;
    live2dModel.scale.set(0.5); // Adjust the model size
    live2dModel.anchor.set(0.5, 0.5);
    live2dModel.position.set(app.screen.width / 2, app.screen.height / 2);
    app.stage.addChild(live2dModel);

    // Load the motion sync data from the JSON file
    PIXI.Loader.shared
        .add('mouthMotion', './VAmodel/VA Character.motionsync3.json')
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
            audioParameterValues[param.Id] = 0;
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

        // Example: Update model parameters every 100ms
        setInterval(updateModelParameters, 150);
    }
}

// Function to simulate audio parameter changes (for testing)
function simulateAudioParameterChange() {
    // Example simulation
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// TTS Functionality
function speakText(text) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US';

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

// Event Listener for Speak Button
document.getElementById('speakButton').addEventListener('click', function () {
    speakText('The sky is blue, the clouds are white, the leaves are green, the sun is bright');
});
