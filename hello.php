<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Code Generator</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jomolhari&display=swap" rel="stylesheet"> <!-- Add Jomolhari font -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <style>
        #qrcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .qrcode-box {
            text-align: center;
        }
    </style>
</head>
<body>

<button onclick="generateAndDownloadQRCodes()">Run</button>
<div id="qrcode-container"></div>

<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "kultoura"); // Change credentials as necessary

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch artifacts with IDs from 200 to 500
$sql = "SELECT artifact_id, name FROM artifact_info WHERE artifact_id BETWEEN 1201 AND 1294";
$result = $conn->query($sql);

$artifacts = array();

if ($result->num_rows > 0) {
    // Store artifact data in an array for JavaScript usage
    while($row = $result->fetch_assoc()) {
        $artifacts[] = array('artifact_id' => $row['artifact_id'], 'name' => $row['name']);
    }
}

$conn->close();
?>

<script>
    // Pass PHP data to JavaScript
    var artifacts = <?php echo json_encode($artifacts); ?>;

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async function generateAndDownloadQRCodes() {
        // Clear the QR code container in case of multiple runs
        $('#qrcode-container').empty();

        // Loop through each artifact and generate its QR code
        for (const artifact of artifacts) {
            var artifactId = artifact.artifact_id;
            var artifactName = artifact.name;

            // Create a container for each QR code
            var qrBox = $('<div class="qrcode-box"></div>');
            qrBox.append('<h3>' + artifactName + '</h3>'); // Add artifact name
            var qrCodeDiv = $('<div id="qrcode-' + artifactId + '"></div>');
            qrBox.append(qrCodeDiv);

            // Append the QR code box to the main container
            $('#qrcode-container').append(qrBox);

            // Generate QR code for the artifact
            $('#qrcode-' + artifactId).qrcode({
                text: artifactId.toString(),
                width: 200,
                height: 200
            });

            // Wait for a short time to ensure QR code is rendered before downloading
            await sleep(300); // Add delay (in milliseconds) to ensure downloads don't happen too fast

            var qrCodeCanvas = document.querySelector('#qrcode-' + artifactId + ' canvas');
            if (qrCodeCanvas) {
                var dataURL = qrCodeCanvas.toDataURL('image/png');
                var link = document.createElement('a');
                link.href = dataURL;
                link.download = artifactId + '-' + artifactName + '.png'; // Filename format: artifact_id-artifact_name.png
                document.body.appendChild(link);
                link.click(); // Automatically trigger the download
                document.body.removeChild(link); // Clean up
            }
        }

        console.log('All QR codes have been generated and downloaded.');
    }
</script>
</body>
</html>