let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};
let isAudioContextUnlocked = false;
let isSpeaking = false; // Track if speech is active
let scriptData; // Store the loaded script data
let script;
let scriptTimeoutId;

// Variables to track dragging state
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag
let speakWorker;

// Track the current audio source
let currentAudioSource = null;

// Initialize the worker
function initializeSpeakWorker() {
    try {
        speakWorker = new Worker('./res/js/client/speakWorker.js');

        speakWorker.onmessage = function(event) {
            if (isSpeaking) { // Only process messages if speaking is active
                playTTS(event.data, live2dModel, simulateAudioParameterChange, animationIntervalId);
            }
        };

        speakWorker.onerror = function(error) {
            console.error("Speak Worker Error:", error);
        };
    } catch(e) {
        console.log('speak.js warning: no worker support');
    }
}

// Initialize the worker initially
initializeSpeakWorker();

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
        scriptInfo = scriptData.scripts[0];
        script = scriptInfo.script;
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

// Function to reset script to the first value when no QR code is scanned
function resetScript() {
    // Assuming scriptData.scripts is an array
    if (scriptData && scriptData.scripts.length > 0) {
        scriptInfo = scriptData.scripts[0];
        script = scriptInfo.script;
    }
}

// Function to handle QR code scanning (you'll need to call this function when a QR code is successfully scanned)
function onQRCodeScanned() {
    // Reset the timeout whenever a QR code is scanned
    clearTimeout(scriptTimeoutId);

    // Set a new timeout to reset the script after 5 minutes
    scriptTimeoutId = setTimeout(resetScript, 300000);
}

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

function simulateAudioParameterChange() {
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

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
        console.error("Speak worker is not initialized.");
    }

    // Handle callback via onmessage
    speakWorker.onmessage = function (event) {
        callback(event.data);
    };
}

// Function to play audio in browser using AudioContext
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

            // Store the current audio source
            currentAudioSource = source;

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

                // Clear the reference to the current audio source
                currentAudioSource = null;
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

function speakText(text) {
    // Ensure the worker is initialized
    if (!speakWorker) {
        initializeSpeakWorker();
    }

    // Generate audio using speak.js
    generateTTS(text, function (audioData) {
        // Play the generated audio and sync with the model
        playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
    });
}

// Function to speak using Speech Synthesis API for iOS
function speakWithNativeTTS(script) {
    console.log("Starting speakWithNativeTTS function with script:", script);

    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(script);

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

let lastTapTime = 0; // To track the last tap time
const doubleTapDelay = 300; // Time interval to consider for double tap (in milliseconds)

// Modify the onPointerUp function to detect double taps
function onPointerUp(event) {
    const currentPoint = event.data.global;
    const distance = Math.sqrt(
        Math.pow(currentPoint.x - startPoint.x, 2) + Math.pow(currentPoint.y - startPoint.y, 2)
    );

    // Check if it's a double tap
    const currentTime = Date.now();
    if (currentTime - lastTapTime < doubleTapDelay && !isDragging && distance < dragThreshold) {
        handleDoubleTap(event); // Call the double tap handler
    } else if (!isDragging && distance < dragThreshold) {
        handleTap(event); // Handle single tap
    }

    // Update the last tap time
    lastTapTime = currentTime;

    // Reset dragging state
    isDragging = false;
}

// Handle double tap interaction (trigger stop TTS)
function handleDoubleTap(event) {
    event.stopPropagation(); // Prevent default behavior
    stopTTS(); // Call stopTTS function on double tap
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

    // If already speaking, stop the current TTS before starting a new one
    if (isSpeaking) {
        stopTTS();
    }

    unlockAudioContext();

    // Check if the device is running iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

    if (isIOS) {
        speakWithNativeTTS(script); // Use native TTS for iOS
    } else {
        isSpeaking = true; // Mark speaking as active
        speakText(script); // Use the TTS worker for non-iOS devices
    }
}

// Stop TTS on page unload or visibility change
function stopTTS() {
    // Stop Speech Synthesis for iOS
    if (window.speechSynthesis) {
        window.speechSynthesis.cancel(); // Cancel any ongoing speech
        console.log("TTS stopped via speechSynthesis.cancel().");
    }

    // Stop Audio Playback for non-iOS
    if (currentAudioSource) {
        try {
            currentAudioSource.stop(); // Stop the audio playback
            console.log("Audio playback stopped.");
        } catch (e) {
            console.error("Error stopping audio source:", e);
        }
        currentAudioSource = null; // Clear the reference
    }

    // Terminate the TTS worker for non-iOS
    if (speakWorker) {
        speakWorker.terminate(); // Terminate the worker
        speakWorker = null; // Clear the reference
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

    // Reinitialize the worker for future use if needed
    reinitializeWorkerIfNeeded();
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

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};


// Function to reinitialize the worker if needed
function reinitializeWorkerIfNeeded() {
    if (!speakWorker) {
        initializeSpeakWorker();
    }
}
