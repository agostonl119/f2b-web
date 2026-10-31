<?php
/**
 * Fail2Ban Web Manager Backend API
 * (CIDR alhálózat és egyedi IP tiltás támogatással)
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'hu';
$allowed_langs = ['hu', 'en', 'zh'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'hu';
}

$file_path = __DIR__ . "/lang/{$lang}.php";
$t = file_exists($file_path) ? include $file_path : include __DIR__ . "/lang/hu.php";

$db_file = '/var/lib/fail2ban/fail2ban.sqlite3';
$log_file = '/var/log/fail2ban.log';

// Segédfüggvény: Ellenőrzi, hogy érvényes IP vagy CIDR formátum-e
function validate_ip_or_cidr($target) {
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        return true;
    }
    // CIDR ellenőrzés (pl. 192.168.5.0/24 vagy 2001:db8::/32)
    if (strpos($target, '/') !== false) {
        list($ip, $subnet) = explode('/', $target, 2);
        if (filter_var($ip, FILTER_VALIDATE_IP) && is_numeric($subnet) && $subnet >= 0 && $subnet <= 128) {
            return true;
        }
    }
    return false;
}

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
                throw new Exception($t['err_db'] . "A fájl nem létezik: " . $db_file);
            }

            $pdo = new PDO("sqlite:" . $db_file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $status_res = run_fail2ban_cmd(['status']);
            $jail_names = [];
            if ($status_res['success']) {
                foreach ($status_res['raw'] as $line) {
                    if (strpos($line, 'Jail list:') !== false) {
                        $parts = explode(':', $line);
                        if (isset($parts[1])) {
                            $jails_str = trim($parts[1]);
                            if ($jails_str !== '') {
                                $jail_names = array_map('trim', explode(',', $jails_str));
                            }
                        }
                    }
                }
            }

            $jails_data = [];
            foreach ($jail_names as $jname) {
                $stmt = $pdo->prepare("
                    SELECT ip, datetime(timeofban, 'unixepoch', 'localtime') as time_banned 
                    FROM bans 
                    WHERE jail = :jail 
                    ORDER BY timeofban DESC
                ");
                $stmt->execute([':jail' => $jname]);
                $banned_ips = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $banned_ips[] = [
                        'ip' => $row['ip'],
                        'time' => $row['time_banned']
                    ];
                }

                $jails_data[] = [
                    'name' => $jname,
                    'banned_count' => count($banned_ips),
                    'banned_ips' => $banned_ips
                ];
            }

            // Gördülő 24 órás statisztika
            $now = time();
            $start_window = $now - (24 * 3600);

            $hourly_stats = [];
            for ($i = 23; $i >= 0; $i--) {
                $timestamp = $now - ($i * 3600);
                $hour_key = date('H:00', $timestamp);
                $hourly_stats[$hour_key] = [];
                foreach ($jail_names as $jname) {
                    $hourly_stats[$hour_key][$jname] = 0;
                }
            }

            $stmt_hourly = $pdo->prepare("
                SELECT jail, timeofban 
                FROM bans 
                WHERE timeofban >= :start_window AND timeofban <= :now
            ");
            $stmt_hourly->execute([
                ':start_window' => $start_window,
                ':now' => $now
            ]);

            while ($row = $stmt_hourly->fetch(PDO::FETCH_ASSOC)) {
                $ban_time = (int)$row['timeofban'];
                $jname = $row['jail'];
                $hour_key = date('H:00', $ban_time);

                if (isset($hourly_stats[$hour_key])) {
                    if (!isset($hourly_stats[$hour_key][$jname])) {
                        $hourly_stats[$hour_key][$jname] = 0;
                    }
                    $hourly_stats[$hour_key][$jname]++;
                }
            }

            echo json_encode([
                'status' => 'success',
                'jails' => $jails_data,
                'hourly_stats' => $hourly_stats
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'logs':
            $logs = [];
            if (file_exists($log_file) && is_readable($log_file)) {
                $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines !== false) {
                    $logs = array_slice($lines, -50);
                }
            } else {
                $logs = [$t['log_not_readable'] . $log_file];
            }
            echo json_encode(['status' => 'success', 'logs' => $logs], JSON_UNESCAPED_UNICODE);
            break;

        case 'ban':
            $jail = $_POST['jail'] ?? '';
            $target = trim($_POST['ip'] ?? '');

            if (empty($jail) || empty($target)) {
                throw new Exception($t['err_missing_params']);
            }
            if (!validate_ip_or_cidr($target)) {
                throw new Exception($t['err_invalid_ip']);
            }

            $res = run_fail2ban_cmd(['set', $jail, 'banip', $target]);
            if ($res['success']) {
                echo json_encode(['status' => 'success', 'message' => sprintf($t['success_ban'], $target, $jail)], JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception($t['err_socket'] . $res['output']);
            }
            break;

        case 'unban':
            $jail = $_POST['jail'] ?? '';
            $target = trim($_POST['ip'] ?? '');

            if (empty($jail) || empty($target)) {
                throw new Exception($t['err_missing_params']);
            }
            if (!validate_ip_or_cidr($target)) {
                throw new Exception($t['err_invalid_ip']);
            }

            $res = run_fail2ban_cmd(['set', $jail, 'unbanip', $target]);
            if ($res['success']) {
                echo json_encode(['status' => 'success', 'message' => sprintf($t['success_unban'], $target, $jail)], JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception($t['err_socket'] . $res['output']);
            }
            break;

        default:
            throw new Exception($t['unknown_action']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}