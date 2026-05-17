<?php
/**
 * CampusWire — Database Seed Script (PUBLIC ACCESS)
 * Access via: http://localhost/campuswire-php/public/setup.php
 *
 * This script:
 * 1. Creates the database tables (runs schema.sql)
 * 2. Inserts demo users, news, events, blogs, discussions and alerts
 *
 * ⚠️  DELETE this file after first-time setup for security.
 */

// Security: Simple one-time-use key to prevent public misuse
$SETUP_KEY = 'campuswire2026';
if (!isset($_GET['key']) || $_GET['key'] !== $SETUP_KEY) {
    echo '<!DOCTYPE html><html><head><title>Setup</title>
    <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#F9FAFB;}
    .box{background:#fff;padding:40px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);max-width:480px;width:100%;}
    input,button{width:100%;padding:12px;border-radius:8px;border:1.5px solid #E5E7EB;font-size:14px;margin-top:10px;}
    button{background:#4F46E5;color:#fff;border:none;cursor:pointer;font-weight:600;}</style>
    </head><body><div class="box">
    <h2 style="margin-bottom:8px;">CampusWire Setup</h2>
    <p style="color:#6B7280;font-size:14px;margin-bottom:20px;">Enter the setup key to initialize the database.</p>
    <form method="GET">
        <input type="text" name="key" placeholder="Setup key: campuswire2026" required autofocus>
        <button type="submit">Run Setup</button>
    </form>
    </div></body></html>';
    exit;
}

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

$db = Database::connect();

echo '<!DOCTYPE html><html><head><title>Setup — CampusWire</title>
<style>
  body{font-family:"Inter",sans-serif;background:#F9FAFB;padding:40px 20px;}
  .box{background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);max-width:700px;margin:auto;}
  pre{background:#0F172A;color:#7DD3FC;padding:20px;border-radius:10px;font-size:13px;overflow-x:auto;white-space:pre-wrap;}
  h1{color:#111827;font-size:22px;margin-bottom:8px;}
  .ok{color:#10B981;} .err{color:#EF4444;} .hdr{color:#F59E0B;}
  table{width:100%;border-collapse:collapse;margin-top:20px;}
  th,td{padding:10px 14px;text-align:left;border-bottom:1px solid #F1F5F9;font-size:14px;}
  th{background:#F8FAFC;font-weight:600;color:#546E7A;font-size:12px;text-transform:uppercase;}
  .badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
  .badge-admin{background:#F3E8FF;color:#7C3AED;}
  .badge-faculty{background:#D1FAE5;color:#065F46;}
  .badge-student{background:#DBEAFE;color:#1D4ED8;}
</style></head>
<body><div class="box">
<h1>🚀 CampusWire Database Setup</h1>
<p style="color:#6B7280;font-size:14px;margin-bottom:24px;">This script will create all tables and seed demo data.</p>
<pre>';

$output = [];
$errors = 0;

// ── Step 1: Create Tables ─────────────────────────
$output[] = '<span class="hdr">═══ Step 1: Creating Tables ═══</span>';

$schemaFile = __DIR__ . '/../database/schema.sql';
if (!file_exists($schemaFile)) {
    $output[] = '<span class="err">✗ schema.sql not found!</span>';
    $errors++;
} else {
    $sql = file_get_contents($schemaFile);
    // Split by semicolon and execute each statement
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => !empty($s) && !str_starts_with($s, '--')
    );

    foreach ($statements as $stmt) {
        if (empty(trim($stmt))) continue;
        try {
            $db->exec($stmt);
        } catch (PDOException $e) {
            // Ignore "already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false) {
                $output[] = '<span class="err">✗ SQL Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
                $errors++;
            }
        }
    }
    $output[] = '<span class="ok">✓ All tables created successfully</span>';
}

// ── Step 2: Seed Users ────────────────────────────
$output[] = '';
$output[] = '<span class="hdr">═══ Step 2: Seeding Demo Data ═══</span>';

$password = password_hash('password123', PASSWORD_BCRYPT);

$users = [
    ['Admin User',    'admin@campuswire.com',    $password, 'admin'],
    ['Prof. Sharma',  'sharma@campuswire.com',   $password, 'faculty'],
    ['Dr. Verma',     'verma@campuswire.com',    $password, 'faculty'],
    ['Karthik Mohan', 'karthik@campuswire.com',  $password, 'student'],
    ['Sarah Jenkins', 'sarah@campuswire.com',    $password, 'student'],
    ['Marcus Wright', 'marcus@campuswire.com',   $password, 'student'],
];

$stmt = $db->prepare('INSERT IGNORE INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
foreach ($users as $u) {
    $stmt->execute($u);
}
$output[] = '<span class="ok">✓ Users seeded (' . count($users) . ' accounts)</span>';

// Get user IDs
$adminId   = $db->query("SELECT id FROM users WHERE email='admin@campuswire.com'")->fetchColumn();
$facultyId = $db->query("SELECT id FROM users WHERE email='sharma@campuswire.com'")->fetchColumn();
$studentId = $db->query("SELECT id FROM users WHERE email='karthik@campuswire.com'")->fetchColumn();

if (!$adminId || !$facultyId || !$studentId) {
    $output[] = '<span class="err">✗ Could not retrieve user IDs. Users may already exist.</span>';
}

// ── News ─────────────────────────────────────────
$newsItems = [
    [$adminId,   'Spring 2026 Registration Opens',         'Priority registration for seniors begins Monday.', 'Priority registration for seniors and graduate students begins this coming Monday at 8 AM. All students should check their academic standing before registering. Financial holds must be cleared beforehand.', 'academic',  'all', 'normal',    'approved'],
    [$facultyId, 'New AI Lab Opening Ceremony',            'State-of-the-art AI research facility.',           'Join us tomorrow at the Science Building for the ribbon-cutting ceremony of the new state-of-the-art Artificial Intelligence research lab. The lab features GPU clusters, VR stations, and collaborative workspaces. Refreshments provided.', 'events',   'CSE', 'normal',    'approved'],
    [$adminId,   'Campus Closed — Extreme Weather Warning','All operations suspended due to severe weather.',   'Due to severe weather conditions, all campus operations will be suspended effective immediately. Classes are canceled and administrative offices will remain closed until further notice. Stay safe.', 'general',  'all', 'emergency', 'approved'],
    [$facultyId, 'Placement Drive: Google & Microsoft',    'Top tech companies visiting next week.',            'Google and Microsoft will be conducting on-campus interviews starting next Monday. Eligible students should register through the placement portal by Friday. Dress code: Business formal.', 'placement', 'all', 'urgent',    'approved'],
    [$adminId,   'Cultural Festival "Utsav" Dates Announced', 'Annual cultural fest from Feb 20-23.',          'We are thrilled to announce that the annual cultural festival "Utsav" will be held from February 20-23, 2026. Event registrations open next week. Categories include: Dance, Music, Drama, Art Exhibition, and Food Stalls.', 'cultural',  'all', 'normal',    'approved'],
    [$studentId, 'Intramural Basketball Signups',          'Winter league registration closes Friday.',        'Grab your friends and form a team! Registration for the Winter IM Basketball league closes this Friday. All skill levels welcome. Games will be played on weekday evenings at the Recreation Center.', 'sports',    'all', 'normal',    'pending'],
];

$stmt = $db->prepare('INSERT IGNORE INTO news (author_id, title, summary, content, category, department, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($newsItems as $n) {
    $stmt->execute($n);
}
$output[] = '<span class="ok">✓ News seeded (' . count($newsItems) . ' articles)</span>';

// ── Events ───────────────────────────────────────
$events = [
    [$adminId,   'Tech Symposium 2026',        'Annual technology conference with keynote speakers and workshops.',             '2026-04-25', '10:00', 'Main Hall',      'Workshops', 142],
    [$facultyId, 'Startup Networking Night',    'Connect with founders and investors in the innovation ecosystem.',            '2026-04-28', '18:30', 'Innovation Hub',  'Social',    84],
    [$facultyId, 'React Performance Workshop',  'Deep dive into React optimization techniques, memoization, and lazy loading.','2026-05-02', '16:00', 'CS Lab 304',      'Academics', 56],
    [$adminId,   'Annual Sports Meet',          'Inter-department sports competition. Register your teams now.',               '2026-05-10', '09:00', 'Sports Ground',   'Sports',    220],
];

$stmt = $db->prepare('INSERT IGNORE INTO events (author_id, title, description, event_date, event_time, location, type, attendees) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($events as $e) {
    $stmt->execute($e);
}
$output[] = '<span class="ok">✓ Events seeded (' . count($events) . ' events)</span>';

// ── Blogs ─────────────────────────────────────────
$blogs = [
    [$studentId, 'Understanding React Server Components', 'Server components are shifting the paradigm in modern React development by reducing bundle sizes...', 'Server components are shifting the paradigm in modern React development by reducing bundle sizes and improving performance. In this article, we explore the architecture behind RSC, how they differ from traditional components, and practical use cases for building faster web applications.'],
    [$studentId, 'My Meta Software Engineering Internship', 'A deep dive into my 12-week journey working on the Instagram infrastructure team...', 'A deep dive into my 12-week journey working on the Instagram infrastructure team, the interview process, and lessons learned. I cover the technical challenges, mentorship experience, and how I prepared for the role from scratch as a college junior.'],
    [$facultyId, 'Why Every Student Should Learn SQL', 'SQL remains one of the most valuable skills a computer science student can possess...', 'SQL remains one of the most valuable skills a computer science student can possess. Despite the rise of NoSQL databases, relational data modeling is foundational. This article explores the top SQL concepts every CS student should master before graduation: JOINs, subqueries, window functions, and query optimization.'],
];

$stmt = $db->prepare('INSERT IGNORE INTO blogs (author_id, title, excerpt, content) VALUES (?, ?, ?, ?)');
foreach ($blogs as $b) {
    $stmt->execute($b);
}
$output[] = '<span class="ok">✓ Blogs seeded (' . count($blogs) . ' posts)</span>';

// ── Discussions ───────────────────────────────────
$discussions = [
    [$studentId, 'Does anyone have recommendations for the elective "Intro to AI" next semester? I\'m trying to decide between that and Web Dev.', 'Academics,CS', 32, 14],
    [$studentId, 'Found a lost AirPod case outside the library café. Left it at the front desk if anyone is looking for it!', 'Lost & Found', 110, 2],
    [$studentId, 'Tips for surviving finals week? Share your best study strategies!', 'Academics,Tips', 87, 23],
];

$stmt = $db->prepare('INSERT IGNORE INTO discussions (user_id, content, tags, likes, replies) VALUES (?, ?, ?, ?, ?)');
foreach ($discussions as $d) {
    $stmt->execute($d);
}
$output[] = '<span class="ok">✓ Discussions seeded (' . count($discussions) . ' posts)</span>';

// ── Alerts ────────────────────────────────────────
$alerts = [
    [$adminId, 'Weather Advisory',   'Heavy rain expected this evening. Avoid open areas and seek shelter indoors.', 'warning', 'medium', 'all'],
    [$adminId, 'Server Maintenance', 'The student portal will be under maintenance from 2-4 AM tonight.',           'info',    'low',    'all'],
    [$adminId, 'Exam Hall Change',   'The final exam for CS401 has been moved from Hall A to Hall C.',               'warning', 'high',   'students'],
];

$stmt = $db->prepare('INSERT IGNORE INTO alerts (created_by, title, message, type, severity, target_audience) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($alerts as $a) {
    $stmt->execute($a);
}
$output[] = '<span class="ok">✓ Alerts seeded (' . count($alerts) . ' alerts)</span>';

// Create upload directories
foreach ([
    __DIR__ . '/uploads/profile',
    __DIR__ . '/uploads/news',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        $output[] = '<span class="ok">✓ Created directory: ' . basename($dir) . '</span>';
    }
}

echo implode("\n", $output);
echo '</pre>';

if ($errors === 0) {
    echo '<div style="background:#D1FAE5;border:1px solid #10B981;border-radius:10px;padding:16px;margin-top:20px;color:#065F46;">
    <strong>✅ Setup Complete!</strong> Your database is ready. You can now <a href="' . APP_URL . '/auth/login" style="color:#059669;font-weight:700;">log in</a>.
    </div>';
} else {
    echo '<div style="background:#FEE2E2;border:1px solid #EF4444;border-radius:10px;padding:16px;margin-top:20px;color:#991B1B;">
    <strong>⚠️ Setup completed with ' . $errors . ' error(s).</strong> Review the output above.
    </div>';
}

echo '<h3 style="margin-top:32px;font-size:16px;color:#111827;">🔐 Demo Login Credentials</h3>';
echo '<table>
<thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
<tbody>
<tr><td><span class="badge badge-admin">Admin</span></td><td>admin@campuswire.com</td><td>password123</td></tr>
<tr><td><span class="badge badge-faculty">Faculty</span></td><td>sharma@campuswire.com</td><td>password123</td></tr>
<tr><td><span class="badge badge-student">Student</span></td><td>karthik@campuswire.com</td><td>password123</td></tr>
</tbody></table>';

echo '<p style="margin-top:20px;font-size:13px;color:#94A3B8;">
⚠️ <strong>Security reminder:</strong> Delete or rename this file after first-time setup.<br>
<code>C:\xampp\htdocs\campuswire-php\public\setup.php</code>
</p>';

echo '</div></body></html>';
