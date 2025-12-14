<!-- Admin Navigation -->
<nav class="bg-white shadow-sm border-b border-gray-200 mb-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between py-4 gap-4">
            
            <!-- Logo & Mobile Menu Toggle -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 lg:gap-3">
                    <img src="/logo/logo.svg" alt="Depod.az Logo" class="h-6 w-6 lg:h-8 lg:w-8">
                    <h1 class="text-lg lg:text-2xl font-bold text-gray-800">Depod.az Admin</h1>
                </div>
                <button id="mobileMenuToggle" class="lg:hidden text-gray-700 hover:text-depod-red">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu -->
            <div id="mobileMenu" class="hidden lg:flex flex-col lg:flex-row items-start lg:items-center gap-3 lg:gap-6 w-full lg:w-auto">
                <a href="/admin/dashboard.php" class="text-gray-700 hover:text-depod-red transition-colors font-medium w-full lg:w-auto py-2 lg:py-0">
                    Dashboard
                </a>
                <a href="/admin/results.php" class="text-gray-700 hover:text-depod-red transition-colors font-medium w-full lg:w-auto py-2 lg:py-0">
                    Nəticələr
                </a>
                <a href="/admin/questions.php" class="text-gray-700 hover:text-depod-red transition-colors font-medium w-full lg:w-auto py-2 lg:py-0">
                    Suallar
                </a>
                <a href="/admin/products.php" class="text-gray-700 hover:text-depod-red transition-colors font-medium w-full lg:w-auto py-2 lg:py-0">
                    Məhsullar
                </a>
                <a href="/admin/settings.php" class="text-gray-700 hover:text-depod-red transition-colors font-medium w-full lg:w-auto py-2 lg:py-0">
                    Parametrlər
                </a>
                <a href="/admin/logout.php" class="bg-gradient-to-r from-depod-red to-depod-orange text-white px-4 py-2 rounded-lg hover:opacity-90 transition-all text-center w-full lg:w-auto">
                    Çıxış
                </a>
            </div>

        </div>
    </div>
</nav>

<script>
document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
});
</script>
