<?php
require_once 'include/admin_protect.php';
require_once 'include/bootstrap.php';
$result = doBootstrap();
foreach ($result as $key => $value) {
    if ($key == 'status') {
        echo "<table border='1'>
        <tr>
        <th>
        $key
        </th>
        </tr>
        <tr>
        <td>
        $value
        </td>
        </tr>
        </table> <br />";
    }
    elseif ($key == 'num-record-loaded') {
        echo "<table border='1'>
        <tr>
        <th colspan='2'>
        $key
        </th>
        </tr>";
        foreach ($value as $status => $file) {
            foreach ($file as $name => $number) {
                echo "<tr>
                <td>
                $name
                </td>
                <td>
                $number
                </td>
                </tr>";
            }
        }
        echo "</table><br />";
    }
    else {
        echo "<table border='1'>
        <tr>
        <th colspan='3'>
        $key
        </th>
        </tr>";
        echo "<tr>
        <th>
        file
        </th>
        <th>
        line
        </th>
        <th>
        message
        </th>
        </tr>";
        $iterations = $value;
        foreach ($iterations as $file) {
            $rowspan = sizeof($file['message']);
            echo "<tr>";
            foreach ($file as $status => $message) {
                if (is_array($message)) {
                    foreach ($message as $individual) {
                        echo "<td>
                        $individual
                        </td></tr>";
                    }
                }
                else {
                    echo "<td rowspan='$rowspan'>
                    $message
                    </td>";
                }
            }
            "</tr>";
        }
        echo "</table><br />";
    }
}
