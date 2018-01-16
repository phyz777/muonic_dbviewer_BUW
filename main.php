<html>
<head>
    <title>PP Skyview Debus Lang</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="style.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <script src="js/responsive.js"></script>
</head>
<body data-ng-app="bodyApp">
<header data-ng-controller="headerController">
    <img src="resources/nightsky_bar.jpg" alt="" width="100%" data-ng-click="rstrt()"/>
</header>
<section>
<?php
    $debug_instance = new \BUW\PpSkVw\MysqlDbAccess(
        "YOUR MYSQL HOST HERE",
        "YOUR MYSQL USER HERE",
        "YOUR MYSQL PASSWORD HERE",
        "YOUR MYSQL DATABASE NAME HERE",
        "MySQL"
    );
    $debug_instance->dbToHtml($_GET);
?>
</section>
</body>
