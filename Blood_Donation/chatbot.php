<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }
        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            font-size: 18px;
            text-align: center;
            flex-shrink: 0;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            border-top: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }
        .chat-message {
            padding: 10px;
            border-radius: 10px;
            margin: 5px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .user-message {
            background-color: #e1ffc7;
            align-self: flex-end;
        }
        .bot-message {
            background-color: #f1f0f0;
            align-self: flex-start;
        }
        .chat-input-container {
            display: flex;
            border-top: 1px solid #ddd;
            background-color: #f9f9f9;
            padding: 10px;
        }
        .chat-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        .send-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .send-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
 
</body>
</html>
