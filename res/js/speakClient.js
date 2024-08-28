// Variable to hold the selected voice
let selectedVoice;

// Function to populate the voice list and set the default voice
function populateVoiceList() {
  const voices = window.speechSynthesis.getVoices();

  // Log available voices for debugging
  console.log("Available voices:", voices);

  // Set the desired voice as the default selected voice
  selectedVoice = voices.find(voice => voice.name === 'Microsoft Mark - English (United States)');
  if (selectedVoice) {
    console.log("Selected voice:", selectedVoice.name);
  } else {
    console.warn("Desired voice not found.");
  }
}

// Function to get the selected voice
function getSelectedVoice() {
  return selectedVoice;
}

// Function to set TTS parameters
function setTTSParameters(utterance) {
  utterance.pitch = 0.1; // Set pitch to 0.1
  utterance.rate = 0.7;  // Set rate to 0.7
  utterance.volume = 1;  // Set volume to 1
}

// Expose functions to the global scope
window.populateVoiceList = populateVoiceList;
window.getSelectedVoice = getSelectedVoice;
window.setTTSParameters = setTTSParameters;

// Initialize voice list population on voices changed
window.speechSynthesis.onvoiceschanged = populateVoiceList;
