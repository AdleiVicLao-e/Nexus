let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};

// Create a PIXI application
const app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
});

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

    // Add event listeners to the model
    live2dModel.on('pointerdown', handleInteraction); // Handle touch/click events

    function handleInteraction(event) {
        event.stopPropagation(); // Prevent default behavior

        // Directly create and speak an utterance
        const utterance = new SpeechSynthesisUtterance('The sky is blue, the clouds are white, the leaves are green, the sun is bright');
        utterance.onstart = function () {
            if (live2dModel) {
                let startTime = Date.now();
                animationIntervalId = setInterval(() => {
                    let elapsed = Date.now() - startTime;
                    simulateAudioParameterChange();
                }, 150);
            }
        };

        utterance.onend = function () {
            clearInterval(animationIntervalId);
            animationIntervalId = null;

            if (live2dModel) {
                live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
                live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
            }
        };

        speechSynthesis.speak(utterance);
    }

});

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

// Define getSelectedVoice to select a voice for speech synthesis
window.getSelectedVoice = function() {
    const voices = window.speechSynthesis.getVoices();
    return voices.find(voice => voice.lang === 'en-US') || voices[0];
};

function speakText(text) {
    const selectedVoice = window.getSelectedVoice();
    if (!selectedVoice) {
        console.warn("Voice not selected or not available.");
        const voices = window.speechSynthesis.getVoices();
        console.log("Available voices:", voices);
        return;
    }

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.voice = selectedVoice;
    utterance.lang = selectedVoice.lang;

    if (typeof window.setTTSParameters === 'function') {
        window.setTTSParameters(utterance);
    }

    utterance.onstart = function () {
        if (live2dModel) {
            let startTime = Date.now();
            animationIntervalId = setInterval(() => {
                let elapsed = Date.now() - startTime;
                simulateAudioParameterChange();
            }, 150);
        }
    };

    utterance.onend = function () {
        clearInterval(animationIntervalId);
        animationIntervalId = null;

        if (live2dModel) {
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
        }
    };

    speechSynthesis.speak(utterance);
}

// Ensure voice list is populated after page load
window.onload = function () {
    if (typeof window.populateVoiceList === 'function') {
        window.populateVoiceList();
    }
};

// Re-populate the voice list when voices are changed
if (typeof window.populateVoiceList === 'function') {
    window.speechSynthesis.onvoiceschanged = window.populateVoiceList;
}
