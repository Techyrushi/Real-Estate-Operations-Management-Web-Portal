<?php 
// includes/email_helper.php 

use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 

// Check for Composer autoloader 
if (file_exists(__DIR__ . '/../vendor/autoload.php')) { 
    require_once __DIR__ . '/../vendor/autoload.php'; 
} 
// Check for manual PHPMailer inclusion 
elseif (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) { 
    require_once __DIR__ . '/PHPMailer/src/Exception.php'; 
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php'; 
    require_once __DIR__ . '/PHPMailer/src/SMTP.php'; 
} 

function send_email($to, $subject, $body_content, $reply_to = null, $reply_to_name = '', $attachment = null) 
{ 
    // Define absolute path to logo for embedding 
    $logo_path = __DIR__ . '/../assets/images/techyrushi.png'; 
    $year = date('Y'); 

    // Email Template with CID reference 
    $message = " 
    <!DOCTYPE html> 
    <html> 
    <head> 
    <meta charset='UTF-8'> 
    <meta name='viewport' content='width=device-width, initial-scale=1.0'> 
    <style> 
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; color: #333333; line-height: 1.6; } 
        .email-wrapper { width: 100%; background-color: #f4f4f4; padding: 40px 0; } 
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); } 
        .email-header { background-color: #ffffff; padding: 30px; text-align: center; border-bottom: 3px solid #0056b3; } 
        .email-header img { max-height: 60px; width: auto; } 
        .email-body { padding: 40px 30px; color: #555555; font-size: 16px; } 
        .email-body h2 { color: #333333; font-size: 24px; margin-top: 0; margin-bottom: 20px; font-weight: 600; } 
        .email-body p { margin-bottom: 15px; font-size: 16px; } 
        .email-body table { width: 100%; border-collapse: collapse; margin-bottom: 20px; } 
        .email-body table th { text-align: left; padding: 12px; background-color: #f8f9fa; border-bottom: 1px solid #eeeeee; width: 30%; color: #333; font-weight: 600; } 
        .email-body table td { padding: 12px; border-bottom: 1px solid #eeeeee; color: #555; } 
        .email-footer { background-color: #333333; color: #ffffff; padding: 30px; text-align: center; font-size: 14px; } 
        .email-footer p { margin: 5px 0; color: #cccccc; } 
        .email-footer a { color: #ffffff; text-decoration: none; } 
        .btn { 
            display: inline-block; 
            padding: 15px 35px; 
            background-color: #000000; 
            color: #ffffff !important; 
            text-decoration: none !important; 
            border-radius: 5px; 
            font-weight: bold; 
            margin-top: 25px; 
            font-size: 18px; 
            text-transform: uppercase; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); 
            border: 2px solid #000000; 
        } 
        .btn:hover { 
            background-color: #333333; 
            border-color: #333333; 
        } 
        @media only screen and (max-width: 600px) { 
            .email-container { width: 100% !important; border-radius: 0; } 
            .email-body { padding: 20px; } 
        } 
    </style> 
    </head> 
    <body> 
        <div class='email-wrapper'> 
            <div class='email-container'> 
                <div class='email-header'> 
                    <!-- Use CID for embedded image --> 
                    <a href='#'><img src='cid:logo_img' alt='Techyrushi Logo'></a> 
                </div> 
                <div class='email-body'> 
                    $body_content 
                </div> 
                <div class='email-footer'> 
                    <p><strong>Techyrushi - IT Solutions & Technology</strong></p> 
                    <p>Phone: (+91) 8446225859 | Email: techyrushi.talks@techyrushi.com</p> 
                    <p>Nashik, Maharashtra, India</p> 
                    <p style='margin-top: 15px; font-size: 12px; color: #999;'>&copy; $year Techyrushi. All rights reserved.</p> 
                </div> 
            </div> 
        </div> 
    </body> 
    </html> 
    "; 

    // Ensure PHPMailer class exists 
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) { 
        // For development/testing without PHPMailer, we can log the email content
        error_log("PHPMailer not found. Email content: " . $body_content);
        return false;
        // throw new Exception("PHPMailer library not found. Please run 'composer install' or check your paths."); 
    } 

    $mail = new PHPMailer(true); 
    try { 
        // Server settings 
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true; 
        $mail->Username = 'crushikesh74@gmail.com'; 
        $mail->Password = 'ynmejwizmmgvcsxu'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587; 

        // Fix for XAMPP/Localhost SSL certificate issues 
        $mail->SMTPOptions = array( 
            'ssl' => array( 
                'verify_peer' => false, 
                'verify_peer_name' => false, 
                'allow_self_signed' => true 
            ) 
        ); 

        // Recipients 
        $mail->setFrom('crushikesh74@gmail.com', 'Techyrushi'); 
        $mail->addAddress($to); 
        
        if ($reply_to) { 
            $mail->addReplyTo($reply_to, $reply_to_name); 
        } 

        // Add Attachment 
        if ($attachment && file_exists($attachment)) { 
            $mail->addAttachment($attachment); 
        } 

        // Embed the logo image 
        if (file_exists($logo_path)) { 
            $mail->addEmbeddedImage($logo_path, 'logo_img', 'techyrushi.png');
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>