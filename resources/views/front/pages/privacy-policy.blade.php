@extends('front.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">Privacy Policy</h1>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h4">Introduction</h2>
                        <p>This Privacy Policy describes how Motor Soog ("Motor Soog", "we", "us", or "our") collects, uses, and protects your personal information when you use our website and services. Motor Soog operates the online marketplace available at <a href="https://motors.azsystems.tech">https://motors.azsystems.tech</a> (the "Site"). This policy applies to information collected through the Site and related services. This policy specifically applies to the application and website branded as Motor Soog and hosted at https://motors.azsystems.tech.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Information We Collect</h2>
                        <h3 class="h5">Personal Information</h3>
                        <p>We may collect the following personal information when you use Motor Soog (the Site):</p>
                        <ul>
                            <li>Name and contact information</li>
                            <li>Email address</li>
                            <li>Phone number</li>
                            <li>Profile information</li>
                            <li>Google account information (when using Google login)</li>
                        </ul>

                        <h3 class="h5">Usage Information</h3>
                        <p>We automatically collect certain information about your device and usage:</p>
                        <ul>
                            <li>IP address</li>
                            <li>Browser type and version</li>
                            <li>Pages visited and time spent</li>
                            <li>Device information</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">How We Use Your Information</h2>
                        <p>We use the collected information for the operation of Motor Soog and to provide, maintain, and improve the Site and our services. This includes:</p>
                        <ul>
                            <li>Creating and managing user accounts on <a href="https://motorssooq.com">motorssooq.com</a></li>
                            <li>Authenticating users and enabling Google login where requested</li>
                            <li>Communicating about listings, transactions, and support</li>
                            <li>Improving the Site and providing personalized content</li>
                            <li>Ensuring security and preventing abuse and fraud</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">OAuth and Scopes</h2>
                        <p>When you sign in using Google, we only request the minimum scopes necessary to authenticate your account and obtain basic profile information. The scopes we request are:</p>
                        <ul>
                            <li>openid</li>
                            <li>profile</li>
                            <li>email</li>
                        </ul>
                        <p>We use these to authenticate your account, read your basic profile (name and profile picture), and your email address to create or link your account on Motor Soog.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Information Sharing</h2>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties except:</p>
                        <ul>
                            <li>With your consent</li>
                            <li>To comply with legal obligations</li>
                            <li>To protect our rights and safety</li>
                            <li>With service providers who assist our operations (under strict confidentiality agreements)</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Data Security</h2>
                        <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Your Rights</h2>
                        <p>You have the right to:</p>
                        <ul>
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Request deletion of your information</li>
                            <li>Object to processing of your information</li>
                            <li>Data portability</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Cookies</h2>
                        <p>We use cookies and similar technologies to enhance your experience, analyze usage, and provide personalized content. You can control cookie settings through your browser.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Third-Party Services</h2>
                        <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Children's Privacy</h2>
                        <p>Our services are not intended for children under 13. We do not knowingly collect personal information from children under 13.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Changes to This Policy</h2>
                        <p>We may update this Privacy Policy from time to time. We will notify you of any significant changes by posting the new policy on this page and updating the "Last Updated" date.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Contact Us</h2>
                        <p>If you have any questions about this Privacy Policy, please contact Motor Soog at:</p>
                        <ul>
                            <li>Website / Homepage: <a href="https://motors.azsystems.tech">https://motors.azsystems.tech</a></li>
                            <li>Email: privacy@motors.azsystems.tech</li>
                            <li>Phone: [Your Phone Number]</li>
                            <li>Address: [Organization Address if available]</li>
                        </ul>
                    </div>

                    <div class="text-muted">
                        <small>Last Updated: {{ date('F j, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection