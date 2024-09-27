var live2dModel;
var mouthMotionData;
var animationIntervalId;
var audioParameterValues = {};
var isAudioContextUnlocked = false;
var isSpeaking = false; // Track if speech is active
var scriptData; // Store the loaded script data
var script;

// Variables to track dragging state
var isDragging = false;
var startPoint = { x: 0, y: 0 };
var dragThreshold = 10; // Minimum movement to qualify as a drag
var speakWorker;

try {
    speakWorker = new Worker('./res/js/client/speakWorker.js');
} catch(e) {
    console.log('speak.js warning: no worker support');
}

// Create a PIXI application
var app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
});
var scriptInfo;

// Load the VA script from assets/VAmodel/script.json
fetch('./assets/VAmodel/script.json')
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        scriptData = data; // Store the script data
        scriptInfo = scriptData.scripts[1302];
        script = scriptInfo.script;
    })
    .catch(function(error) {
        console.error('Error loading script:', error);
    });

// Load and display the Live2D model
var audioContext = null; // Moved initialization to be inside the unlockAudioContext function

// Unlock AudioContext on user interaction (for iOS support)
function unlockAudioContext() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)(); // Create AudioContext when unlocking
    }

    if (!isAudioContextUnlocked && audioContext.state !== 'running') {
        audioContext.resume().then(function() {
            console.log("Audio context resumed.");
            isAudioContextUnlocked = true;
        });
    }
}

PIXI.live2d.Live2DModel.from('./assets/VAmodel/VA Character.model3.json').then(function(model) {
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
        .load(function(loader, resources) {
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
        var settings = mouthMotionData.Settings[0];
        var mappings = settings.Mappings;

        for (var i = 0; i < settings.AudioParameters.length; i++) {
            var param = settings.AudioParameters[i];
            audioParameterValues[param.Id] = 0;
        }

        function updateModelParameters() {
            for (var audioParamId in audioParameterValues) {
                if (audioParameterValues.hasOwnProperty(audioParamId)) {
                    var value = audioParameterValues[audioParamId];
                    var mapping = null;
                    for (var j = 0; j < mappings.length; j++) {
                        if (mappings[j].Id === audioParamId) {
                            mapping = mappings[j];
                            break;
                        }
                    }
                    if (mapping) {
                        for (var k = 0; k < mapping.Targets.length; k++) {
                            var target = mapping.Targets[k];
                            live2dModel.internalModel.coreModel.setParameterValueById(target.Id, target.Value * value);
                        }
                    }
                }
            }
        }
        // Update the mouth motion parameters at regular intervals
        setInterval(updateModelParameters, 150);
    }
}

function simulateAudioParameterChange() {
    audioParameterValues.A = Math.random();
    audioParameterValues.E = Math.random();
    audioParameterValues.I = Math.random();
    audioParameterValues.O = Math.random();
    audioParameterValues.U = Math.random();
}

function generateTTS(text, callback) {
    var message = {
        text: text,
        amplitude: 200,
        wordgap: 1,
        pitch: 20, // Test with the lowest pitch
        speed: 10, // Test with a very slow speed
        voice: 'en',
        base64: true
    };

    speakWorker.postMessage(message);

    speakWorker.onmessage = function(event) {
        callback(event.data);
    };
}

// Function to play audio in browser using AudioContext
function playTTS(audioData, live2dModel, simulateAudioParameterChangeFunc, animationIntervalIdRef) {
    // Check if audioData is a Uint8Array (binary data)
    if (!(audioData instanceof Uint8Array)) {
        console.error("Invalid audio data received:", audioData);
        return;
    }

    // Decode the audio data into an AudioBuffer
    audioContext.decodeAudioData(audioData.buffer)
        .then(function(buffer) {
            // Create an audio source
            var source = audioContext.createBufferSource();
            source.buffer = buffer;
            source.connect(audioContext.destination);
            source.start(0);

            isSpeaking = true; // Mark that speech is ongoing

            // Start lip sync when audio starts
            source.onended = function() {
                clearInterval(animationIntervalIdRef);
                animationIntervalIdRef = null;
                isSpeaking = false; // Mark that speech is finished

                // Reset the mouth movement
                if (live2dModel) {
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthOpenY', 0);
                    live2dModel.internalModel.coreModel.setParameterValueById('ParamMouthForm', 0);
                }
            };

            // Lip sync while the audio is playing
            if (live2dModel) {
                var startTime = Date.now();
                animationIntervalIdRef = setInterval(function() {
                    simulateAudioParameterChangeFunc();
                }, 150);
            }
        })
        .catch(function(error) {
            console.error("Error decoding audio data:", error);
        });
}

function speakText(text, live2dModel, simulateAudioParameterChangeFunc, animationIntervalIdRef) {
    // Generate audio using speak.js
    generateTTS(text, function(audioData) {
        // Play the generated audio and sync with the model
        playTTS(audioData, live2dModel, simulateAudioParameterChangeFunc, animationIntervalIdRef);
    });
}

// Function to speak using Speech Synthesis API for iOS
function speakWithNativeTTS(scriptText) {
    console.log("Starting speakWithNativeTTS function with script:", scriptText);

    var synth = window.speechSynthesis;
    var utterance = new SpeechSynthesisUtterance(scriptText);

    var voices = synth.getVoices();
    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    var selectedVoice = null;

    if (isIOS) {
        for (var i = 0; i < voices.length; i++) {
            if (voices[i].name === 'Fred') {
                selectedVoice = voices[i];
                console.log("Selected Fred voice for iOS");
                break;
            }
        }
    }

    if (!selectedVoice) {
        for (var j = 0; j < voices.length; j++) {
            if (voices[j].lang === 'en-US') {
                selectedVoice = voices[j];
                console.log("Selected default en-US voice");
                break;
            }
        }
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

// Track the starting point when the user taps
function onPointerDown(event) {
    startPoint = event.data.global;
    isDragging = false;
}

// Event handler for pointer up (tap detection)
function onPointerUp(event) {
    var currentPoint = event.data.global;
    var distance = Math.sqrt(
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
    if (isSpeaking) {
        console.log("Currently speaking, ignoring tap.");
        return;
    }
    unlockAudioContext();

    // Check if the device is running iOS
    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    if (isIOS) {
        speakWithNativeTTS(script); // Use native TTS for iOS
    } else {
        isSpeaking = true; // Mark speaking as active
        speakText(script, live2dModel, simulateAudioParameterChange, animationIntervalId);
    }
}

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};

// Stop TTS on page unload or visibility change
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

// Event listener for page unload
window.addEventListener('beforeunload', stopTTS);

// Event listener for visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopTTS(); // Stop TTS when the page becomes hidden
    }
});

// Function to handle tab close or navigate away
window.addEventListener('pagehide', stopTTS);

// Function to handle tab or browser being inactive (onblur)
window.addEventListener('blur', stopTTS);
