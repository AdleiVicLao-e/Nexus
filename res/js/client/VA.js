let live2dModel;
let mouthMotionData;
let animationIntervalId;
let audioParameterValues = {};

// Variables to track dragging state
let isDragging = false;
let startPoint = { x: 0, y: 0 };
let dragThreshold = 10; // Minimum movement to qualify as a drag

// Create a PIXI application
const app = new PIXI.Application({
    view: document.getElementById('va-canvas'),
    autoStart: true,
    backgroundAlpha: 0, // Transparent background
});

// Load and display the Live2D model
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

    // Add event listeners for tap/drag functionality
    live2dModel.on('pointerdown', onPointerDown);
    live2dModel.on('pointerup', onPointerUp);
    live2dModel.on('pointerupoutside', onPointerUp);
    live2dModel.on('pointermove', onPointerMove);
});

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

// Simulate mouth movement based on random audio parameter values (for testing purposes)
function simulateAudioParameterChange() {
    audioParameterValues["A"] = Math.random();
    audioParameterValues["E"] = Math.random();
    audioParameterValues["I"] = Math.random();
    audioParameterValues["O"] = Math.random();
    audioParameterValues["U"] = Math.random();
}

// Event handler for pointer down
function onPointerDown(event) {
    isDragging = false; // Reset dragging flag
    startPoint = event.data.global.clone(); // Record the start position
}

// Event handler for pointer move (dragging)
function onPointerMove(event) {
    const currentPoint = event.data.global;
    const distance = Math.sqrt(
        Math.pow(currentPoint.x - startPoint.x, 2) + Math.pow(currentPoint.y - startPoint.y, 2)
    );

    // If the mouse/touch moves more than the threshold, consider it a drag
    if (distance > dragThreshold) {
        isDragging = true;
        // Move the model as per the drag
        live2dModel.position.set(currentPoint.x, currentPoint.y);
    }
}

// Event handler for pointer up (tap detection)
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

// Handle tap interaction (trigger speech)
function handleTap(event) {
    event.stopPropagation(); // Prevent default behavior

    // Text the VA will say when tapped
    const text = "The sky is blue, the clouds are white, the leaves are green, the sun is bright";

    // Call the speakText function (handles TTS and lip-syncing)
    speakText(text, live2dModel, simulateAudioParameterChange, animationIntervalId);
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
