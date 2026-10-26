<?php
class clsKetnoi {
    function moketnoi() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli('localhost', 'root', '', 'qlbh7');
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    function dongketnoi($conn) {
        if ($conn) {
            $conn->close();
        }
    }
}
