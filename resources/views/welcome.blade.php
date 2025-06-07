<!DOCTYPE html>
<html lang="en" x-data="{ mobileMenuOpen: false, featuresTab: 'monitoring' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netumo Clone - Network Monitoring Made Simple</title>
    {{--<script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-gray-800">
<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="#" class="flex items-center">
            <span class="text-2xl font-bold text-blue-600">Netumo Clone</span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-8">
            <a href="#" class="text-blue-600 font-medium">Home</a>
            <a href="#" class="hover:text-blue-600">Features</a>
            <a href="#" class="hover:text-blue-600">Pricing</a>
            <a href="#" class="hover:text-blue-600">Blog</a>
            <a href="#" class="hover:text-blue-600">Contact</a>
        </nav>

        <!-- Auth Buttons -->
        <div class="hidden md:flex space-x-4">
            <a href="{{ route('login') }}" class="px-4 py-2 rounded hover:text-blue-600">Login</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Sign
                Up</a>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" class="md:hidden bg-white py-4 px-4 shadow-lg">
        <nav class="flex flex-col space-y-4">
            <a href="#" class="text-blue-600 font-medium">Home</a>
            <a href="#" class="hover:text-blue-600">Features</a>
            <a href="#" class="hover:text-blue-600">Pricing</a>
            <a href="#" class="hover:text-blue-600">Blog</a>
            <a href="#" class="hover:text-blue-600">Contact</a>
        </nav>
        <div class="mt-4 pt-4 border-t flex flex-col space-y-4">
            <a href="{{ route('login') }}" class="px-4 py-2 rounded hover:text-blue-600">Login</a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-center">Sign Up</a>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-50 to-indigo-50 py-20">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center md:space-x-12">
        <div class="md:w-1/2 mb-10 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">Network Monitoring Made Simple</h1>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">Host ID: {{ gethostname() }}</h1>
            <p class="text-xl text-gray-600 mb-8">Netumo Clone keeps an eye on your websites, servers and APIs. Get instant
                alerts when something goes wrong.</p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium">Get Startd</a>
                <a href="#"
                   class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-center font-medium">See How
                    It Works</a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Netumo Dashboard"
                 class="rounded-lg shadow-xl max-w-full h-96 transition-transform duration-300 hover:scale-105">
        </div>
    </div>
</section>

<!-- Trusted By Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl md:text-4xl text-center font-bold text-gray-800 mb-4">Trusted by companies worldwide</h2>
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Company Logo"
                 class="h-16 opacity-70 hover:opacity-100">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Company Logo"
                 class="h-16 opacity-70 hover:opacity-100">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Company Logo"
                 class="h-16 opacity-70 hover:opacity-100">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Company Logo"
                 class="h-16 opacity-70 hover:opacity-100">
            <img src="{{ asset('img/comp.jpeg') }}" alt="Company Logo"
                 class="h-16 opacity-70 hover:opacity-100">
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Powerful Features</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to monitor your infrastructure and
                stay informed</p>
        </div>

        <!-- Feature Tabs -->
        <div class="mb-12">
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button @click="featuresTab = 'monitoring'"
                        :class="{ 'bg-blue-600 text-white': featuresTab === 'monitoring', 'bg-white text-gray-700': featuresTab !== 'monitoring' }"
                        class="px-6 py-3 rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    Monitoring
                </button>
                <button @click="featuresTab = 'alerting'"
                        :class="{ 'bg-blue-600 text-white': featuresTab === 'alerting', 'bg-white text-gray-700': featuresTab !== 'alerting' }"
                        class="px-6 py-3 rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    Alerting
                </button>
                <button @click="featuresTab = 'reports'"
                        :class="{ 'bg-blue-600 text-white': featuresTab === 'reports', 'bg-white text-gray-700': featuresTab !== 'reports' }"
                        class="px-6 py-3 rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    Reports
                </button>
                <button @click="featuresTab = 'integrations'"
                        :class="{ 'bg-blue-600 text-white': featuresTab === 'integrations', 'bg-white text-gray-700': featuresTab !== 'integrations' }"
                        class="px-6 py-3 rounded-lg font-medium shadow-sm hover:shadow-md transition">
                    Integrations
                </button>
            </div>

            <!-- Feature Content -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <div x-show="featuresTab === 'monitoring'" class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Comprehensive Monitoring</h3>
                        <p class="text-gray-600 mb-6">Monitor websites, servers, APIs, and more with our comprehensive
                            monitoring solutions.</p>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>HTTP/HTTPS monitoring</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Ping monitoring</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>SSL certificate monitoring</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Port monitoring</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ asset('img/comp.jpeg') }}"
                             alt="Monitoring Dashboard" class="rounded-lg">
                    </div>
                </div>

                <div x-show="featuresTab === 'alerting'" class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Instant Alerting</h3>
                        <p class="text-gray-600 mb-6">Get notified immediately when something goes wrong with your
                            services.</p>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Email, SMS, and push notifications</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Escalation policies</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Custom alert thresholds</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Maintenance windows</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ asset('img/comp.jpeg') }}"
                             class="rounded-lg" alt="">
                    </div>
                </div>

                <div x-show="featuresTab === 'reports'" class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Detailed Reports</h3>
                        <p class="text-gray-600 mb-6">Get insights into your system's performance with comprehensive
                            reports.</p>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Uptime reports</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Response time reports</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Custom report scheduling</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Export to PDF/CSV</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ asset('img/comp.jpeg') }}"
                             class="rounded-lg" alt="">
                    </div>
                </div>

                <div x-show="featuresTab === 'integrations'" class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-2xl font-bold mb-4">Powerful Integrations</h3>
                        <p class="text-gray-600 mb-6">Connect Netumo with your favorite tools and services.</p>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Slack notifications</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Microsoft Teams</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>Webhooks</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                                <span>API access</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex justify-center">
                        <img src="{{ asset('img/comp.jpeg') }}"
                             class="rounded-lg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">What Our Customers Say</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Don't just take our word for it - hear from some of our
                happy customers</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <div class="text-yellow-400 mr-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">"Netumo has saved us countless hours of downtime. Their alerting system is
                    lightning fast and their dashboard is incredibly intuitive."</p>
                <div class="flex items-center">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Customer"
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">John Smith</h4>
                        <p class="text-gray-500 text-sm">CTO at TechCorp</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <div class="text-yellow-400 mr-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">"The peace of mind that comes with knowing Netumo is watching our servers
                    is priceless. Their support team is also fantastic."</p>
                <div class="flex items-center">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Customer"
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Sarah Johnson</h4>
                        <p class="text-gray-500 text-sm">DevOps Lead at WebSolutions</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-gray-50 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <div class="text-yellow-400 mr-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
                <p class="text-gray-600 mb-6">"We switched to Netumo from another monitoring service and haven't looked
                    back. The feature set is perfect for our needs."</p>
                <div class="flex items-center">
                    <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Customer"
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Michael Chen</h4>
                        <p class="text-gray-500 text-sm">Founder at StartupX</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Get Started?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of businesses who trust Netumo to monitor their
            critical infrastructure.</p>
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('login') }}" class="px-6 py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-medium">Get Started</a>
            <a href="#" class="px-6 py-3 border border-white rounded-lg hover:bg-blue-700 font-medium">Contact Sales</a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
            <div class="col-span-2">
                <h3 class="text-2xl font-bold text-white mb-4">netumo clone</h3>
                <p class="mb-4">Network monitoring made simple. Keep an eye on your websites, servers and APIs.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">Product</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white">Features</a></li>
                    <li><a href="#" class="hover:text-white">Pricing</a></li>
                    <li><a href="#" class="hover:text-white">Integrations</a></li>
                    <li><a href="#" class="hover:text-white">API</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">Resources</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white">Documentation</a></li>
                    <li><a href="#" class="hover:text-white">Guides</a></li>
                    <li><a href="#" class="hover:text-white">Blog</a></li>
                    <li><a href="#" class="hover:text-white">Support</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">Company</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white">About Us</a></li>
                    <li><a href="#" class="hover:text-white">Careers</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                    <li><a href="#" class="hover:text-white">Legal</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
            <p class="mb-4 md:mb-0">© 2023 Netumo. All rights reserved.</p>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-white">Terms of Service</a>
                <a href="#" class="hover:text-white">Cookies</a>
            </div>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
