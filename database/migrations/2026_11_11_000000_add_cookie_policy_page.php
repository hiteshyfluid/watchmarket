<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('pages')->where('slug', 'cookie-policy')->exists()) {
            return;
        }

        DB::table('pages')->insert([
            'slug' => 'cookie-policy',
            'title' => 'Cookie Policy',
            'content' => $this->content(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'cookie-policy')->delete();
    }

    private function content(): string
    {
        return <<<'HTML'
<p>This Cookie Policy explains how WatchMarket uses cookies and similar technologies when you visit our website.</p>

<h2>What are cookies?</h2>
<p>Cookies are small text files placed on your device when you visit a website. They help the site work properly, keep you signed in and keep your account secure.</p>

<h2>Cookies we use</h2>
<p>We only use cookies that are strictly necessary for the website to work. We do not use advertising or tracking cookies.</p>
<ul>
    <li><strong>Session cookie</strong>: keeps you signed in as you move between pages and remembers what you've entered in forms. It is deleted when your session ends.</li>
    <li><strong>XSRF-TOKEN</strong>: protects you against cross-site request forgery attacks when you submit forms. It is deleted when your session ends.</li>
    <li><strong>Remember me</strong>: if you tick "Remember me" when you log in, this keeps you signed in on that device until you log out.</li>
    <li><strong>Cookie notice preference</strong>: remembers that you've dismissed our cookie notice so we don't show it again.</li>
</ul>

<h2>Third-party cookies</h2>
<p>When you pay for a listing package or membership, payments are processed by Stripe. Stripe may set its own cookies to process the payment securely and prevent fraud. See <a href="https://stripe.com/cookies-policy/legal" target="_blank" rel="noopener">Stripe's Cookie Policy</a> for details.</p>

<h2>Managing cookies</h2>
<p>You can block or delete cookies in your browser settings. Because the cookies we use are strictly necessary, blocking them may stop parts of the website, such as signing in or posting an advert, from working.</p>

<h2>Changes to this policy</h2>
<p>We may update this policy from time to time. Any changes will be posted on this page.</p>

<h2>Contact us</h2>
<p>If you have any questions about our use of cookies, please <a href="/contact-us">contact us</a>.</p>
HTML;
    }
};
