<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 8025;
    $mail->Timeout = 1;
    $mail->SMTPDebug = 1;

    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('joe@example.net', 'Joe User');
    $mail->addAddress('ellen@example.com');
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');
    $mail->addBCC('pinco@example.com', 'Pinco');
    $mail->addStringAttachment("My file attachment....", 'vouchers.txt');

    $mail->Subject = 'Here is the subject, welcome to New york!';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum lacinia rutrum neque. Etiam eu pretium diam. Suspendisse porta in enim vitae aliquam. Nulla vel sapien nec est placerat accumsan vitae id justo. Pellentesque rhoncus viverra lacus, in ullamcorper sapien posuere ut. Phasellus consectetur mollis ipsum eu finibus. Fusce vulputate odio ut sapien placerat porta. Vestibulum eleifend eleifend metus, vitae dapibus elit interdum vitae. Morbi molestie interdum nisl, sed commodo felis tincidunt sed.

    Donec eget mauris quis nulla rhoncus pellentesque ac at erat. Suspendisse vitae metus at purus faucibus consectetur. Nullam elementum sit amet risus id commodo. Donec felis massa, suscipit nec posuere vitae, pharetra cursus sapien. Vivamus non orci sed nisi dignissim congue. Maecenas ligula erat, pretium id quam a, dictum ornare ex. Aliquam posuere rutrum lorem sed dapibus. Suspendisse vel elementum arcu, non facilisis felis. Morbi dapibus fermentum gravida. Nunc vulputate ut risus sed viverra. Nam nec dui eget ipsum gravida varius sit amet et lacus. Ut viverra massa vel urna rutrum congue. Suspendisse porttitor, arcu vel condimentum tempus, dui eros feugiat nulla, eu consectetur velit ipsum ac mauris. Nullam ultricies congue aliquam. Vestibulum ornare justo id tincidunt sollicitudin. Cras commodo dolor et tortor vulputate vehicula.";

    echo "\n----------------\nReady to send...\n----------------\n";

    $success = $mail->send();

    if ($success) {
        echo "\n----------------\nMessage sent!!!\n----------------\n";
    } else {
        echo "Ahiaiaiaiaiaiaiiiiiii....";
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
