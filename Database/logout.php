<?php
session_start();
session_unset();
session_destroy();
header("Location: ../Index.php"); // naik satu folder ke Green-Basket
exit();
?>