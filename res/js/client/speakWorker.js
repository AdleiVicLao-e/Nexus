importScripts('speakGenerator.js');

onmessage = function(event) {
  console.log("Worker received message:", event.data);

  // Call generateSpeech
  const audioData = generateSpeech(event.data.text, event.data.args);

  // Convert Uint8Array to Base64 if audioData is not null
  let base64AudioData = null;
  if (audioData) {
    base64AudioData = uint8ArrayToBase64(audioData);
  }

  console.log("Generated audio data:", base64AudioData ? base64AudioData.substring(0, 30) + '...' : 'No audio data');

  // Send the audio data back to the main thread
  postMessage(base64AudioData);
};

// Function to convert Uint8Array to Base64
function uint8ArrayToBase64(uint8Array) {
  let binaryString = '';
  const len = uint8Array.byteLength;
  for (let i = 0; i < len; i++) {
    binaryString += String.fromCharCode(uint8Array[i]);
  }
  return btoa(binaryString); // Using btoa, which is available in the worker context
}

// Optional: Add error handling for the worker
onerror = function(error) {
  console.error('Worker error:', error.message);
};
