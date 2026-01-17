<?php
/**
 * Reception-Verified Attendance Page
 * 
 * Members enter their ID, and reception PIN is required to confirm.
 * 
 * @package ShivajiPool
 * @version 1.1
 */

// Load configuration
require_once 'config/config.php';
require_once 'db_connect.php';

$page_title = 'Member Attendance';

// Fetch the PIN for today
$correct_pin = Attendance::get_daily_pin();

$response = null;

// Handle marking attendance
if (is_post_request()) {
    $member_identifier = sanitize_input($_POST['member_id_or_phone'] ?? '');
    $entered_pin = sanitize_input($_POST['reception_pin'] ?? '');
    
    if (empty($member_identifier)) {
        $response = ['success' => false, 'message' => 'Please enter your Member Code or Phone number.'];
    } elseif ($entered_pin !== $correct_pin) {
        $response = ['success' => false, 'message' => 'Incorrect Reception PIN. Please look at the display board at the reception desk.'];
    } else {
        // Find member by code or phone
        $query = "SELECT * FROM members WHERE member_code = ? OR phone = ? LIMIT 1";
        $member = db_fetch_one($query, 'ss', [$member_identifier, $member_identifier]);
        
        if (!$member) {
            $response = ['success' => false, 'message' => 'Member not found. Please check the code or contact desk.'];
        } else {
            $member_code = $member['member_code'];
            
            // Check if member is already inside
            $today = date('Y-m-d');
            $inside_query = "SELECT attendance_id FROM attendance 
                             WHERE member_id = ? AND attendance_date = ? AND exit_time IS NULL";
            $is_inside = db_fetch_one($inside_query, 'is', [$member['member_id'], $today]);
            
            if ($is_inside) {
                // Member is inside, mark EXIT
                $response = Attendance::mark_exit($member_code);
                $response['action'] = 'EXIT';
            } else {
                // Member is outside, mark ENTRY
                $response = Attendance::mark_entry($member_code);
                $response['action'] = 'ENTRY';
            }
            
            if ($response['success']) {
                $response['name'] = $member['first_name'] . ' ' . $member['last_name'];
                $response['expiry'] = $member['membership_end_date'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo POOL_NAME; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --bg-gradient: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }
        
        .attendance-container {
            width: 100%;
            max-width: 500px;
            perspective: 1000px;
        }
        
        .attendance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-box {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 32px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
        }
        
        h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        
        .badge-reception {
            background: #e9ecef;
            color: #495057;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .input-group-text {
            background: white;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: var(--primary-color);
        }
        
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            font-weight: 500;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }
        
        .pin-input-group .form-control {
            text-align: center;
            letter-spacing: 10px;
            font-size: 1.5rem;
            font-family: monospace;
        }
        
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 20px;
        }
        
        .key {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 15px;
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            transition: all 0.2s;
            cursor: pointer;
            user-select: none;
        }
        
        .key:active {
            background: #e9ecef;
            transform: scale(0.95);
        }
        
        .key.clear { color: var(--danger-color); }
        .key.backspace { color: var(--secondary-color); }
        
        .btn-submit {
            background: var(--primary-color);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 25px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
        }
        
        .btn-submit:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }
        
        .success-box {
            text-align: center;
            padding: 20px;
            border-radius: 20px;
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.2);
        }
        
        .icon-round {
            width: 80px;
            height: 80px;
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
        }
        
        .member-name { font-weight: 700; color: #1a1a1a; margin-top: 10px; }
        .member-info { font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body>

    <div class="attendance-container">
        <div class="attendance-card shadow">
            
            <?php if (!$response || !$response['success']): ?>
                <div class="header">
                    <div class="logo-box">
                        <i class="fa-solid fa-person-swimming"></i>
                    </div>
                    <h2><?php echo POOL_NAME; ?></h2>
                    <span class="badge-reception">Reception PIN Mode</span>
                </div>

                <?php if ($response && !$response['success']): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4 py-2 small">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $response['message']; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="attendanceForm">
                    <div class="mb-4">
                        <label class="form-label">Member ID / Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                            <input type="text" name="member_id_or_phone" id="member_id" class="form-control" 
                                   placeholder="Enter Code or Phone" required 
                                   value="<?php echo isset($_POST['member_id_or_phone']) ? clean($_POST['member_id_or_phone']) : ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Verification PIN</label>
                        <div class="pin-input-group">
                            <input type="password" name="reception_pin" id="pin_display" class="form-control" 
                                   placeholder="••••" maxlength="6" readonly required>
                        </div>
                    </div>

                    <!-- Keypad -->
                    <div class="keypad">
                        <div class="key" onclick="pressIdx(1)">1</div>
                        <div class="key" onclick="pressIdx(2)">2</div>
                        <div class="key" onclick="pressIdx(3)">3</div>
                        <div class="key" onclick="pressIdx(4)">4</div>
                        <div class="key" onclick="pressIdx(5)">5</div>
                        <div class="key" onclick="pressIdx(6)">6</div>
                        <div class="key" onclick="pressIdx(7)">7</div>
                        <div class="key" onclick="pressIdx(8)">8</div>
                        <div class="key" onclick="pressIdx(9)">9</div>
                        <div class="key clear" onclick="clearPin()"><i class="fa-solid fa-rotate-left"></i></div>
                        <div class="key" onclick="pressIdx(0)">0</div>
                        <div class="key backspace" onclick="backspacePin()"><i class="fa-solid fa-delete-left"></i></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-submit shadow">
                        Confirm Attendance <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
            <?php else: ?>
                <div class="success-box">
                    <div class="icon-round">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3 class="text-success mb-1">
                        <?php echo $response['action'] == 'ENTRY' ? 'Welcome!' : 'Goodbye!'; ?>
                    </h3>
                    <div class="member-name"><?php echo clean($response['name']); ?></div>
                    <div class="member-info mt-2">
                        <p class="mb-1 text-primary font-weight-bold"><?php echo $response['message']; ?></p>
                        <?php if ($response['expiry']): ?>
                            <small class="text-muted">Membership Expires: <?php echo format_date($response['expiry']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <a href="mark_attendance.php" class="btn btn-outline-success border-2 rounded-pill px-4 mt-4">Done</a>
                </div>

                <script>
                    // Auto-refresh after 4 seconds
                    setTimeout(function() {
                        window.location.href = 'mark_attendance.php';
                    }, 4000);
                </script>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const pinDisplay = document.getElementById('pin_display');
        
        function pressIdx(n) {
            if (pinDisplay.value.length < 6) {
                pinDisplay.value += n;
            }
        }
        
        function clearPin() {
            pinDisplay.value = '';
        }
        
        function backspacePin() {
            pinDisplay.value = pinDisplay.value.slice(0, -1);
        }

        // Support physical keyboard too
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                pressIdx(e.key);
            } else if (e.key === 'Backspace') {
                backspacePin();
            } else if (e.key === 'Escape') {
                clearPin();
            }
        });
    </script>
</body>
</html>
