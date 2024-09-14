// Initialize the speak.js engine (WebAssembly) for TTS
let speakWorker = new Worker('/res/js/client/speakWorker.js'); // Path to speakWorker.js from speak.js-master
let audioContext = new (window.AudioContext || window.webkitAudioContext)(); // AudioContext for audio playback

// Variable to hold the selected voice and TTS settings
let selectedVoice = null;

// Function to populate voices (note: speak.js doesn't support multiple voices)
function populateVoiceList() {
  // speak.js only has one default voice, but this could be extended in the future

  selectedVoice = { name: "speak.js voice", lang: "en-US" };
  console.log("speak.js TTS initialized.");
}

// Function to generate audio from text using speak.js
function generateTTS(text, callback) {
  const message = {
    text: text,
    amplitude: 200,   // Volume (0-200)
    wordgap: 1,       // Gap between words
    pitch: 60,        // Pitch (0-100)
    speed: 185,       // Speed (words per minute)
    voice: 'en',      // Language code
    base64: true      // Ensure this is set to true to get base64-encoded output
  };


  // Use the worker to process the text
  speakWorker.postMessage(message);

  // When speakWorker finishes, call the provided callback with the audio data
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
  audioContext.decodeAudioData(audioData.buffer, function (buffer) {
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
    };

    // Lip sync while the audio is playing
    if (live2dModel) {
      let startTime = Date.now();
      animationIntervalId = setInterval(() => {
        let elapsed = Date.now() - startTime;
        simulateAudioParameterChange();
      }, 150);
    }
  }, function (error) {
    console.error("Error decoding audio data:", error);
  });
}

function isBase64(str) {
  try {
    return btoa(atob(str)) === str;
  } catch (err) {
    return false;
  }
}


// Function to trigger the TTS using speak.js and synchronize with Live2D model
function speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId) {
  // Generate audio using speak.js
  generateTTS(text, function (audioData) {
    // Play the generated audio and sync with the model
    playTTS(audioData, live2dModel, simulateAudioParameterChange, animationIntervalId);
  });
}

// Ensure voice list is populated on page load
window.onload = function () {
  populateVoiceList();
};

// Expose functions to the global scope for use in va.js
window.populateVoiceList = populateVoiceList;
window.speakText = speakText;
