@extends('front.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">Terms of Service</h1>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h2 class="h4">Agreement to Terms</h2>
                        <p>By accessing and using Motors ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Use License</h2>
                        <p>Permission is granted to temporarily access the materials (information or software) on Motors' website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                        <ul>
                            <li>Modify or copy the materials</li>
                            <li>Use the materials for any commercial purpose or for any public display</li>
                            <li>Attempt to decompile or reverse engineer any software contained on the website</li>
                            <li>Remove any copyright or other proprietary notations from the materials</li>
                        </ul>
                        <p>This license shall automatically terminate if you violate any of these restrictions and may be terminated by Motors at any time.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">User Accounts</h2>
                        <h3 class="h5">Account Creation</h3>
                        <p>To use certain features of our Service, you must register for an account. When you register, you agree to provide accurate, current, and complete information.</p>

                        <h3 class="h5">Account Security</h3>
                        <p>You are responsible for:</p>
                        <ul>
                            <li>Safeguarding your account password</li>
                            <li>All activities that occur under your account</li>
                            <li>Notifying us immediately of any unauthorized use</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Content and Conduct</h2>
                        <h3 class="h5">User Content</h3>
                        <p>By posting content on our platform, you grant us a non-exclusive, royalty-free, perpetual, and worldwide license to use, display, and distribute your content.</p>

                        <h3 class="h5">Prohibited Conduct</h3>
                        <p>You agree not to:</p>
                        <ul>
                            <li>Violate any applicable laws or regulations</li>
                            <li>Infringe on intellectual property rights</li>
                            <li>Post harmful, offensive, or inappropriate content</li>
                            <li>Harass, threaten, or intimidate other users</li>
                            <li>Attempt to gain unauthorized access to our systems</li>
                            <li>Use the service for any fraudulent or illegal purposes</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Vehicle Listings and Transactions</h2>
                        <h3 class="h5">Listing Accuracy</h3>
                        <p>When listing vehicles, you agree to provide accurate and truthful information about the vehicle, including its condition, history, and specifications.</p>

                        <h3 class="h5">Transaction Responsibility</h3>
                        <p>Motors facilitates connections between buyers and sellers but is not responsible for:</p>
                        <ul>
                            <li>The accuracy of listings</li>
                            <li>The completion of transactions</li>
                            <li>The quality or condition of vehicles</li>
                            <li>Any disputes between users</li>
                        </ul>
                        <p>All transactions are conducted at your own risk.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Privacy and Data</h2>
                        <p>Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service, to understand our practices.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Disclaimer</h2>
                        <p>The information on this website is provided on an 'as is' basis. To the fullest extent permitted by law, Motors:</p>
                        <ul>
                            <li>Excludes all representations and warranties relating to this website and its contents</li>
                            <li>Excludes all liability for damages arising out of or in connection with your use of this website</li>
                            <li>Does not guarantee the accuracy, completeness, or timeliness of information</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Limitations</h2>
                        <p>In no event shall Motors or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Motors' website.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Revisions</h2>
                        <p>The materials appearing on Motors' website could include technical, typographical, or photographic errors. Motors does not warrant that any of the materials on its website are accurate, complete, or current. Motors may make changes to the materials contained on its website at any time without notice.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Governing Law</h2>
                        <p>These terms and conditions are governed by and construed in accordance with the laws of [Your Country/State], and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">Contact Information</h2>
                        <p>If you have any questions about these Terms of Service, please contact us at:</p>
                        <ul>
                            <li>Email: legal@motors.azsystems.tech</li>
                            <li>Phone: [Your Phone Number]</li>
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