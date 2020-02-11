<?php

ini_set('max_execution_time', (60*2));

//recupero i parametri inviati in formato json
$body  = file_get_contents('php://input');

//trasformo i paramentri in un array associativo
$parameters = json_decode($body, true);

// elenco negozi
$ipMtx = [
    '0101' => '192.168.201.11',
    '0102' => '192.168.202.11',
    '0103' => '192.168.203.11',
    '0104' => '192.168.204.11',
    '0105' => '192.168.205.11',
    '0106' => '192.168.206.11',
    '0107' => '192.168.207.11',
    '0108' => '192.168.208.11',
    '0109' => '192.168.209.11',
    '0110' => '192.168.210.11',
    '0111' => '192.168.223.11',
    '0113' => '192.168.213.11',
    '0114' => '192.168.17.11',
    '0115' => '192.168.4.11',
    '0119' => '192.168.219.11',
    '0121' => '192.168.121.11',
    '0122' => '192.168.122.11',
    '0123' => '192.168.123.11',
    '0124' => '192.168.224.11',
    '0125' => '192.168.225.11',
    '0126' => '192.168.26.11',
    '0127' => '192.168.227.11',
    '0128' => '192.168.228.11',
    '0129' => '192.168.18.11',
    '0131' => '192.168.3.11',
    '0132' => '192.168.13.11',
    '0133' => '192.168.233.11',
    '0134' => '11.0.34.11',
    '0136' => '192.168.236.11',
    '0138' => '192.168.238.11',
    '0139' => '192.168.239.11',
    '0140' => '192.168.240.11',
    '0141' => '192.168.7.11',
    '0142' => '192.168.242.11',
    '0143' => '192.168.243.11',
    '0144' => '192.168.244.11',
    '0145' => '192.168.245.11',
    '0146' => '192.168.2.11',
    '0147' => '192.168.6.11',
    '0148' => '192.168.5.11',
    '0149' => '192.168.15.11',
    '0153' => '192.168.153.11',
    '0155' => '192.168.155.11',
    '0156' => '192.168.156.11',
    '0170' => '192.168.9.11',
    '0171' => '192.168.141.11',
    '0172' => '192.168.172.11',
    '0173' => '192.168.173.11',
    '0176' => '192.168.176.11',
    '0177' => '192.168.16.11',
    '0178' => '192.168.12.11',
    '0179' => '192.168.14.11',
    '0180' => '192.168.11.11',
    '0181' => '192.168.10.11',
    '0184' => '192.168.184.11',
    '0185' => '192.168.185.11',
    '0186' => '192.168.186.11',
    '0188' => '192.168.188.11',
    '0190' => '192.168.190.11',
    '0461' => '192.168.161.11',
    '0462' => '192.168.162.11',
    '0463' => '192.168.163.11',
    '0464' => '192.168.164.11',
    '0465' => '192.168.165.11',
    '0466' => '192.168.166.11',
    '0467' => '192.168.167.11',
    '0468' => '192.168.168.11',
    '3151' => '172.30.10.2',
    '3152' => '172.30.18.2',
    '3650' => '172.30.2.2',
    '3652' => '172.30.30.2',
    '3654' => '192.168.154.11',
    '3657' => '172.30.26.2',
    '3658' => '172.30.13.2',
    '3659' => '172.30.12.2',
    '3661' => '192.168.170.11',
    '3665' => '172.30.4.2',
    '3666' => '172.30.6.2',
    '3668' => '172.30.27.2',
    '3670' => '172.30.1.2',
    '3671' => '172.30.8.2',
    '3673' => '172.30.29.2',
    '3674' => '172.30.25.2',
    '3675' => '172.30.14.2',
    '3682' => '172.30.0.2',
    '3683' => '172.30.37.2',
    '3687' => '172.30.23.2',
    '3689' => '172.30.7.2',
    '3692' => '172.30.31.2',
    '3693' => '172.30.33.2',
    '3694' => '192.168.169.11'
];

$result = [];
$error = '';
$status = 1;

