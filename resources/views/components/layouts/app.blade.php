<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Neraca</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="icon-wallet"></i>
            </div>
            <span class="sidebar-brand-name">Neraca</span>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="icon-layout-dashboard"></i> Dashboard
            </a>

            <div class="sidebar-section-label">Keuangan</div>
            <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="icon-arrow-left-right"></i> Transaksi
            </a>
            <a href="{{ route('accounts.index') }}" class="sidebar-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <i class="icon-credit-card"></i> Akun
            </a>
            <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="icon-tag"></i> Kategori
            </a>
            <a href="{{ route('recurring.index') }}" class="sidebar-link {{ request()->routeIs('recurring.*') ? 'active' : '' }}">
                <i class="icon-repeat"></i> Berulang
            </a>
            <a href="{{ route('templates.index') }}" class="sidebar-link {{ request()->routeIs('templates.*') ? 'active' : '' }}">
                <i class="icon-zap"></i> Template
            </a>

            <div class="sidebar-section-label">Perencanaan</div>
            <a href="{{ route('budgets.index') }}" class="sidebar-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                <i class="icon-piggy-bank"></i> Anggaran
            </a>
            <a href="{{ route('goals.index') }}" class="sidebar-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <i class="icon-target"></i> Target
            </a>

            <div class="sidebar-section-label">Laporan</div>
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="icon-bar-chart-3"></i> Laporan
            </a>
            <a href="{{ route('prediction.index') }}" class="sidebar-link {{ request()->routeIs('prediction.*') ? 'active' : '' }}">
                <i class="icon-trending-up"></i> Prediksi
            </a>
            <a href="{{ route('calendar.index') }}" class="sidebar-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="icon-calendar"></i> Kalender
            </a>

            <div class="sidebar-section-label">Akun</div>
            <a href="{{ route('profile.index') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="icon-user"></i> Profil
            </a>
            <a href="{{ route('2fa.setup') }}" class="sidebar-link {{ request()->routeIs('2fa.*') ? 'active' : '' }}">
                <i class="icon-shield"></i> 2FA
            </a>
            <a href="{{ route('activity.index') }}" class="sidebar-link {{ request()->routeIs('activity.*') ? 'active' : '' }}">
                <i class="icon-activity"></i> Aktivitas
            </a>
            <a href="{{ route('backup.import') }}" class="sidebar-link {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                <i class="icon-database"></i> Backup
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="sidebar-logout" title="Keluar">
                        <i class="icon-log-out" style="font-size: 15px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="main-content">
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 10px;">
                <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
                    <i class="icon-menu"></i>
                </button>
                <h1 class="topbar-title">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="topbar-actions">
                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </header>

        <main class="page-content">
            @if(session('success'))
                <div class="toast toast--success" id="toast">
                    <i class="icon-check-circle" style="font-size: 16px; color: var(--n-income);"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="toast toast--error" id="toast">
                    <i class="icon-alert-circle" style="font-size: 16px; color: var(--n-expense);"></i>
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>

    {{-- Global Confirm Modal --}}
    <div class="modal-overlay" id="confirmModal">
        <div class="modal" style="max-width: 380px;">
            <div class="modal-body" style="text-align: center; padding: 28px 24px 20px;">
                <div id="confirmIcon" style="width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; background: rgba(220,38,38,0.08); color: #DC2626;">
                    <i class="icon-triangle-alert" style="font-size: 22px;"></i>
                </div>
                <h3 id="confirmTitle" style="font-size: 15px; font-weight: 600; margin-bottom: 6px;">Konfirmasi</h3>
                <p id="confirmMessage" style="font-size: 12.5px; color: var(--n-text-secondary); line-height: 1.5; margin-bottom: 0;">Yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 8px; padding-top: 0;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeConfirm()" style="min-width: 90px;">Batal</button>
                <button type="button" id="confirmBtn" class="btn btn-sm" style="min-width: 90px; background: #DC2626; color: #fff;">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }

        // Auto-dismiss toast
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-12px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        
        function formatCurrency(value) {
            const num = value.replace(/\D/g, '');
            return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function parseCurrency(value) {
            return value.replace(/\./g, '');
        }

        // ===== Global Confirm Modal =====
        let pendingForm = null;

        function showConfirm(form, message, title) {
            pendingForm = form;
            document.getElementById('confirmTitle').textContent = title || 'Konfirmasi';
            document.getElementById('confirmMessage').textContent = message || 'Yakin ingin melanjutkan?';
            document.getElementById('confirmModal').classList.add('open');
            document.getElementById('confirmBtn').onclick = function() {
                if (pendingForm) pendingForm.submit();
                closeConfirm();
            };
            return false;
        }

        function closeConfirm() {
            document.getElementById('confirmModal').classList.remove('open');
            pendingForm = null;
        }

        // ===== Password Eye Toggle =====
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="icon-eye-off" style="font-size:14px;"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="icon-eye" style="font-size:14px;"></i>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Format on input
            document.querySelectorAll('[data-currency]').forEach(input => {
                input.addEventListener('input', function() {
                    const pos = this.selectionStart;
                    const oldLen = this.value.length;
                    this.value = formatCurrency(this.value);
                    const newLen = this.value.length;
                    this.setSelectionRange(pos + (newLen - oldLen), pos + (newLen - oldLen));
                });

                // Format initial value if present
                if (input.value && !isNaN(input.value)) {
                    input.value = formatCurrency(input.value);
                }
            });

            // Strip dots before form submission
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    this.querySelectorAll('[data-currency]').forEach(input => {
                        input.value = parseCurrency(input.value);
                    });
                });
            });

            // Auto-attach confirm to data-confirm forms
            document.querySelectorAll('[data-confirm]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirm(this, this.dataset.confirm, this.dataset.confirmTitle);
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
