// Function to speak using Speech Synthesis API, prioritize Fred for iOS and male en-US for other phones
function speakWithNativeTTS(text) {
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);

    // Get the list of available voices
    const voices = synth.getVoices();

    // Check if the device is running iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    // Check if the device is a phone but not iOS
    const isPhoneNotIOS = /Mobile/.test(navigator.userAgent) && !isIOS;

    let selectedVoice;

    // If on iOS, attempt to use the 'Fred' voice
    if (isIOS) {
        selectedVoice = voices.find(voice => voice.name === 'Fred');
    }

    // If on other phones, attempt to use a male en-US voice
    if (isPhoneNotIOS) {
        selectedVoice = voices.find(voice => voice.lang === 'en-US' && voice.name.toLowerCase().includes('male'));
    }

    // If Fred is not available, or not iOS, or not a phone, fall back to default en-US voice
    if (!selectedVoice) {
        selectedVoice = voices.find(voice => voice.lang === 'en-US');
    }

    // Set the voice for the utterance
    if (selectedVoice) {
        utterance.voice = selectedVoice;
    }

    synth.speak(utterance);
}

// Ensure voice list is populated after page load
window.onload = function () {
    window.speechSynthesis.onvoiceschanged = function () {
        window.speechSynthesis.getVoices();
    };
};
