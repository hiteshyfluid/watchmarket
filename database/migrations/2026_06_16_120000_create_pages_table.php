<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 190)->unique();
            $table->string('title', 255);
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        DB::table('pages')->insert([
            [
                'slug' => 'terms-conditions',
                'title' => 'Terms & Conditions',
                'content' => $this->termsContent(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => $this->privacyContent(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }

    private function termsContent(): string
    {
        return <<<'HTML'
<h2>Introduction</h2>
<p>These terms govern the relationship between users and Watch Market Limited regarding use of watchmarket.co.uk. Users must be at least 18 years old. Agreement is assumed upon first website use. The company defines "user" as any third party accessing the site who isn't employed by or contracted to Watch Market Limited.</p>

<h2>Intellectual Property And Acceptable Use</h2>
<p>Watch Market Limited owns all site content unless user-uploaded. Users may retrieve, display, and print content for personal, non-commercial purposes only. Prohibited actions include reproducing, modifying, or distributing content without written permission.</p>
<p>Users are responsible for all submitted content and warrant it's legal, original, and doesn't infringe rights. Prohibited uploads include confidential material, fraudulent content, obscene material, or anything illegal. Users must not impersonate others or use false information.</p>

<h2>Prohibited Use</h2>
<p>The site cannot be used in ways that cause damage, are harmful, unlawful, abusive, or harassing. This includes making unauthorized copies of copyrighted content.</p>

<h2>Registration</h2>
<p>Users must provide accurate, complete registration details and notify the company of any changes. Watch Market Limited may suspend or cancel accounts for reasonable purposes or term violations. Users may cancel anytime via email, forfeiting subscription credits.</p>

<h2>Password And Security</h2>
<p>Users must keep passwords confidential. The company may require password changes or account suspension if misuse is suspected.</p>

<h2>Links To Other Websites</h2>
<p>The site contains links to external sites not controlled by Watch Market Limited. The company assumes no responsibility for external site content or endorses them.</p>

<h2>Privacy Policy And Cookies Policy</h2>
<p>Site use is governed by the Privacy Policy, incorporated by reference into these terms.</p>

<h2>Availability Of The Website And Disclaimers</h2>
<p>Services are provided "as is" without warranties regarding defects, fitness for purpose, or information accuracy. Watch Market Limited has no obligation to update website information. The company disclaims responsibility for website security, errors, viruses, or malware. No liability exists for website disruption or unavailability. The company reserves rights to alter or discontinue any website portion.</p>

<h2>Limitation Of Liability</h2>
<p>The terms don't limit liability for death, personal injury from negligence, fraud, or fraudulent misrepresentation. For free services, the company has no liability for losses. The company isn't liable for losses from uncontrollable events.</p>
<p>Watch Market Limited accepts no liability for business losses, data corruption, or indirect or consequential damages.</p>

<h2>General</h2>
<p>Users cannot transfer rights under these terms. The company may modify terms, with revised versions applying upon publication. These terms and Privacy Policy constitute the complete agreement. Third parties cannot enforce provisions. Invalid provisions will be severed without affecting others. The agreement is governed by England and Wales law, with exclusive jurisdiction in English and Welsh courts.</p>

<h2>Formation Of Contract</h2>
<p>Listing items with Watch Market and confirming payment creates a binding sales offer. Sellers must describe items accurately. Items may be amended or withdrawn anytime. The Sale of Goods Act 1979 applies to business-to-business transactions; the Consumer Rights Act 2015 applies otherwise.</p>

<h2>Dispute Resolution</h2>
<p>Watch Market Limited provides a platform for transactions but holds no liability for disputes between users. The company doesn't hold or exchange funds&mdash;transactions occur directly between parties. The company accepts no responsibility for goods' condition or quality.</p>

<h2>WatchMarket Limited Details</h2>
<p>Watch Market Limited operates watchmarket.co.uk and holds VAT number 293229780. Contact: info@watchmarket.co.uk</p>
HTML;
    }

    private function privacyContent(): string
    {
        return <<<'HTML'
<h2>Watch Market Ltd Website Privacy Policy</h2>
<p>This policy explains how Watch Market Limited gathers and handles personal information through website usage, including data provided during registration. The site is not designed for children, and child data collection is not intentional.</p>

<h3>Controller</h3>
<p>Watch Market Limited (company number 11285078, England and Wales) manages personal data as the responsible party. Contact us at info@watchmarket.co.uk or the postal address in section 11 for privacy questions or rights requests.</p>

<h3>1. Types of Personal Data Collected</h3>
<p>The company collects various data categories:</p>
<ul>
<li><strong>Identity Data</strong>: Names, usernames, titles, birth dates, gender</li>
<li><strong>Contact Data</strong>: Addresses, emails, phone numbers</li>
<li><strong>Transaction Data</strong>: Payment details and purchase information</li>
<li><strong>Technical Data</strong>: IP addresses, browser details, device identifiers, location data</li>
<li><strong>Profile Data</strong>: Usernames, passwords, preferences, feedback</li>
<li><strong>Usage Data</strong>: Website interaction patterns</li>
<li><strong>Marketing and Communications Data</strong>: Marketing preferences</li>
</ul>
<p>Aggregated, anonymized data is also collected for statistical analysis and trend identification.</p>

<h3>2. Data Collection Methods</h3>
<p>Data collection occurs through:</p>
<ul>
<li><strong>Direct interactions</strong>: Form submissions, account creation, social media engagement, surveys, feedback</li>
<li><strong>Automated technologies</strong>: Cookies and tracking technologies (see cookie policy)</li>
<li><strong>Third parties</strong>: Watch Registry validation services, single sign-on providers (Google, Microsoft, Facebook, Apple), analytics services, payment processors</li>
<li><strong>Public sources</strong>: Companies House, Electoral Register</li>
</ul>
<p>Third-party tracking partners include Google Analytics, Instagram, and Facebook. The company uses personalized advertising across third-party platforms.</p>

<h3>3. Personal Data Usage</h3>
<p>The company uses personal data under these legal bases:</p>
<ul>
<li><strong>Contract performance</strong>: Fulfilling agreements with users</li>
<li><strong>Legitimate interests</strong>: Business operations, fraud prevention, customer security</li>
<li><strong>Legal obligations</strong>: Compliance requirements</li>
<li><strong>Consent</strong>: When actively agreed to by users</li>
</ul>

<h3>4. Direct Marketing</h3>
<p>During registration, preferences for marketing communications are indicated. The company analyzes data to identify relevant products and services.</p>
<p>"Express consent" is obtained before sharing personal data with third parties for their marketing. Users may opt out via links in communications or by contacting info@watchmarket.co.uk. Service-related messages continue even after opting out.</p>

<h3>5. Cookies</h3>
<p>See the cookie policy at watchmarket.co.uk/cookie-policy/ for details on cookie usage and preference management.</p>

<h3>6. Data Sharing</h3>
<p>Personal data may be shared with:</p>
<ul>
<li>External third parties (detailed elsewhere)</li>
<li>Prospective business buyers or merger partners</li>
<li>Law enforcement, courts, regulators, government authorities</li>
</ul>
<p>All third parties must maintain security standards and follow instructions. They cannot use personal data for their own purposes.</p>

<h3>7. International Transfers</h3>
<p>Data may transfer outside the UK to service providers. Protection is ensured through:</p>
<ul>
<li>International Data Transfer Agreements</li>
<li>International Data Transfer Addendum to EU standard contractual clauses</li>
</ul>
<p>Contact the company for copies of these safeguards.</p>

<h3>8. Data Security</h3>
<p>Appropriate security measures prevent unauthorized access, loss, or disclosure. Access is limited to employees and contractors with business need-to-know. Breach procedures are in place, with legal notification where required.</p>

<h3>9. Data Retention</h3>
<p>Personal data is retained only as long as necessary for collection purposes, including legal and tax requirements. Six years of basic customer information (Contact, Identity, Financial, Transaction) is kept for tax purposes after customer relationship ends.</p>
<p>Data deletion is possible in some circumstances. Personal data may be anonymized for research without further notice.</p>

<h3>10. Legal Rights</h3>
<p>Users have the right to:</p>
<ul>
<li><strong>Access</strong>: Request personal data copies via subject access request</li>
<li><strong>Correction</strong>: Correct incomplete or inaccurate information</li>
<li><strong>Erasure</strong>: Request deletion under certain conditions</li>
<li><strong>Object</strong>: Oppose processing based on legitimate interests; absolute right to object to direct marketing</li>
<li><strong>Transfer</strong>: Receive data in machine-readable format</li>
<li><strong>Restrict</strong>: Suspend processing for accuracy verification, unlawful use, legal claim needs, or pending objection verification</li>
</ul>
<p>Requests should be sent to info@watchmarket.co.uk. No fees apply, though unreasonable, repetitive, or excessive requests may incur charges. Responses typically arrive within one month.</p>

<h3>11. Contact Details</h3>
<p><strong>Email</strong>: info@watchmarket.co.uk</p>
<p><strong>Postal Address</strong>: Justa House, 204-208 Holbrook Lane, Coventry, England, CV6 4DD</p>

<h3>12. Complaints</h3>
<p>Users may lodge complaints with the Information Commissioner's Office (ico.org.uk). The company requests the opportunity to address concerns first.</p>

<h3>13. Policy Changes</h3>
<p>The policy is regularly reviewed. Users should notify the company of personal data changes during the business relationship.</p>

<h3>14. Third-Party Links</h3>
<p>The website may contain links to third-party sites, plugins, and applications. The company does not control these and is not responsible for their privacy statements. Users are encouraged to review third-party privacy policies.</p>
HTML;
    }
};
