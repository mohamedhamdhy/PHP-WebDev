<?php
// Replace with your OpenAI API key
$apiKey = 'YOUR_OPENAI_API_KEY';
$apiUrl = 'https://api.openai.com/v1/completions';

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

// Prepare the request payload
$data = [
    'model' => 'text-davinci-003', // or whichever model you prefer
    'prompt' => $message,
    'max_tokens' => 150
];

$headers = [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$reply = $responseData['choices'][0]['text'] ?? 'Sorry, I did not understand that.';

echo json_encode(['reply' => trim($reply)]);
?>
