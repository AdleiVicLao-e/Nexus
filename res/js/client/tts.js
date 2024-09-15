let speakWorker = null; // Initialized later after user interaction
let audioContext = null; // Initialized later after user interaction
let isAudioContextUnlocked = false;
let selectedVoice = null;

// Function to populate voices (note: speak.js doesn't support multiple voices)
function populateVoiceList() {
  selectedVoice = { name: "speak.js voice", lang: "en-US" };
  console.log("speak.js TTS initialized.");
}

// Function to unlock the AudioContext on iOS
function unlockAudioContext() {
  if (!isAudioContextUnlocked && audioContext.state !== 'running') {
    audioContext.resume().then(() => {
      console.log("AudioContext resumed.");
      isAudioContextUnlocked = true;
    });
  }
}

// Function to generate audio from text using speak.js
function generateTTS(text, callback) {
  if (!speakWorker) {
    console.error("speakWorker has not been initialized.");
    return;
  }

  const message = {
    text: text,
    amplitude: 200,   // Volume (0-200)
    wordgap: 1,       // Gap between words
    pitch: 60,        // Pitch (0-100)
    speed: 185,       // Speed (words per minute)
    voice: 'en',      // Language code
    base64: true      // Ensure this is set to true to get base64-encoded output
  };

  speakWorker.postMessage(message);

  speakWorker.onmessage = function (event) {
    callback(event.data);
  };
}

// Function to play audio in browser using AudioContext
function playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId) {
  if (!audioContext) {
    console.error("audioContext has not been initialized.");
    return;
  }

  // Check if audioData is a Uint8Array (binary data)
  if (!(audioData instanceof Uint8Array)) {
    console.error("Invalid audio data received:", audioData);
    return;
  }

  // Decode the audio data into an AudioBuffer
  audioContext.decodeAudioData(audioData.buffer, function (buffer) {
    let source = audioContext.createBufferSource();
    source.buffer = buffer;
    source.connect(audioContext.destination);
    source.start(0);

    // Start lip sync when audio starts
    source.onended = function () {
      clearInterval(animationIntervalId);
      animationIntervalId = null;

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
  }, function (error) {
    console.error("Error decoding audio data:", error);
  });
}

// Function to trigger the TTS using speak.js and synchronize with Live2D model
function speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId) {
  // Unlock AudioContext on iOS with user interaction
  unlockAudioContext();

  // Generate audio using speak.js
  generateTTS(text, function (audioData) {
    playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
  });
}

// Initialize AudioContext and speakWorker on first interaction
function initializeTTSSystem() {
  if (!audioContext) {
    audioContext = new (window.AudioContext || window.webkitAudioContext)();
  }

  if (!speakWorker) {
    speakWorker = new Worker('/res/js/client/speakWorker.js'); // Path to speakWorker.js
  }

  // Populate voice list (for future compatibility)
  populateVoiceList();
}

// Ensure TTS system is initialized on the first user interaction (iOS workaround)
window.addEventListener('click', initializeTTSSystem);
window.addEventListener('touchend', initializeTTSSystem);

// Ensure voice list is populated on page load
window.onload = function () {
  populateVoiceList();
};

// Expose functions to the global scope for use in va.js
window.populateVoiceList = populateVoiceList;
window.speakText = speakText;
