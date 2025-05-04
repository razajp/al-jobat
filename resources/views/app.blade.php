<!DOCTYPE html>
<html lang="en" data-theme="{{ Auth::check() ? Auth::user()->theme : (isset($_COOKIE['theme']) ? $_COOKIE['theme'] : (request()->cookie('theme') ?? (strpos($_SERVER['HTTP_USER_AGENT'], 'Dark') !== false ? 'dark' : 'light'))) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Al Jobat`s Garments Busniess Management Solution!">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', app('company')->name)</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url('/calibri.ttf') format('truetype'); /* For TTF */
            font-weight: normal;
            font-style: normal;
        }

        /* color theme */
        :root {
            --bg-color: #111827;
            /* Default dark theme background */
            --h-bg-color: #374151;
            --secondary-bg-color: #1f2937;
            --h-secondary-bg-color: hsl(215, 28%, 13%);
            /* Default dark theme secondary background */
            --text-color: #ffffff;
            /* Default dark theme text color */
            --secondary-text: #d1d5db;
            /* Default dark theme secondary text */
            --primary-color: #2563eb;
            --h-primary-color: #1f56cd;
            /* Default dark theme primary color */
            --bg-warning: hsl(45, 50%, 30%);
            --bg-success: hsl(130, 50%, 30%);
            --bg-error: hsl(360, 50%, 30%);
            --border-warning: hsl(45, 100%, 45%);
            --border-success: hsl(130, 100%, 45%);
            --border-error: hsl(360, 100%, 45%);
            --text-warning: hsl(45, 30%, 95%);
            --text-success: hsl(130, 30%, 95%);
            --text-error: hsl(360, 30%, 95%);

            --h-bg-warning: hsl(45, 50%, 20%);
            --h-bg-success: hsl(130, 50%, 20%);
            --h-bg-error: hsl(360, 50%, 20%);

            --danger-color: hsl(0, 65%, 51%);
            --h-danger-color: hsl(0, 65%, 41%);
            --success-color: hsl(142, 65%, 36%);
            --h-success-color: hsl(142, 65%, 26%);

            --overlay-color: rgba(0, 0, 0, 0.438);
            --glass-border-color: #ffffff;
        }

        [data-theme='light'] {
            --bg-color: #eef0f2;
            --h-bg-color: #d1d3d7;
            --secondary-bg-color: #ffffff;
            --h-secondary-bg-color: hsl(0, 0%, 96%);
            --text-color: #1f2937;
            --secondary-text: #4b5563;
            --bg-warning: hsl(45, 100%, 87%);
            --bg-success: hsl(130, 100%, 87%);
            --bg-error: hsl(360, 100%, 87%);
            --h-bg-warning: hsl(45, 100%, 80%);
            --h-bg-success: hsl(130, 100%, 80%);
            --h-bg-error: hsl(360, 100%, 80%);
            --border-warning: hsl(45, 100%, 45%);
            --border-success: hsl(130, 100%, 45%);
            --border-error: hsl(360, 100%, 45%);
            --text-warning: hsl(45, 75%, 40%);
            --text-success: hsl(130, 75%, 40%);
            --text-error: hsl(360, 75%, 40%);
            --glass-border-color: #000000;
        }

        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .bg-\[var\(--primary-color\)\]{
            color: #e2e8f0 !important;
        }
        
        .my-scrollbar-2::-webkit-scrollbar {
            display: none;
        }
        
        .fade-in {
            animation: fadeIn 0.35s ease-in-out;
        }

        /* Example animation */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .fade-out {
            animation: fadeOut 0.35s forwards !important;
        }

        .opacity-zero {
            opacity: 0;
        }

        .opacity-transition {
            transition: opacity .2s linear;
        }

        .card {
            transition: all 0.3s ease-in-out;
            position: relative;
        }

        .card:hover {
            transform: translateY(-0.3rem);
            background-color: var(--h-secondary-bg-color);
            box-shadow: 0 5px 0.8rem var(--bg-color);
        }

        .card button {
            transition: all 0.2s ease-in-out;
        }

        .card:hover button {
            scale: 1.1;
        }

        .active_inactive_dot {
            opacity: 100;
            transition: all 0.2s ease-in-out;
        }

        .active_inactive {
            opacity: 0;
            transition: all 0.2s ease-in-out;
        }

        .card:hover .active_inactive {
            opacity: 100;
        }

        .card:hover .active_inactive_dot {
            opacity: 0;
        }

        .nav-link.active {
            background-color: var(--h-bg-color) !important;
        }

        .nav-link.active i {
            color: var(--primary-color) !important;
        }

        .nav-link.active:hover i {
            color: var(--h-primary-color) !important;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            /* For Firefox */
        }

        input[disabled] {
            cursor: not-allowed;
        }

        select[disabled] {
            cursor: not-allowed;
        }

        input::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
        }

        strong {
            font-weight: 600 !important;
        }

        span {
            color: var(--secondary-text) !important;
        }

        .open-dropdown:hover .open-dropdown-hover\:block {
            display: block;
        }
    </style>
    
    @vite('resources/css/app.css')
    
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
            .then(reg => console.log('Service Worker registered ✔️', reg))
            .catch(err => console.warn('Service Worker registration failed ❌', err));
        }
    </script>
    <script src="{{ asset('jquery.js') }}"></script>
