<?php
/**
 * @var Mailer $this
 * @var string $content
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->title ?></title>
    <style>
        body {
            font-family: Open-sans, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        #header, #content, #footer {
            padding: 10px;
        }
        #header {
            background-color: #FF851B;
            color: #ffffff;
        }
        #footer {
            border-top: 2px solid #FF851B;
            font-size: .9em;
            color: #666666;
        }
    </style>
</head>
<body>
<div id="header">
    <h1>Organizzy</h1>
</div>
<div id="content">
    <?php echo $content ?>
</div>
<div id="footer">
    <p>Regards<br/>Organizzy Team</p>
    <p><i><b>NB:</b> Do not reply this email</i></p>
</div>
</body>
</html>