if ( $parameters['function'] == 'getDatiContabili' ) {
    $connection_string = "DRIVER={SQL Server};SERVER=".$ipMtx[$parameters['sede']].";PORT=1433;DATABASE=mtx";
    try {
        $conn = odbc_connect($connection_string,"mtxadmin","mtxadmin", SQL_CUR_USE_ODBC);

        $sql = "select
					REG, STORE, substring(convert(VARCHAR, DDATE, 120),1,10) 'DDATE', TTIME, SEQUENCENUMBER,
					TRANS, TRANSSTEP, RECORDTYPE, RECORDCODE, USERNO, MISC, DATA
				from IDC_EOD
				where Ddate = ? and convert(varbinary, RECORDTYPE) in (convert(varbinary, 'H'),convert(varbinary, 'F'),convert(varbinary, 'T'),convert(varbinary, 'V'))
				order by ddate, reg, sequencenumber;";

        $stmt    = odbc_prepare($conn, $sql);
        if ( odbc_execute($stmt, [$parameters['data']]) ) {
            while ($record = odbc_fetch_array($stmt)) {
                $result[] = $record;
            }

            if (! count($result)) {
                $sql = "select 
						REG, STORE, substring(convert(VARCHAR, DDATE, 120),1,10) 'DDATE', TTIME, SEQUENCENUMBER,
						TRANS, TRANSSTEP, RECORDTYPE, RECORDCODE, USERNO, MISC, DATA
					from IDC
					where Ddate = ? and convert(varbinary, RECORDTYPE) in (convert(varbinary, 'H'),convert(varbinary, 'F'),convert(varbinary, 'T'),convert(varbinary, 'V'))
					order by ddate, reg, sequencenumber;";

                $status = 0;

                $stmt    = odbc_prepare($conn, $sql);
                if ( odbc_execute($stmt, [$parameters['data']] ) ) {
                    while ($record = odbc_fetch_array($stmt)) {
                        $result[] = $record;
                    }
                }
            }

            odbc_close($conn);
        }
    } catch (Exception $e) {

        $error = $e->getMessage();
    }

    header('Content-type: application/json; charset=utf8');
    echo json_encode(['errorMessage' => $error, 'datacollect' => $result, 'status'=>$status]);
} else if ($parameters['function'] == 'getDatacollect' ) {
    $connection_string = "DRIVER={SQL Server};SERVER=".$ipMtx[$parameters['sede']].";PORT=1433;DATABASE=mtx";
    try {
        $conn = odbc_connect($connection_string,"mtxadmin","mtxadmin", SQL_CUR_USE_ODBC);

        $sql = "select
						REG, STORE, substring(convert(VARCHAR, DDATE, 120),1,10) 'DDATE', TTIME, SEQUENCENUMBER,
						TRANS, TRANSSTEP, RECORDTYPE, RECORDCODE, USERNO, MISC, DATA
					from IDC_EOD
					where Ddate = ?
					order by ddate, reg, sequencenumber;";

        $stmt    = odbc_prepare($conn, $sql);
        if ( odbc_execute($stmt, [$parameters['data']] ) ) {
            while ($record = odbc_fetch_array($stmt)) {
                $result[] = $record;
            }

            if (! count($result)) {
                $sql = "select
						REG, STORE, substring(convert(VARCHAR, DDATE, 120),1,10) 'DDATE', TTIME, SEQUENCENUMBER,
						TRANS, TRANSSTEP, RECORDTYPE, RECORDCODE, USERNO, MISC, DATA
					from IDC
					where Ddate = ? 
					order by ddate, reg, sequencenumber;";

                $status = 0;

                $stmt    = odbc_prepare($conn, $sql);
                if ( odbc_execute($stmt, [$parameters['data']] ) ) {
                    while ($record = odbc_fetch_array($stmt)) {
                        $result[] = $record;
                    }
                }
            }

            odbc_close($conn);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    header('Content-type: application/json; charset=utf8');
    echo json_encode(['errorMessage' => $error, 'datacollect' => $result, 'status'=>$status]);
}
?>