</head>

<body class="bg-[var(--secondary-bg-color)] text-[var(--text-color)] text-sm min-h-screen flex flex-col md:flex-row items-center justify-center fade-in" cz-shortcut-listen="true">
    {{-- side bar --}}
    @if (Auth::check())
        <script>
            const url = window.location.href; // Get the current URL
        </script>

        @component('components.sidebar')
        @endcomponent
    @endif

    <!-- Loader -->
    <div id="page-loader" class="fixed inset-0 z-[999] bg-[var(--overlay-color)] bg-opacity-80 flex items-center justify-center hidden">
        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div class="wrapper flex-1 flex flex-col md:h-screen relative w-full overflow-y-hidden">
        {{-- main content --}}
        <main class="flex-1 px-8 py-0 md:p-8 overflow-y-auto my-scrollbar-2 flex items-center justify-center bg-[var(--bg-color)] rounded-3xl mx-2.5 md:mr-2.5 {{ request()->is('login') ? 'mt-2.5 md:ml-2.5' : 'mt-0 md:ml-0' }} md:mt-2.5 relative">
            {{-- alert --}}
            <div id="messageBox" class="absolute top-3 mx-auto flex items-center flex-col space-y-3 z-[100] text-sm w-full select-none pointer-events-none">
                @if (session('info'))
                    <x-alert type="info" :messages="session('info')" />
                @endif
            
                @if (session('success'))
                    <x-alert type="success" :messages="session('success')" />
                @endif
            
                @if (session('warning'))
                    <x-alert type="warning" :messages="session('warning')" />
                @endif
            
                @if (session('error'))
                    <x-alert type="error" :messages="session('error')" />
                @endif
            </div>
            <!-- Notification Box -->
            <div id="notificationBox" class="absolute top-3 right-3 flex flex-col space-y-3 z-[100] text-sm mx-auto items-end w-full select-none">
                {{-- <x-notification
                    title="Payment Method Expiring"
                    message="Your card ending in 1122 is expiring soon. Please update your billing info."
                    actionLabel="Update Card"
                    actionUrl="/billing"
                />
                <x-notification
                    title="Payment Method Expiring"
                    message="Your card ending in 1122 is expiring soon. Please update your billing info."
                /> --}}
            </div>

            <div class="main-child grow">
                @yield('content')
            </div>
        </main>

        {{-- footer --}}
        @component('components.footer')
        @endcomponent
    </div>

    <script>
        function checkMax(input) {
            input.value = input.value.replace(/\D/g, '');

            let errorElem = document.getElementById(input.id+"-error");
            
            const max = parseInt(input.max, 10);
            if (parseInt(input.value, 10) > max) {
                errorElem.textContent = `Value cannot exceed ${max}.`;
                if (errorElem.classList.contains("hidden")) {
                    errorElem.classList.remove("hidden");
                }

                input.value = max;
            } else {
                errorElem.textContent = ``;
                if (!errorElem.classList.contains("hidden")) {
                    errorElem.classList.add("hidden");
                }
            }
        }

        // Message box animation
        function messageBoxAnimation() {
            setTimeout(function() {
                // Select all alert messages by their common class
                const messages = document.querySelectorAll('.alert-message');

                messages.forEach((message) => {
                    if (message) {
                        message.classList.add('fade-out');
                        message.addEventListener('animationend', () => {
                            message.style.display = 'none'; // Hide the element after animation
                        });
                    }
                });
            }, 5000); // Trigger fade-out after 5 seconds
        }
        messageBoxAnimation();

        // notification box animation
        function hideNotification(notificationElem) {
            notificationElem.classList.add('fade-out');

            notificationElem.addEventListener('animationend', () => {
                notificationElem.style.display = 'none';
                notificationElem.remove();
            });
        }
        
        let dropdownMenus, dropdownTriggers;

        // drop down toggle
        function setDropdownListeners(params) {
            dropdownTriggers = document.querySelectorAll('.dropdown-trigger');
            dropdownMenus = document.querySelectorAll('.dropdownMenu');
        
            dropdownTriggers.forEach((trigger, index) => {
                const dropdownMenu = dropdownMenus[index];
        
                trigger.onclick = (e) => {
                    e.stopPropagation();
        
                    if (dropdownMenu.classList.contains('hidden')) {
                        closeAllDropdowns();
        
                        dropdownMenu.classList.remove('hidden');
                        setTimeout(() => {
                            dropdownMenu.classList.add('opacity-100', 'scale-100');
                            dropdownMenu.classList.remove('opacity-0', 'scale-95');
                        }, 10);
                    } else {
                        dropdownMenu.classList.remove('opacity-100', 'scale-100');
                        dropdownMenu.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => {
                            dropdownMenu.classList.add('hidden');
                        }, 300);
                    }
                }
            });
        }
        setDropdownListeners();

        function formatDate(date) {
            const inputDate = new Date(date);

            const day = inputDate.getDate().toString().padStart(2, '0');
            const month = inputDate.toLocaleString('en-US', { month: 'short' });
            const year = inputDate.getFullYear();
            const weekday = inputDate.toLocaleString('en-US', { weekday: 'short' });

            const formatted = `${day}-${month}-${year} ${weekday}`;
            return formatted;
        }

        function closeAllDropdowns() {
            dropdownMenus.forEach(menu => {
                menu.classList.add('hidden');
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95');
            });
        }

        document.addEventListener('focus', function(event) {
            if (event.target.matches('input[type="date"]')) {
                event.target.showPicker(); // Trigger the date picker
            } else if (event.target.matches('input[type="month"]')) {
                event.target.showPicker(); // Trigger the date picker
            }
        }, true); // Use capturing phase

        const previewImage = (event) => {
            console.log(event.target.files[0]);
            
            const file = event.target.files[0];
            const placeholderIcon = document.querySelector(".placeholder_icon");
            const uploadText = document.querySelector(".upload_text");

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    placeholderIcon.src = e.target.result;
                    placeholderIcon.classList.add("rounded-md", "w-full", "h-auto");
                    uploadText.textContent = "Preview";
                }
                reader.readAsDataURL(file);
            }
        }

        function formatNumbersDigitLess(number) {
            return new Intl.NumberFormat('en-US').format(number);
        }

        function formatNumbersWithDigits(number, maxFraction, minFraction) {
            return new Intl.NumberFormat('en-US', { maximumFractionsDigits:maxFraction, minimumFractionDigits:minFraction}).format(number);
        }

        document.addEventListener("contextmenu", e => e.preventDefault());

        @if(!request()->is('orders/create') && !request()->is('shipments/create') && !request()->is('cargos/create'))
            // Search Script
            let filterType;
            let cardsDataArray = [];

            let cardsArray = $('.search_container').children().toArray();

            function setCardsData() {
                cardsArray = $('.search_container').children().toArray();

                cardsArray.forEach((card) => {
                    cardsDataArray.push(JSON.parse(card.dataset.json));
                })
            }
            setCardsData();

            function setFilter(filterTypeArg) {
                filterType = filterTypeArg;

                searchData(document.getElementById('search_box').value);
            }

            function searchData(search) {
                search = search.toLowerCase();

                let filteredData = filterData(search);

                const cardContainerDom = document.querySelector('.search_container');
                cardContainerDom.innerHTML = "";

                if (filteredData.length === 0) {
                    const noResultMessage = "<p class='text-center col-span-full text-[var(--border-error)]'>No data found</p>"
                    cardContainerDom.innerHTML = noResultMessage;
                } else {
                    filteredData.forEach(item => {
                        const cardElement = cardsArray.find(card => card.id == item.id);
                        if (cardElement) {
                            cardContainerDom.appendChild(cardElement);
                        }
                    });
                }
            }
        @endif

        // Function to send AJAX request to update last_activity
        function updateLastActivity() {
            $.ajax({
                url: '/update-last-activity',
                type: 'POST',
                data: {}, // Optional if you want to send any data, can be left empty
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'updated') {
                        console.log("Last activity updated successfully.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update last activity", error);
                }
            });
        }

        // Call the function immediately once
        updateLastActivity();

        // Then every 60 minutes (3600000 milliseconds)
        setInterval(updateLastActivity, 60 * 60 * 1000);
    </script>

    <script>
        var pusher = new Pusher('c99f4e2f9df04cc306f4', {
            cluster: 'ap2',
            forceTLS: true
        });

        var channel = pusher.subscribe('notifications');

        // Utility function to create and show notification
        function showNotification(title = '', message = '') {
            const notificationBox = document.getElementById("notificationBox");
            if (!notificationBox) return;

            const wrapper = document.createElement("div");
            wrapper.innerHTML = `
                <x-notification
                    title="${title}"
                    message="${message}"
                />
            `;
            const notificationElement = wrapper.firstElementChild;
            notificationBox.prepend(notificationElement);

            setTimeout(() => hideNotification(notificationElement), 5000);
        }

        // Listen to the event
        channel.bind('App\\Events\\NewNotificationEvent', function (data) {
            console.log('📢 Notification received:', data);

            const dataObject = data.data;

            @if(request()->is('orders/create'))
                if (dataObject.title === "New Article Added.") {
                    const dateInput = document.querySelector("#date");

                    if (dateInput?.value) {
                        getDataByDate(dateInput);
                        showNotification(dataObject.title, dataObject.message);
                    }
                }
            @endif

            @if(!request()->is('login'))
                if (dataObject.title == "User Inactivated" && dataObject.id == {{Auth::user()->id}}) {
                    document.getElementById("logoutForm").submit();
                }
            @endif
        });

        pusher.connection.bind('connected', function() {
            console.log('✅ Pusher connected');
        });
    </script>
