<!-- Footer Component Start -->
<footer class="w-full bg-[var(--secondary-bg-color)] px-6 py-4 md:py-2 shadow-lg z-30 text-sm fade-in">
    <div class="container mx-auto flex justify-between items-center">
        @if (request()->is('users/create') || request()->is('suppliers/create') || request()->is('employees/create') || request()->is('articles/create') || request()->is('rates/create') || request()->is('productions/create') || request()->is('articles/*/edit') || request()->is('customers/*/edit') || request()->is('suppliers/*/edit') || request()->is('customers/create') || request()->is('orders/create') || request()->is('shipments/create') || request()->is('invoices/create') || request()->is('vouchers/create') || request()->is('cargos/create') || request()->is('reports/statement'))
            <button id="prevBtn" class="bg-[var(--h-bg-color)] text-[var(--text-color)] px-4 md:px-5 py-2 md:py-1 rounded-lg hover:scale-95 transition-all duration-300 ease-in-out flex items-center disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer" disabled>
                <i class='fas fa-angles-left mr-1'></i> <div class="bg-[var(--h-bg-color)] hidden md:block">Previous</div>
            </button>
        @endif
        <div class="flex justify-between items-center mx-auto px-8 py-3">
            <div class="md:flex hidden justify-between items-center mx-auto">
                <span class="text-center text-sm mx-3">Copyright  &copy; 2024-<span class="opacity-100" id="year">2025</span> Spark Pair All rights reserved.</span>
                <div class="flex justify-center mx-3 ">
                    <a href="https://wa.me/+923165825495" target="_blank" class="text-[var(--primary-color)] hover:underline">+92 316 5825495</a>
                    <span class="mx-2">|</span>
                    <a href="https://sparkpair.vercel.app" target="_blank" class="text-[var(--primary-color)] hover:underline">sparkpair.vercel.app</a>
                </div>
            </div>
            <div class="md:hidden flex justify-between items-center mx-auto">
                <span class="text-center text-xs mx-3">Copyright  &copy; <span class="opacity-100" id="year">2025</span> Spark Pair</span>
            </div>
            @if (request()->is('login'))
                <div class="flex justify-center mx-5 fixed right-0">
                    <button id="themeToggle" onclick="changeTheme()" class="text-sm text-[var(--secondary-text)] hover:text-[var(--primary-color)] cursor-pointer">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @if (request()->is('invoices/create') || request()->is('cargos/create'))
                <button id="printAndSaveBtn" class="bg-[var(--success-color)] text-[#e2e8f0] px-4 md:px-5 py-2 md:py-1 rounded-lg hover:bg-[var(--h-success-color)] hover:scale-95 transition-all duration-300 ease-in-out flex items-center gap-1 hidden cursor-pointer">
                    <i class='fas fa-save'></i> <div class="text-[#e2e8f0] hidden md:block">Print & Save</div>
                </button>
            @endif
            @if (request()->is('orders/create'))
                <button id="quickInvoiceBtn" class="bg-[var(--success-color)] text-[#e2e8f0] px-4 md:px-5 py-2 md:py-1 rounded-lg hover:bg-[var(--h-success-color)] hover:scale-95 transition-all duration-300 ease-in-out flex items-center gap-1 hidden cursor-pointer">
                    <i class='fas fa-receipt'></i> <div class="text-[#e2e8f0] hidden md:block">Quick Invoice</div>
                </button>
            @endif
            @if (request()->is('users/create') || request()->is('suppliers/create') || request()->is('employees/create') || request()->is('articles/create') || request()->is('rates/create') || request()->is('productions/create') || request()->is('articles/*/edit') || request()->is('customers/*/edit') || request()->is('suppliers/*/edit') || request()->is('customers/create') || request()->is('orders/create') || request()->is('shipments/create') || request()->is('invoices/create') || request()->is('vouchers/create') || request()->is('cargos/create'))
                <button id="saveBtn" class="bg-[var(--success-color)] text-[#e2e8f0] px-4 md:px-5 py-2 md:py-1 rounded-lg hover:bg-[var(--h-success-color)] hover:scale-95 transition-all duration-300 ease-in-out flex items-center gap-1 hidden cursor-pointer">
                    <i class='fas fa-save'></i> <div class="text-[#e2e8f0] hidden md:block">Save</div>
                </button>
            @endif
            @if (request()->is('users/create') || request()->is('suppliers/create') || request()->is('employees/create') || request()->is('articles/create') || request()->is('rates/create') || request()->is('productions/create') || request()->is('articles/*/edit') || request()->is('customers/*/edit') || request()->is('suppliers/*/edit') || request()->is('customers/create') || request()->is('orders/create') || request()->is('shipments/create') || request()->is('invoices/create') || request()->is('vouchers/create') || request()->is('cargos/create') || request()->is('reports/statement'))
                <button id="nextBtn" class="bg-[var(--primary-color)] text-[var(--text-color)] px-4 md:px-5 py-2 md:py-1 rounded-lg hover:bg-[var(--h-primary-color)] hover:scale-95 transition-all duration-300 ease-in-out flex items-center gap-1 cursor-pointer">
                    <div class="text-[#e2e8f0] hidden md:block">Next</div> <i class='fas fa-angles-right'></i>
                </button>
            @endif
        </div>
    </div>
    @if (request()->is('users/create') || request()->is('suppliers/create') || request()->is('employees/create') || request()->is('articles/create') || request()->is('rates/create') || request()->is('productions/create') || request()->is('articles/*/edit') || request()->is('customers/*/edit') || request()->is('suppliers/*/edit') || request()->is('customers/create') || request()->is('orders/create') || request()->is('shipments/create') || request()->is('invoices/create') || request()->is('vouchers/create') || request()->is('cargos/create') || request()->is('reports/statement'))
        <script>
            document.getElementById('year').textContent = new Date().getFullYear();

            let currentStep = 1;
            let noOfSteps = document.querySelector(".progress-indicators").children.length;

            function nextStep(step) {
                validateForNextStep()
                if (!validateForNextStep()) {
                    return isValid; // Return final validation status
                }

                let step1Doms = document.querySelectorAll(`.step${currentStep}`);
                let step2Doms = document.querySelectorAll(`.step${step + 1}`);

                if (currentStep === noOfSteps) {
                    return;
                }

                if (step1Doms) {
                    step1Doms.forEach((dom) => dom.classList.add('hidden'));
                }
                if (step2Doms) {
                    step2Doms.forEach((dom) => dom.classList.remove('hidden'));
                }

                document.getElementById(`step${step + 1}-indicator`).classList.remove('bg-[var(--h-bg-color)]');
                document.getElementById(`step${step + 1}-indicator`).classList.remove('hover:bg-[var(--secondary-bg-color)]');
                document.getElementById(`step${step + 1}-indicator`).classList.add('bg-[var(--primary-color)]');
                document.getElementById(`step${step + 1}-indicator`).classList.add('hover:bg-[var(--h-primary-color)]');
                if (currentStep <= step) {
                    document.getElementById(`step${currentStep}-indicator`).classList.remove('bg-[var(--primary-color)]');
                    document.getElementById(`step${currentStep}-indicator`).classList.remove('hover:bg-[var(--h-primary-color)]');
                    document.getElementById(`step${currentStep}-indicator`).classList.add('bg-[var(--h-bg-color)]');
                    document.getElementById(`step${currentStep}-indicator`).classList.add('hover:bg-[var(--secondary-bg-color)]');
                }
                document.getElementById('progress-bar').style.width = `${(step + 1) * (100/noOfSteps)}%`;

                currentStep = step + 1;
                updateButtons();
            }

            function prevStep(step) {
                let step1Doms = document.querySelectorAll(`.step${step - 1}`);
                let step2Doms = document.querySelectorAll(`.step${currentStep}`);

                if (step <= 1) {
                    return;
                }

                if (step1Doms) {
                    step1Doms.forEach((dom) => dom.classList.remove('hidden'));
                }
                if (step2Doms) {
                    step2Doms.forEach((dom) => dom.classList.add('hidden'));
                }

                document.getElementById(`step${step - 1}-indicator`).classList.add('bg-[var(--primary-color)]');
                document.getElementById(`step${step - 1}-indicator`).classList.add('hover:bg-[var(--h-primary-color)]');
                document.getElementById(`step${step - 1}-indicator`).classList.remove('bg-[var(--h-bg-color)]');
                document.getElementById(`step${step - 1}-indicator`).classList.remove('hover:bg-[var(--secondary-bg-color)]');
                document.getElementById(`step${currentStep}-indicator`).classList.remove('bg-[var(--primary-color)]');
                document.getElementById(`step${currentStep}-indicator`).classList.remove('hover:bg-[var(--h-primary-color)]');
                document.getElementById(`step${currentStep}-indicator`).classList.add('bg-[var(--h-bg-color)]');
                document.getElementById(`step${currentStep}-indicator`).classList.add('hover:bg-[var(--secondary-bg-color)]');
                document.getElementById('progress-bar').style.width = `${(step - 1) * (100/noOfSteps)}%`;
                currentStep = step - 1;
                updateButtons();
            }

            function gotoStep(step) {
                if (currentStep <= step) {
                    nextStep(step - 1);
                } else if (currentStep > step) {
                    prevStep(step + 1);
                }
            }

            function updateButtons() {
                document.getElementById('prevBtn').disabled = currentStep === 1;
                document.getElementById('nextBtn')?.classList.toggle('hidden', currentStep === noOfSteps);
                document.getElementById('saveBtn')?.classList.toggle('hidden', currentStep !== noOfSteps);
                document.getElementById('printAndSaveBtn')?.classList.toggle('hidden', currentStep !== noOfSteps);
                document.getElementById('quickInvoiceBtn')?.classList.toggle('hidden', currentStep !== noOfSteps);
            }


            document.getElementById('nextBtn')?.addEventListener('click', () => nextStep(currentStep));
            document.getElementById('prevBtn').addEventListener('click', () => prevStep(currentStep));
            document.getElementById('saveBtn')?.addEventListener('click', () => {
                document.getElementById('form').submit();
            });

            let saveBtn = document.getElementById('saveBtn');
            document.addEventListener("keydown", (e) => {
                // check if control ke is pressed
                if (e.ctrlKey && e.key === "ArrowRight") {
                    nextStep(currentStep);
                } else if ((e.ctrlKey && e.key === "ArrowLeft")) {
                    prevStep(currentStep);
                } else if (e.ctrlKey && e.key === "Enter") {
                    if (!saveBtn.classList.contains('hidden')) {
                        saveBtn.click();
                    }
                }
            })
        </script>
    @endif
    @if (!request()->is('login'))
        <script>
            document.addEventListener("keydown", (e) => {
                if (e.key == 'Escape') {
                    closeAllDropdowns();
                }
            })
        </script>
    @endif
    @if (request()->is('login'))
        <script>
            const html = document.documentElement;
            const themeIcon = document.querySelector('#themeToggle i');
            function changeTheme() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', newTheme);

                themeIcon?.classList.toggle('fa-sun');
                themeIcon?.classList.toggle('fa-moon');
            }

            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                document.cookie = `theme=${theme} path=/; max-age=31536000`;  // Save for 1 year
            }

            const userTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            setTheme(userTheme);
        </script>
    @endif
</footer>
<!-- Footer Component End -->
