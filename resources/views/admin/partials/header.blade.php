<header>
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <span>🏠</span>
        <a href="{{ route('dashboard') }}">Backoffice</a>
        @php
            $currentRoute = request()->route()->getName();
            $breadcrumbs = [];

            // Define breadcrumb structure with links
            $breadcrumbMap = [
                'dashboard' => [['label' => 'Dashboard', 'link' => null]],
                'admin.kandang' => [
                    ['label' => 'Master', 'link' => '#', 'class' => 'category-link'],
                    ['label' => 'Kandang', 'link' => null]
                ],
                'admin.karyawan' => [
                    ['label' => 'Master', 'link' => '#', 'class' => 'category-link'],
                    ['label' => 'Karyawan', 'link' => null]
                ],
                'admin.penetasan' => [
                    ['label' => 'Operasional', 'link' => '#', 'class' => 'category-link'],
                    ['label' => 'Penetasan', 'link' => null]
                ],
                'admin.pembesaran' => [
                    ['label' => 'Operasional', 'link' => '#', 'class' => 'category-link'],
                    ['label' => 'Pembesaran', 'link' => null]
                ],
                'admin.produksi' => [
                    ['label' => 'Operasional', 'link' => '#', 'class' => 'category-link'],
                    ['label' => 'Produksi', 'link' => null]
                ],
            ];

            if (isset($breadcrumbMap[$currentRoute])) {
                $breadcrumbs = $breadcrumbMap[$currentRoute];
            } else {
                $breadcrumbs = [['label' => $title ?? 'Dashboard', 'link' => null]];
            }
        @endphp

        @foreach($breadcrumbs as $index => $crumb)
            <span>/</span>
            @if($index === count($breadcrumbs) - 1)
                <span class="active">{{ $crumb['label'] }}</span>
            @elseif(isset($crumb['link']) && $crumb['link'] !== null)
                <a href="{{ $crumb['link'] }}" class="{{ $crumb['class'] ?? 'breadcrumb-link' }}">{{ $crumb['label'] }}</a>
            @else
                <span class="breadcrumb-category">{{ $crumb['label'] }}</span>
            @endif
        @endforeach
    </div>

    <!-- Right Info -->
    <div class="info">
        <div class="date-time">
            <span id="date"></span> | <span id="clock"></span>
        </div>
        <div class="status">
            <div class="status-dot"></div>
            Online
        </div>
        <div class="user" id="userMenu">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}</div>
            <div class="user-info">
                <span>{{ explode(' ', auth()->user()->nama)[0] ?? 'Admin' }}</span>
                <span class="role">{{ auth()->user()->peran === 'owner' ? 'Owner' : 'Operator' }}</span>
            </div>

            <!-- Dropdown -->
            <div class="dropdown" id="dropdownMenu">
                <a href="{{ route('profile.edit') }}">👤 Profile</a>
                <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">🚪 Logout</a>
                <form id="header-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>

<style>
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        padding: 12px 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border-radius: 0 0 16px 16px;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Left - Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        color: #555;
    }

    .breadcrumb a {
        color: #555;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        color: #2563eb;
    }

    .breadcrumb span.active {
        font-weight: 600;
        color: #2563eb;
    }

    .breadcrumb-category {
        color: #6b7280;
        font-weight: 500;
        font-size: 13px;
    }

    .breadcrumb-link {
        color: #555;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .category-link {
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
    }

    .category-link:hover {
        color: #374151;
        text-decoration: underline;
    }

    /* Right - Info */
    .info {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 14px;
        color: #555;
    }

    .status {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #16a34a;
        font-weight: 500;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #16a34a;
        border-radius: 50%;
    }

    /* User */
    .user {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        position: relative;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        background: #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        font-size: 13px;
        line-height: 1.2;
    }

    .user-info .role {
        font-size: 12px;
        color: #777;
    }

    /* Dropdown */
    .dropdown {
        display: none;
        position: absolute;
        top: 48px;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
        z-index: 100;
    }

    .dropdown a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 8px;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }

    .dropdown a:hover {
        background: #f1f5f9;
    }

    .dropdown a.logout {
        color: #ef4444;
        font-weight: 500;
    }

    .dropdown.show {
        display: block;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Update date & clock
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById("date").textContent = now.toLocaleDateString('en-US', options);
        document.getElementById("clock").textContent = now.toLocaleTimeString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Dropdown toggle
    const userMenu = document.getElementById("userMenu");
    const dropdownMenu = document.getElementById("dropdownMenu");

    userMenu.addEventListener("click", () => {
        dropdownMenu.classList.toggle("show");
    });

    // Klik di luar dropdown untuk menutup
    document.addEventListener("click", (e) => {
        if (!userMenu.contains(e.target)) {
            dropdownMenu.classList.remove("show");
        }
    });

    // Breadcrumb category link handler
    document.querySelectorAll('.category-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const category = e.target.textContent.toLowerCase();

            // Toggle sidebar menu based on category
            const sidebar = document.querySelector('.sidebar');
            const miniMenus = document.querySelectorAll('.mini-menu');

            // Remove active class from all mini menus
            miniMenus.forEach(menu => menu.classList.remove('active'));

            // Add active class to corresponding mini menu
            if (category === 'master') {
                miniMenus[1].classList.add('active'); // Master menu
                // Switch to master menu content
                document.querySelectorAll('[data-group]').forEach(group => {
                    group.style.display = group.getAttribute('data-group') === 'master' ? 'block' : 'none';
                });
                // Update nav-list data-active
                document.querySelector('.nav-list').setAttribute('data-active', 'master');
                // Update section text
                const sectionText = document.querySelector('.section-text');
                if (sectionText) sectionText.textContent = 'Master';
                // Save to localStorage
                localStorage.setItem('activeMenu', 'master');
            } else if (category === 'operasional') {
                miniMenus[0].classList.add('active'); // Operasional menu
                // Switch to operasional menu content
                document.querySelectorAll('[data-group]').forEach(group => {
                    group.style.display = group.getAttribute('data-group') === 'operasional' ? 'block' : 'none';
                });
                // Update nav-list data-active
                document.querySelector('.nav-list').setAttribute('data-active', 'operasional');
                // Update section text
                const sectionText = document.querySelector('.section-text');
                if (sectionText) sectionText.textContent = 'Operasional';
                // Save to localStorage
                localStorage.setItem('activeMenu', 'operasional');
            }

            // Auto-expand sidebar if collapsed
            if (!sidebar.classList.contains('open')) {
                sidebar.classList.add('open');
                document.querySelector('.home-section').style.left = '250px';
                document.querySelector('.home-section').style.width = 'calc(100% - 250px)';
                // Save sidebar state
                localStorage.setItem('sidebarOpen', 'true');
                // Update menu button
                const menuBtn = document.querySelector('#btn');
                if (menuBtn) menuBtn.src = '{{ asset("bolopa/img/icon/line-md--menu-fold-left.svg") }}';
            }
        });
    });
</script>