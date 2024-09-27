// Global Variables
let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isAudioContextUnlocked = false;
let isSpeaking = false; // Track if speech is active
let scriptData; // Store the loaded script data

// Dragging State Variables
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag
let speakWorker;

// Initialize Speak Worker
try {
    speakWorker = new Worker('./res/js/client/speakWorker.js');
} catch(e) {
    console.log('speak.js warning: no worker support');
}

// PIXI Application Setup
let app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
}), scriptInfo;

// ScriptManager Object
const ScriptManager = {
    currentScript: '',
    setScript: function(artifactId) {
        if (!scriptData) {
            console.error('ScriptManager: Script data not loaded yet.');
            return;
        }
        const scriptInfo = scriptData.scripts[artifactId];
        if (scriptInfo) {
            this.currentScript = scriptInfo.script;
            console.log(`ScriptManager: Updated script to "${this.currentScript}" for artifactId ${artifactId}`);
        } else {
            console.error(`ScriptManager: No script found for artifactId ${artifactId}`);
        }
    },
    getScript: function() {
        return this.currentScript;
    }
};

// Load the VA script from assets/VAmodel/script.json
fetch('./assets/VAmodel/script.json')
    .then(response => response.json())
    .then(data => {
        scriptData = data; // Store the script data

        // Check for artifactId in localStorage or default to 1302
        let artifactId = localStorage.getItem('selectedArtifactId') || '1302';
        if (scriptData.scripts[artifactId]) {
            ScriptManager.setScript(artifactId);
            console.log(`Initial script loaded: "${ScriptManager.getScript()}"`);
        } else {
            console.error(`No script found for initial artifactId ${artifactId}, defaulting to 1302.`);
            artifactId = '1302';
            ScriptManager.setScript(artifactId);
        }
    })
    .catch(error => console.error('Error loading script:', error));

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

// Load and Display the Live2D Model
PIXI.live2d.Live2DModel.from('./assets/VAmodel/VA Character.model3.json').then(model => {
    live2dModel = model;

    live2dModel.scale.set(0.15);
    live2dModel.anchor.set(0.5, 0.5);
    live2dModel.position.set(app.screen.width / 2, app.screen.height / 2);

    // Make the model interactive
    live2dModel.interactive = true;
    live2dModel.buttonMode = true; // Change cursor to pointer on hover

    app.stage.addChild(live2dModel);

    // Load Mouth Motion Data
    PIXI.Loader.shared
        .add('mouthMotion', './assets/VAmodel/VA Character.motionsync3.json')
        .load((loader, resources) => {
            mouthMotionData = resources.mouthMotion ? resources.mouthMotion.data : null;
            applyMotionData();
        });

    // Event Listeners for Tap Interaction
    live2dModel.on('pointerdown', onPointerDown);
    live2dModel.on('pointerup', onPointerUp);
    live2dModel.on('pointerupoutside', onPointerUp);
});

// Synchronize Lip Movement Based on Motion Data
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

// Simulate Audio Parameter Changes for Lip Sync
function simulateAudioParameterChange() {
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// Generate TTS Using Speak Worker
function generateTTS(text, callback) {
    const message = {
        text: text,
        amplitude: 200,
        wordgap: 1,
        pitch: 20, // Test with the lowest pitch
        speed: 10, // Test with a very slow speed
        voice: 'en',
        base64: true
    };

    if (speakWorker) {
        speakWorker.postMessage(message);
    } else {
        console.error('SpeakWorker is not available.');
        return;
    }

    speakWorker.onmessage = function (event) {
        callback(event.data);
    };
}

// Play TTS Audio in Browser Using AudioContext
function playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    // Check if audioData is a Uint8Array (binary data)
    if (!(audioData instanceof Uint8Array)) {
        console.error("Invalid audio data received:", audioData);
        return;
    }

    // Decode the audio data into an AudioBuffer
    audioContext.decodeAudioData(audioData.buffer)
        .then(buffer => {
            // Create an audio source
            let source = audioContext.createBufferSource();
            source.buffer = buffer;
            source.connect(audioContext.destination);
            source.start(0);

            isSpeaking = true; // Mark that speech is ongoing

            // Start lip sync when audio starts
            source.onended = function () {
                clearInterval(animationIntervalId);
                animationIntervalId = null;
                isSpeaking = false; // Mark that speech is finished

                // Reset the mouth movement
                if (live2dModel) {
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
                }
            };

            // Lip sync while the audio is playing
            if (live2dModel) {
                let startTime = Date.now();
                animationIntervalId = setInterval(() => {
                    simulateAudioParameterChange();
                }, 150);
            }
        })
        .catch(error => {
            console.error("Error decoding audio data:", error);
        });
}

// Speak Text Using Speak Worker or Native TTS
function speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId) {
    // Generate audio using speak.js
    generateTTS(text, function (audioData) {
        // Play the generated audio and sync with the model
        playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
    });
}

// Speak Using Native TTS (Speech Synthesis API)
function speakWithNativeTTS(text) {
    console.log(`speakWithNativeTTS: Speaking script "${text}"`);

    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);

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
        isSpeaking = true; // Mark that speech is ongoing
    };

    // Stop lip sync when speech ends
    utterance.onend = function() {
        console.log("Speech ended, stopping lip sync");
        clearInterval(animationIntervalId);
        animationIntervalId = null;

        // Reset mouth movement
        if (live2dModel) {
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
            live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
        }

        isSpeaking = false; // Mark speaking as finished
    };

    // Start speaking
    synth.speak(utterance);
}

// Handle Artifact by Updating ScriptManager
function handleArtifact(artifactId) {
    ScriptManager.setScript(artifactId);
}

// Handle Tap Interaction (Trigger Speech)
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior
    const currentScript = ScriptManager.getScript();
    alert.log(`handleTap: Current script is "${currentScript}"`);

    // If already speaking, stop the current speech and return
    if (isSpeaking) {
        alert.log("Currently speaking, ignoring tap.");
        return;
    }

    unlockAudioContext();

    // Trigger speech when the model is tapped
    if (window.speechSynthesis && window.speechSynthesis.speak) {
        speakWithNativeTTS(currentScript);
    } else {
        speakText(currentScript, live2dModel, simulateAudioParameterChange, animationIntervalId);
    }
}

// Track the Starting Point When the User Taps
function onPointerDown(event) {
    startPoint = event.data.global;
    isDragging = false;
}

// Event Handler for Pointer Up (Tap Detection)
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

// Stop TTS on Page Unload or Visibility Change
function stopTTS() {
    if (window.speechSynthesis) {
        window.speechSynthesis.cancel(); // Cancel any ongoing speech
        console.log("TTS stopped.");
    }

    if (speakWorker) {
        speakWorker.terminate(); // Terminate the TTS worker
        console.log("TTS worker terminated.");
    }

    clearInterval(animationIntervalId); // Clear lip sync animation
    animationIntervalId = null;

    // Reset mouth movement
    if (live2dModel) {
        live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
        live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
    }

    isSpeaking = false; // Reset the speaking flag
}

// Event Listeners for Page Unload and Visibility Change
window.addEventListener('beforeunload', stopTTS);
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopTTS(); // Stop TTS when the page becomes hidden
    }
});
window.addEventListener('pagehide', stopTTS);
window.addEventListener('blur', stopTTS);
