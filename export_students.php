<?php
require 'vendor/autoload.php';
include "config.php";

use Dompdf\Dompdf;

$dompdf = new Dompdf();

/* Fetch students */
$result = $conn->query("SELECT fullname, email FROM users WHERE role='student'");

$html = '
<h2 style="text-align:center;">Student List Report</h2>
<table border="1" width="100%" cellspacing="0" cellpadding="8">
<tr>
<th>#</th>
<th>Full Name</th>
<th>Email</th>
</tr>';

$count = 1;

while($row = $result->fetch_assoc()){
    $html .= '
    <tr>
        <td>'.$count++.'</td>
        <td>'.$row['fullname'].'</td>
        <td>'.$row['email'].'</td>
    </tr>';
}

$html .= '</table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* Download file */
$dompdf->stream("students_report.pdf", ["Attachment" => true]);
exit();