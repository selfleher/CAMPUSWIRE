<?php $pageTitle = 'CampusWire — Smart Campus Ecosystem · BIT Durg'; ?>
<?php require __DIR__ . '/layouts/header.php'; ?>

<style>
/* ── Landing Page Specific Styles ───────── */
:root {
    --lp-bg: #FFFFFF;
    --lp-text: #111827;
    --lp-muted: #6B7280;
    --lp-border: #E5E7EB;
    --lp-accent: #111827;
}

body { background: var(--lp-bg); color: var(--lp-text); }

.lp-glass {
    background: #FFFFFF;
    border: 1px solid var(--lp-border);
    border-radius: 12px;
}

@keyframes slide-up { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

.slide-up { animation: slide-up 0.5s ease forwards; opacity:0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }
</style>

<!-- ══════ TOP NAV ══════ -->
<nav style="position:sticky;top:0;z-index:200;background:#FFFFFF;border-bottom:1px solid var(--lp-border);padding:14px 40px;display:flex;justify-content:space-between;align-items:center;">
    <div style="display:flex;align-items:center;gap:32px;">
        <div style="font-size:20px;font-weight:800;letter-spacing:-0.5px;color:var(--lp-text);">
            CampusWire
        </div>
        <div style="display:flex;gap:24px;" class="lp-nav-links">
            <a href="#features" style="font-size:14px;font-weight:500;color:var(--lp-muted);">Features</a>
            <a href="#clubs" style="font-size:14px;font-weight:500;color:var(--lp-muted);">Clubs</a>
            <a href="#about" style="font-size:14px;font-weight:500;color:var(--lp-muted);">About</a>
        </div>
    </div>
    <div style="display:flex;gap:12px;align-items:center;">
        <a href="<?= $appUrl ?>/auth/login" style="font-size:14px;font-weight:600;color:var(--lp-text);padding:8px 18px;">Sign In</a>
        <a href="<?= $appUrl ?>/auth/register"
           style="background:var(--lp-text);color:#fff;font-size:14px;font-weight:600;padding:10px 22px;border-radius:8px;text-decoration:none;transition:all 0.2s;"
           onmouseover="this.style.opacity='0.9'"
           onmouseout="this.style.opacity='1'">
            Get Started &rarr;
        </a>
    </div>
</nav>

<!-- ══════ HERO SECTION ══════ -->
<section style="position:relative;min-height:90vh;display:flex;align-items:center;overflow:hidden;padding:80px 40px;">
    <div style="max-width:1200px;margin:0 auto;width:100%;display:flex;align-items:center;gap:60px;flex-wrap:wrap;position:relative;z-index:1;">
        
        <!-- Left: Copy -->
        <div style="flex:1;min-width:300px;">
            <div class="slide-up" style="display:inline-flex;align-items:center;gap:10px;border:1px solid var(--lp-border);border-radius:20px;padding:6px 16px;margin-bottom:24px;font-size:13px;font-weight:600;color:var(--lp-text);">
                BIT Durg Platform
            </div>

            <h1 class="slide-up delay-1" style="font-size:clamp(40px,6vw,68px);font-weight:800;line-height:1.1;letter-spacing:-1.5px;margin-bottom:24px;">
                One Platform for<br>Everything Campus
            </h1>

            <p class="slide-up delay-2" style="font-size:18px;color:var(--lp-muted);line-height:1.7;margin-bottom:40px;max-width:520px;">
                Stay connected with verified news, discover events, join clubs, and engage in real campus discussions — all in one clean platform built for BIT Durg students.
            </p>

            <div class="slide-up delay-3" style="display:flex;gap:16px;flex-wrap:wrap;">
                <a href="<?= $appUrl ?>/auth/register"
                   style="background:var(--lp-text);color:#fff;font-size:16px;font-weight:600;padding:16px 32px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;transition:all 0.2s;"
                   onmouseover="this.style.opacity='0.9'"
                   onmouseout="this.style.opacity='1'">
                    Join CampusWire
                </a>
                <a href="<?= $appUrl ?>/auth/login"
                   style="background:#fff;border:1px solid var(--lp-border);color:var(--lp-text);font-size:16px;font-weight:600;padding:16px 32px;border-radius:8px;text-decoration:none;transition:all 0.2s;"
                   onmouseover="this.style.background='#F9FAFB'"
                   onmouseout="this.style.background='#fff'">
                    Sign In &rarr;
                </a>
            </div>
            
            <div class="slide-up delay-3" style="display:flex;gap:28px;margin-top:48px;padding-top:32px;border-top:1px solid var(--lp-border);flex-wrap:wrap;">
                <?php foreach ([['1200+','Students'],['7','Clubs'],['50+','Events'],['>4.8★','Rating']] as $s): ?>
                <div>
                    <div style="font-size:24px;font-weight:800;color:var(--lp-text);"><?= $s[0] ?></div>
                    <div style="font-size:13px;color:var(--lp-muted);margin-top:2px;"><?= $s[1] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Clean Minimal UI Frame -->
        <div class="slide-up delay-2" style="flex:1;min-width:300px;max-width:480px;">
            <div style="border:1px solid var(--lp-border); border-radius:12px; background:#fff; padding:24px; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                    <div style="width:40px;height:40px;background:#F3F4F6;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;">📅</div>
                    <div>
                        <div style="font-weight:600;font-size:14px;">Campus Meeting</div>
                        <div style="font-size:12px;color:var(--lp-muted);">Main Auditorium · 180 registered</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="border:1px solid var(--lp-border); padding:16px; border-radius:8px;">
                        <div style="font-size:20px;margin-bottom:6px;">👥</div>
                        <div style="font-weight:600;font-size:14px;">GDSC</div>
                        <div style="font-size:12px;color:var(--lp-muted);">520 Members</div>
                    </div>
                    <div style="border:1px solid var(--lp-border); padding:16px; border-radius:8px;">
                        <div style="font-size:20px;margin-bottom:6px;">🎓</div>
                        <div style="font-weight:600;font-size:14px;">Updates</div>
                        <div style="font-size:12px;color:var(--lp-muted);">3 new alerts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════ MARQUEE TICKER ══════ -->
<div style="background:#F9FAFB;padding:12px 0;border-top:1px solid var(--lp-border);border-bottom:1px solid var(--lp-border);overflow:hidden;">
    <div style="display:flex;animation:marquee 25s linear infinite;">
        <?php
        $tickers = ['Techno Hub wins SIH 2025','Exam schedule released','PAC presents "Echoes of Time"','GDSC chapter update','Cultural fest registration','Hackathon on May 10th'];
        $all = array_merge($tickers, $tickers);
        foreach ($all as $t): ?>
        <span style="white-space:nowrap;padding:0 40px;font-size:13px;font-weight:500;color:var(--lp-text);border-right:1px solid var(--lp-border);"><?= $t ?></span>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══════ FEATURES SECTION ══════ -->
<section id="features" style="padding:100px 40px;background:#FFFFFF;">
    <div style="max-width:1100px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:64px;">
            <h2 style="font-size:clamp(32px,4vw,48px);font-weight:800;letter-spacing:-1px;margin-bottom:16px;">
                Everything you need
            </h2>
            <p style="font-size:17px;color:var(--lp-muted);max-width:500px;margin:0 auto;">A complete campus platform.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:24px;">
            <?php
            $features = [
                ['Live News Feed','Get instant access to verified campus announcements, news, and emergency alerts.'],
                ['Events & RSVP','Discover upcoming workshops and tournaments. Register with one tap.'],
                ['Campus Clubs','Explore campus clubs, send join requests, and get access to exclusive communities.'],
                ['Community Forum','Start discussions, share ideas, and engage with students and faculty.'],
                ['Student Blogs','Publish your articles and projects. Build your campus portfolio.'],
                ['Smart Alerts','Never miss a critical update. Emergency notifications and schedule changes.']
            ];
            foreach ($features as $f): ?>
            <div class="lp-glass" style="padding:28px;">
                <h3 style="font-size:16px;font-weight:600;margin-bottom:10px;color:var(--lp-text);"><?= $f[0] ?></h3>
                <p style="font-size:14px;color:var(--lp-muted);line-height:1.6;"><?= $f[1] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════ CLUBS SHOWCASE ══════ -->
<section id="clubs" style="padding:100px 40px;background:#F9FAFB;border-top:1px solid var(--lp-border);">
    <div style="max-width:1100px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-size:clamp(28px,4vw,44px);font-weight:800;letter-spacing:-1px;margin-bottom:14px;">
                Campus Clubs
            </h2>
        </div>
        <div style="display:flex;gap:16px;overflow:hidden;">
            <div style="display:flex;gap:16px;animation:marquee 20s linear infinite;">
                <?php
                $clubs = [
                    ['Astro Club','120 members'],['Clicks Club','185 members'],['Techno Hub','450 members'],
                    ['PAC','150 members'],['GDSC','520 members'],['Vista Club','110 members'],['Quizbizz','95 members'],
                    ['Astro Club','120 members'],['Clicks Club','185 members'],['Techno Hub','450 members'],
                    ['PAC','150 members'],['GDSC','520 members'],['Vista Club','110 members'],['Quizbizz','95 members']
                ];
                foreach ($clubs as $c): ?>
                <div style="flex-shrink:0;background:#FFFFFF;border:1px solid var(--lp-border);border-radius:12px;padding:20px 24px;min-width:180px;text-align:center;">
                    <div style="font-weight:600;font-size:14px;color:var(--lp-text);margin-bottom:4px;"><?= $c[0] ?></div>
                    <div style="font-size:12px;color:var(--lp-muted);"><?= $c[1] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ══════ ABOUT / BIT DURG ══════ -->
<section id="about" style="padding:100px 40px;background:#FFFFFF;border-top:1px solid var(--lp-border);">
    <div style="max-width:1100px;margin:0 auto;display:flex;gap:64px;align-items:center;flex-wrap:wrap;">
        <div style="flex:1;min-width:280px;">
            <h2 style="font-size:clamp(28px,4vw,44px);font-weight:800;letter-spacing:-1px;margin-bottom:20px;">
                BIT Durg
            </h2>
            <p style="font-size:16px;color:var(--lp-muted);line-height:1.7;margin-bottom:20px;">
                Established in 1986, BIT Durg is one of Central India's premier engineering institutions with a legacy of excellence in academics, research, and innovation.
            </p>
            <p style="font-size:16px;color:var(--lp-muted);line-height:1.7;margin-bottom:32px;">
                CampusWire was built to digitize campus life — bringing every student, faculty member, and administrator onto one seamless, modern platform.
            </p>
        </div>
    </div>
</section>

<!-- ══════ CTA SECTION ══════ -->
<section style="padding:100px 40px;background:#F9FAFB;border-top:1px solid var(--lp-border);">
    <div style="max-width:800px;margin:0 auto;text-align:center;">
        <h2 style="font-size:clamp(32px,5vw,56px);font-weight:800;letter-spacing:-1.5px;margin-bottom:20px;line-height:1.1;">
            Ready to join?
        </h2>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= $appUrl ?>/auth/register"
               style="background:var(--lp-text);color:#fff;font-size:16px;font-weight:600;padding:16px 32px;border-radius:8px;text-decoration:none;">
                Create Free Account
            </a>
            <a href="<?= $appUrl ?>/auth/login"
               style="background:#fff;border:1px solid var(--lp-border);color:var(--lp-text);font-size:16px;font-weight:600;padding:16px 32px;border-radius:8px;text-decoration:none;">
                Sign In
            </a>
        </div>
    </div>
</section>

<!-- ══════ FOOTER ══════ -->
<footer style="background:#FFFFFF;padding:40px;border-top:1px solid var(--lp-border);">
    <div style="max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:24px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:var(--lp-text);">CampusWire</div>
            <div style="font-size:13px;color:var(--lp-muted);">BIT Durg · <?= date('Y') ?></div>
        </div>
    </div>
</footer>

<?php require __DIR__ . '/layouts/footer.php'; ?>
