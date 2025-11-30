<?php
session_start();
session_unset();
session_destroy();
header("Location: ../login.php"); // naik satu folder ke Green-Basket
exit();
?>