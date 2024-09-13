// Variable to hold the selected voice
let selectedVoice;

// Function to populate the voice list and set the default voice
function populateVoiceList() {
  const voices = window.speechSynthesis.getVoices();

  // Log available voices for debugging
  console.log("Available voices:", voices);

  // Prioritize selecting a male voice if available
  selectedVoice = voices.find(voice => voice.name.includes('Male') || voice.gender === 'male');

  // If no male voice is found, fall back to the first available voice
  if (!selectedVoice && voices.length > 0) {
    selectedVoice = voices[0];
  }

  // If no voices are found, use the browser's or device's default voice
  if (!selectedVoice) {
    selectedVoice = new SpeechSynthesisUtterance().voice;
    console.warn("Using default voice as no specific voices were found.");
  }

  if (selectedVoice) {
    console.log("Selected voice:", selectedVoice.name || "Default voice");
  } else {
    console.warn("No available voices found.");
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

// Call populateVoiceList on page load to ensure voices are available immediately
populateVoiceList();
