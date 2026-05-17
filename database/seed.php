<?php
/**
 * CampusWire — Database Seed Script
 * Run from browser: http://localhost/campuswire-php/public/seed.php
 * Or CLI: php database/seed.php
 *
 * Creates demo users, news articles, events, discussions, blogs, and alerts.
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';

$db = Database::connect();

echo "<pre>\n";
echo "═══════════════════════════════════════\n";
echo "  CampusWire — Seeding Database\n";
echo "═══════════════════════════════════════\n\n";

// ── Users ────────────────────────────────────────
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
echo "✅ Users seeded (" . count($users) . " users)\n";

// Get user IDs
$adminId   = $db->query("SELECT id FROM users WHERE email='admin@campuswire.com'")->fetchColumn();
$facultyId = $db->query("SELECT id FROM users WHERE email='sharma@campuswire.com'")->fetchColumn();
$studentId = $db->query("SELECT id FROM users WHERE email='karthik@campuswire.com'")->fetchColumn();

// ── News ─────────────────────────────────────────
$newsItems = [
    [$adminId,   'Spring 2026 Registration Opens',                    'Priority registration for seniors begins Monday.',   'Priority registration for seniors and graduate students begins this coming Monday at 8 AM. All students should check their academic standing before registering. Financial holds must be cleared beforehand.', 'academic',  'all', 'normal',    'approved'],
    [$facultyId, 'New AI Lab Opening Ceremony',                       'State-of-the-art AI research facility.',             'Join us tomorrow at the Science Building for the ribbon-cutting ceremony of the new state-of-the-art Artificial Intelligence research lab. The lab features GPU clusters, VR stations, and collaborative workspaces. Refreshments provided.', 'events',   'CSE', 'normal',    'approved'],
    [$adminId,   'Campus Closed — Extreme Weather Warning',           'All operations suspended due to severe weather.',    'Due to severe weather conditions, all campus operations will be suspended effective immediately. Classes are canceled and administrative offices will remain closed until further notice. Stay safe.', 'general',  'all', 'emergency', 'approved'],
    [$facultyId, 'Placement Drive: Google & Microsoft',               'Top tech companies visiting next week.',             'Google and Microsoft will be conducting on-campus interviews starting next Monday. Eligible students should register through the placement portal by Friday. Dress code: Business formal.', 'placement', 'all', 'urgent',    'approved'],
    [$adminId,   'Cultural Festival "Utsav" Dates Announced',         'Annual cultural fest from Feb 20-23.',               'We are thrilled to announce that the annual cultural festival "Utsav" will be held from February 20-23, 2026. Event registrations open next week. Categories include: Dance, Music, Drama, Art Exhibition, and Food Stalls.', 'cultural',  'all', 'normal',    'approved'],
    [$studentId, 'Intramural Basketball Signups',                     'Winter league registration closes Friday.',          'Grab your friends and form a team! Registration for the Winter IM Basketball league closes this Friday. All skill levels welcome. Games will be played on weekday evenings at the Recreation Center.', 'sports',    'all', 'normal',    'pending'],
];

$stmt = $db->prepare('INSERT IGNORE INTO news (author_id, title, summary, content, category, department, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($newsItems as $n) {
    $stmt->execute($n);
}
echo "✅ News seeded (" . count($newsItems) . " articles)\n";

// ── Events ───────────────────────────────────────
$events = [
    [$adminId,   'Tech Symposium 2026',           'Annual technology conference with keynote speakers and workshops.',                '2026-04-25', '10:00', 'Main Hall',      'Workshops', 142],
    [$facultyId, 'Startup Networking Night',       'Connect with founders and investors in the innovation ecosystem.',                '2026-04-28', '18:30', 'Innovation Hub',  'Social',    84],
    [$facultyId, 'React Performance Workshop',     'Deep dive into React optimization techniques, memoization, and lazy loading.',    '2026-05-02', '16:00', 'CS Lab 304',      'Academics', 56],
];

$stmt = $db->prepare('INSERT IGNORE INTO events (author_id, title, description, event_date, event_time, location, type, attendees) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
foreach ($events as $e) {
    $stmt->execute($e);
}
echo "✅ Events seeded (" . count($events) . " events)\n";

// ── Blogs ────────────────────────────────────────
$blogs = [
    [$studentId, 'Understanding React Server Components', 'Server components are shifting the paradigm in modern React development by reducing bundle sizes...', 'Server components are shifting the paradigm in modern React development by reducing bundle sizes and improving performance. In this article, we explore the architecture behind RSC, how they differ from traditional components, and practical use cases for building faster web applications.'],
    [$studentId, 'My Meta Software Engineering Internship', 'A deep dive into my 12-week journey working on the Instagram infrastructure team...', 'A deep dive into my 12-week journey working on the Instagram infrastructure team, the interview process, and lessons learned. I cover the technical challenges, mentorship experience, and how I prepared for the role from scratch as a college junior.'],
];

$stmt = $db->prepare('INSERT IGNORE INTO blogs (author_id, title, excerpt, content) VALUES (?, ?, ?, ?)');
foreach ($blogs as $b) {
    $stmt->execute($b);
}
echo "✅ Blogs seeded (" . count($blogs) . " posts)\n";

// ── Discussions ──────────────────────────────────
$discussions = [
    [$studentId, 'Does anyone have recommendations for the elective "Intro to AI" next semester? I\'m trying to decide between that and Web Dev.', 'Academics,CS', 32, 14],
    [$studentId, 'Found a lost AirPod case outside the library café. Left it at the front desk if anyone is looking for it!', 'Lost & Found', 110, 2],
];

$stmt = $db->prepare('INSERT IGNORE INTO discussions (user_id, content, tags, likes, replies) VALUES (?, ?, ?, ?, ?)');
foreach ($discussions as $d) {
    $stmt->execute($d);
}
echo "✅ Discussions seeded (" . count($discussions) . " posts)\n";

// ── Alerts ───────────────────────────────────────
$alerts = [
    [$adminId, 'Weather Advisory',     'Heavy rain expected this evening. Avoid open areas and seek shelter indoors.', 'warning', 'medium', 'all'],
    [$adminId, 'Server Maintenance',   'The student portal will be under maintenance from 2-4 AM tonight.',           'info',    'low',    'all'],
];

$stmt = $db->prepare('INSERT IGNORE INTO alerts (created_by, title, message, type, severity, target_audience) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($alerts as $a) {
    $stmt->execute($a);
}
echo "✅ Alerts seeded (" . count($alerts) . " alerts)\n";

echo "\n═══════════════════════════════════════\n";
echo "  ✅ Seeding Complete!\n";
echo "═══════════════════════════════════════\n";
echo "\nDemo Accounts:\n";
echo "  Admin:   admin@campuswire.com   / password123\n";
echo "  Faculty: sharma@campuswire.com  / password123\n";
echo "  Student: karthik@campuswire.com / password123\n";
echo "</pre>\n";
