<?php
/**
 * Fail2Ban Web Manager Backend API (SQLite + Socket alapú, többnyelvű, óránkénti statisztikával)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Nyelvi szótárak
$translations = [
    'hu' => [
        'err_socket' => 'Nem sikerült végrehajtani a parancsot a socketen keresztül: ',
        'err_missing_params' => 'Hiányzó Jail vagy IP cím!',
        'err_invalid_ip' => 'Érvénytelen IP cím formátum!',
        'err_db' => 'Nem sikerült csatlakozni a Fail2Ban SQLite adatbázisához: ',
        'success_ban' => 'IP (%s) sikeresen kitiltva a(z) %s jailben.',
        'success_unban' => 'IP (%s) sikeresen feloldva a(z) %s jailből.',
        'unknown_action' => 'Ismeretlen művelet!'
    ],
    'en' => [
        'err_socket' => 'Failed to execute command via socket: ',
        'err_missing_params' => 'Missing Jail or IP address!',
        'err_invalid_ip' => 'Invalid IP address format!',
        'err_db' => 'Failed to connect to Fail2Ban SQLite database: ',
        'success_ban' => 'IP (%s) successfully banned in jail %s.',
        'success_unban' => 'IP (%s) successfully unbanned from jail %s.',
        'unknown_action' => 'Unknown action!'
    ]
];

$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'hu';
if (!isset($translations[$lang])) {
    $lang = 'hu';
}
$t = $translations[$lang];

$db_file = '/var/lib/fail2ban/fail2ban.sqlite3';

function run_fail2ban_cmd($args) {
    global $t;
    $escaped_args = array_map('escapeshellarg', $args);
    $cmd = "/usr/bin/fail2ban-client " . implode(' ', $escaped_args) . " 2>&1";
    
    $output = [];
    $return_var = 0;
    exec($cmd, $output, $return_var);
    
    return [
        'success' => ($return_var === 0),
        'output'  => implode("\n", $output),
        'raw'     => $output
    ];
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'status';

try {
    switch ($action) {

        case 'status':
            if (!file_exists($db_file)) {
                throw new Exception($t['err_db'] . "A fájl nem található ($db_file).");
            }

            try {
                $pdo = new PDO('sqlite:' . $db_file);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception($t['err_db'] . $e->getMessage());
            }

            // Jailek lekérdezése
            $jail_stmt = $pdo->query("SELECT name FROM jails");
            $jails_raw = $jail_stmt->fetchAll(PDO::FETCH_ASSOC);

            $jail_details = [];
            $hourly_stats = []; // Óránkénti adatok gyűjtéséhez (elmúlt 24 óra)

            // Inicializáljuk a 0-24 órát
            for ($i = 0; $i < 24; $i++) {
                $hour_key = sprintf('%02d:00', $i);
                $hourly_stats[$hour_key] = [];
            }

            // Az elmúlt 24 óra UNIX timestamp határa
            $last_24h = time() - (24 * 3600);

            foreach ($jails_raw as $jail_row) {
                $jail_name = $jail_row['name'];
                $banned_ips = [];

                try {
                    $ban_query = $pdo->prepare("SELECT ip, timeofban, datetime(timeofban, 'unixepoch') as ban_time FROM bans WHERE jail = ?");
                    $ban_query->execute([$jail_name]);
                    $bans = $ban_query->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($bans as $b) {
                        $banned_ips[] = [
                            'ip' => $b['ip'],
                            'time' => $b['ban_time'] ?? ''
                        ];

                        // Ha az elmúlt 24 órán belül történt, beletesszük az óránkénti statisztikába
                        if ($b['timeofban'] >= $last_24h) {
                            $hour_str = date('H:00', $b['timeofban']);
                            if (!isset($hourly_stats[$hour_str][$jail_name])) {
                                $hourly_stats[$hour_str][$jail_name] = 0;
                            }
                            $hourly_stats[$hour_str][$jail_name]++;
                        }
                    }
                } catch (Exception $ex) {
                    // Fallback socketre
                    $j_res = run_fail2ban_cmd(['status', $jail_name]);
                    if ($j_res['success']) {
                        foreach ($j_res['raw'] as $line) {
                            if (strpos($line, 'Banned IP list:') !== false) {
                                $parts = explode(':', $line);
                                if (isset($parts[1])) {
                                    $ips = explode(' ', trim($parts[1]));
                                    foreach ($ips as $ip) {
                                        $clean_ip = trim($ip);
                                        if (!empty($clean_ip)) {
                                            $banned_ips[] = ['ip' => $clean_ip, 'time' => 'N/A'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $jail_details[] = [
                    'name' => $jail_name,
                    'banned_count' => count($banned_ips),
                    'banned_ips' => $banned_ips
                ];
            }

            echo json_encode([
                'status' => 'success',
                'jails' => $jail_details,
                'hourly_stats' => $hourly_stats
            ]);
            break;

        case 'ban':
            $jail = $_POST['jail'] ?? '';
            $ip = $_POST['ip'] ?? '';
            if (empty($jail) || empty($ip)) throw new Exception($t['err_missing_params']);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) throw new Exception($t['err_invalid_ip']);

            $res = run_fail2ban_cmd(['set', $jail, 'banip', $ip]);
            if (!$res['success']) throw new Exception($t['err_socket'] . $res['output']);

            echo json_encode(['status' => 'success', 'message' => sprintf($t['success_ban'], $ip, $jail)]);
            break;

        case 'unban':
            $jail = $_POST['jail'] ?? '';
            $ip = $_POST['ip'] ?? '';
            if (empty($jail) || empty($ip)) throw new Exception($t['err_missing_params']);

            $res = run_fail2ban_cmd(['set', $jail, 'unbanip', $ip]);
            if (!$res['success']) throw new Exception($t['err_socket'] . $res['output']);

            echo json_encode(['status' => 'success', 'message' => sprintf($t['success_unban'], $ip, $jail)]);
            break;

        case 'logs':
            $log_file = '/var/log/fail2ban.log';
            $lines = [];
            if (file_exists($log_file) && is_readable($log_file)) {
                exec("tail -n 50 " . escapeshellarg($log_file), $lines);
            } else {
                $lines = ["A log fájl nem olvasható."];
            }
            echo json_encode(['status' => 'success', 'logs' => $lines]);
            break;

        default:
            throw new Exception($t['unknown_action']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}