</body>
<script>
    window.addEventListener('beforeunload', function () {
        showLoader();
    });

    function showLoader() {
        document.getElementById('page-loader').classList.remove('hidden');
        document.getElementById('page-loader').classList.remove('fade-out');
        document.getElementById('page-loader').classList.add('fade-in');
    }

    function hideLoader() {
        document.getElementById('page-loader').classList.add('hidden');
        document.getElementById('page-loader').classList.add('fade-out');
        document.getElementById('page-loader').classList.remove('fade-in');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Loader for normal <a> clicks
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                const target = this.getAttribute('target');

                if (
                    href &&
                    !href.startsWith('#') &&
                    !href.startsWith('javascript:') &&
                    !target
                ) {
                    showLoader();
                }
            });
        });

        // Loader for all form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                showLoader();
            });
        });
    });

    // Hide loader on full page load (in case of back/refresh)
    window.addEventListener('load', function () {
        hideLoader();
    });

    // ======================
    // For AJAX Requests
    // ======================

    // If using Axios
    if (typeof axios !== 'undefined') {
        axios.interceptors.request.use(config => {
            showLoader();
            return config;
        }, error => {
            hideLoader();
            return Promise.reject(error);
        });

        axios.interceptors.response.use(response => {
            hideLoader();
            return response;
        }, error => {
            hideLoader();
            return Promise.reject(error);
        });
    }

    // If using jQuery
    if (typeof $ !== 'undefined') {
        $(document).ajaxStart(function () {
            showLoader();
        }).ajaxStop(function () {
            hideLoader();
        });
    }

    // its for cache clear
    // // Unregister service worker and clear caches
    // if ('serviceWorker' in navigator) {
    // navigator.serviceWorker.getRegistrations().then(registrations => {
    //     for (let registration of registrations) {
    //     registration.unregister();
    //     }
    // });

    // caches.keys().then(function(names) {
    //     for (let name of names) {
    //     caches.delete(name);
    //     }
    // }).then(() => {
    //     console.log('Service Worker and Cache cleared!');
    //     window.location.reload(); // Optional: reload the page
    // });
    // }
</script>

</html>
