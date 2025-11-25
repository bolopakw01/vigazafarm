<header class="bolopa-header-vigazafarm-header">
  <div class="bolopa-header-vigazafarm-header-top">
    @php
        $authUser = auth()->user();
        $fullName = trim($authUser->nama ?? '');
        $firstName = $fullName !== '' ? explode(' ', $fullName)[0] : 'Admin';
        if ($firstName === '') {
            $firstName = 'Admin';
        }
        $initialSource = $fullName !== '' ? $fullName : 'A';
        $userInitial = function_exists('mb_substr')
            ? mb_strtoupper(mb_substr($initialSource, 0, 1))
            : strtoupper(substr($initialSource, 0, 1));
  $profilePhotoPath = $authUser && $authUser->foto_profil && file_exists(public_path('foto_profil/' . $authUser->foto_profil)) ? asset('foto_profil/' . $authUser->foto_profil) : null;
        $userRoleLabel = $authUser && $authUser->peran === 'owner' ? 'owner' : 'operator';
    @endphp
    <div class="bolopa-header-vigazafarm-header-left">
      @php
          $currentRoute = request()->route()->getName();
          $breadcrumbs = [];
          $userRole = $authUser->peran ?? 'operator';
          
          // Get route parameters for dynamic links
          $routeParams = request()->route()->parameters();
          $pembesaranId = $routeParams['pembesaran']->id ?? null;

          $breadcrumbMap = [
              'dashboard' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => null]],
              'admin.dashboard' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => null], ['label' => 'Dashboard', 'link' => null]],
              'admin.kandang' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Kandang', 'link' => null]],
              'admin.kandang.create' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Kandang', 'link' => route('admin.kandang'), 'class' => 'breadcrumb-link'], ['label' => 'Tambah Data', 'link' => null]],
              'admin.kandang.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Kandang', 'link' => route('admin.kandang'), 'class' => 'breadcrumb-link'], ['label' => 'Edit Data', 'link' => null]],
              'admin.karyawan' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Karyawan', 'link' => null]],
              'admin.karyawan.create' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Karyawan', 'link' => route('admin.karyawan'), 'class' => 'breadcrumb-link'], ['label' => 'Tambah Data', 'link' => null]],
              'admin.karyawan.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Karyawan', 'link' => route('admin.karyawan'), 'class' => 'breadcrumb-link'], ['label' => 'Edit Data', 'link' => null]],
              'admin.sistem' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => null]],
              'admin.sistem.dashboard' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Pengaturan Dashboard', 'link' => null]],
              'admin.sistem.matriks' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Set Matriks', 'link' => null]],
              'admin.sistem.pakanvitamin' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Set Pakan & Vitamin', 'link' => null]],
              'admin.sistem.database.backup' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Backup Database', 'link' => null]],
              'admin.sistem.database.restore' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Restore Database', 'link' => null]],
              'admin.sistem.database.connection' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Koneksi Database', 'link' => null]],
              'admin.sistem.database.optimization' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Master', 'link' => '#', 'class' => 'category-link'], ['label' => 'Sistem', 'link' => route('admin.sistem'), 'class' => 'breadcrumb-link'], ['label' => 'Optimasi Database', 'link' => null]],
              'profile.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Profile', 'link' => null]],
              'admin.penetasan' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Penetasan', 'link' => null]],
              'admin.penetasan.create' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Penetasan', 'link' => route('admin.penetasan'), 'class' => 'breadcrumb-link'], ['label' => 'Tambah Data', 'link' => null]],
              'admin.penetasan.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Penetasan', 'link' => route('admin.penetasan'), 'class' => 'breadcrumb-link'], ['label' => 'Edit Data', 'link' => null]],
              'admin.pembesaran' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Pembesaran', 'link' => null]],
              'admin.pembesaran.create' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Pembesaran', 'link' => route('admin.pembesaran'), 'class' => 'breadcrumb-link'], ['label' => 'Tambah Data', 'link' => null]],
              'admin.pembesaran.createFromPenetasan' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Penetasan', 'link' => route('admin.penetasan'), 'class' => 'breadcrumb-link'], ['label' => 'Transfer ke Pembesaran', 'link' => null]],
              'admin.pembesaran.show' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Pembesaran', 'link' => route('admin.pembesaran'), 'class' => 'breadcrumb-link'], ['label' => 'Detail Data', 'link' => null]],
              'admin.pembesaran.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Pembesaran', 'link' => route('admin.pembesaran'), 'class' => 'breadcrumb-link'], ['label' => 'Edit Data', 'link' => null]],
              'admin.pembesaran.recording.laporan.show' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Pembesaran', 'link' => route('admin.pembesaran'), 'class' => 'breadcrumb-link'], ['label' => 'Detail', 'link' => $pembesaranId ? route('admin.pembesaran.show', $pembesaranId) : '#', 'class' => 'breadcrumb-link'], ['label' => 'Catatan', 'link' => null]],
              'admin.produksi' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Produksi', 'link' => null]],
              'admin.produksi.create' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Produksi', 'link' => route('admin.produksi'), 'class' => 'breadcrumb-link'], ['label' => trim(View::yieldContent('title')) !== '' ? View::yieldContent('title') : 'Tambah Data', 'link' => null]],
              'admin.produksi.show' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Produksi', 'link' => route('admin.produksi'), 'class' => 'breadcrumb-link'], ['label' => 'Detail Data', 'link' => null]],
              'admin.produksi.edit' => [['label' => '🏠', 'link' => null], ['label' => 'Backoffice', 'link' => '#', 'class' => 'category-link'], ['label' => 'Produksi', 'link' => route('admin.produksi'), 'class' => 'breadcrumb-link'], ['label' => trim(View::yieldContent('title')) !== '' ? View::yieldContent('title') : 'Edit Data', 'link' => null]],
          ];

          // Untuk owner, tambahkan "Operasional" di breadcrumb operasional menu
      if ($userRole === 'owner') {
        $operationalRoutes = ['admin.penetasan', 'admin.penetasan.create', 'admin.penetasan.edit', 'admin.pembesaran', 'admin.pembesaran.create', 'admin.pembesaran.createFromPenetasan', 'admin.pembesaran.show', 'admin.pembesaran.edit', 'admin.pembesaran.recording.laporan.show', 'admin.produksi', 'admin.produksi.create', 'admin.produksi.show', 'admin.produksi.edit'];
              if (in_array($currentRoute, $operationalRoutes) && isset($breadcrumbMap[$currentRoute])) {
                  // Insert "Operasional" setelah "Backoffice"
                  $temp = $breadcrumbMap[$currentRoute];
                  array_splice($temp, 2, 0, [['label' => 'Operasional', 'link' => '#', 'class' => 'category-link']]);
                  $breadcrumbMap[$currentRoute] = $temp;
              }
          }

          if (isset($breadcrumbMap[$currentRoute])) {
              $breadcrumbs = $breadcrumbMap[$currentRoute];
          } else {
              $breadcrumbs = [['label' => $title ?? 'Dashboard', 'link' => null]];
          }
      @endphp

      <div class="bolopa-header-vigazafarm-breadcrumb">
        @foreach($breadcrumbs as $index => $crumb)
          @if($index === count($breadcrumbs) - 1)
            <span class="bolopa-header-vigazafarm-active">{{ $crumb['label'] }}</span>
          @elseif(isset($crumb['link']) && $crumb['link'] !== null)
            <a href="{{ $crumb['link'] }}" class="{{ $crumb['class'] ?? 'breadcrumb-link' }}">{{ $crumb['label'] }}</a>
          @else
            <span class="bolopa-header-vigazafarm-category">{{ $crumb['label'] }}</span>
          @endif
          @if($index < count($breadcrumbs) - 1 && $index > 0) <span class="bolopa-header-vigazafarm-separator">/</span> @endif
        @endforeach
      </div>

      <div class="bolopa-header-vigazafarm-status-text">
        <div class="bolopa-header-vigazafarm-status-text-dot"></div>
        <span>Online</span>
      </div>
    </div>

    <div class="bolopa-header-vigazafarm-info">
      <div class="bolopa-header-vigazafarm-date-time">
        <span id="bolopaHeaderDate"></span> | <span id="bolopaHeaderClock"></span>
      </div>
      <div class="bolopa-header-vigazafarm-status">
        <div class="bolopa-header-vigazafarm-status-dot"></div>
        Online
      </div>
      <div class="bolopa-header-vigazafarm-user" id="bolopaHeaderUserMenu">
        <div class="bolopa-header-vigazafarm-user-avatar">
          @if($profilePhotoPath)
            <img src="{{ $profilePhotoPath }}" alt="Foto Profil {{ $fullName !== '' ? $fullName : 'Pengguna' }}" onerror="this.style.display='none'; var fallback = this.nextElementSibling; if (fallback) { fallback.style.display='flex'; }">
          @endif
          <span class="avatar-initial" @if($profilePhotoPath) style="display:none;" @endif>{{ $userInitial }}</span>
        </div>
        <div class="bolopa-header-vigazafarm-user-info">
          <span>{{ $firstName }}</span>
          <span class="bolopa-header-vigazafarm-role">{{ $userRoleLabel }}</span>
        </div>

        <!-- Dropdown -->
        <div class="bolopa-header-vigazafarm-dropdown" id="bolopaHeaderDropdown">
          <a href="{{ route('profile.edit') }}">👤 Profile</a>
          <a href="#" class="bolopa-header-vigazafarm-logout" onclick="event.preventDefault(); document.getElementById('bolopaHeaderLogoutForm').submit();">🚪 Logout</a>
          <form id="bolopaHeaderLogoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
          </form>
        </div>
      </div>

    </div>
  </div>

  <!-- Hamburger (mobile) -->
  <button class="bolopa-header-vigazafarm-hamburger" id="bolopaHeaderHamburger" aria-label="Open menu" aria-expanded="false">
    <svg class="bolopa-header-vigazafarm-hamburger-icon" width="24" height="24" viewBox="0 0 24 24">
      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5L12 12L19 5M12 12H12M5 19L12 12L19 19">
        <animate fill="freeze" attributeName="d" dur="0.4s" values="M5 5L12 12L19 5M12 12H12M5 19L12 12L19 19;M5 5L12 5L19 5M5 12H19M5 19L12 19L19 19"/>
      </path>
    </svg>
  </button>

  <nav class="bolopa-header-vigazafarm-mobile-menu" id="bolopaHeaderMobileMenu" aria-hidden="true" inert>
    <button class="bolopa-header-vigazafarm-close" id="bolopaHeaderMobileClose" aria-label="Close menu">
      <svg width="20" height="20" viewBox="0 0 24 24">
        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5L12 5L19 5M5 12H19M5 19L12 19L19 19">
          <animate fill="freeze" attributeName="d" dur="0.4s" values="M5 5L12 5L19 5M5 12H19M5 19L12 19L19 19;M5 5L12 12L19 5M12 12H12M5 19L12 12L19 19"/>
        </path>
      </svg>
    </button>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
      <div class="bolopa-header-vigazafarm-user-avatar bolopa-header-vigazafarm-user-avatar--mobile" style="width:46px;height:46px;font-size:18px;">
        @if($profilePhotoPath)
          <img src="{{ $profilePhotoPath }}" alt="Foto Profil {{ $fullName !== '' ? $fullName : 'Pengguna' }}" onerror="this.style.display='none'; var fallback = this.nextElementSibling; if (fallback) { fallback.style.display='flex'; }">
        @endif
        <span class="avatar-initial" @if($profilePhotoPath) style="display:none;" @endif>{{ $userInitial }}</span>
      </div>
      <div style="display:flex;flex-direction:column;">
        <strong>{{ $firstName }}</strong>
        <small class="bolopa-header-vigazafarm-role">{{ $userRoleLabel }}</small>
      </div>
    </div>
    <div class="bolopa-header-vigazafarm-menu-links">
      <a href="{{ route('profile.edit') }}">👤 Profile</a>
      <a href="#" class="bolopa-header-vigazafarm-logout" onclick="event.preventDefault(); document.getElementById('bolopaHeaderLogoutForm').submit();">🚪 Logout</a>
    </div>
    <div style="flex:1"></div>
    <div style="font-size:13px;color:#666;border-top:1px solid #eee;padding-top:10px;">
      <div id="bolopaHeaderMobileDate"></div>
    </div>
  </nav>

  <style>
    .bolopa-header-vigazafarm-header {
      background: #fff;
      padding: 12px 24px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      border-radius: 0 0 16px 16px;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .bolopa-header-vigazafarm-header-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Left - Breadcrumb */
    .bolopa-header-vigazafarm-breadcrumb {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 14px;
      color: #555;
    }

    .bolopa-header-vigazafarm-breadcrumb span:first-child {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 16px; /* Fixed width for consistent alignment */
    }

    .bolopa-header-vigazafarm-breadcrumb span.bolopa-header-vigazafarm-active {
      font-weight: 600;
      color: #2563eb;
    }

    .bolopa-header-vigazafarm-breadcrumb a, .bolopa-header-vigazafarm-category {
      color: #777;
      text-decoration: none;
    }

    /* Right - Info */
    .bolopa-header-vigazafarm-info {
      display: flex;
      align-items: center;
      gap: 20px;
      font-size: 14px;
      color: #555;
    }

    .bolopa-header-vigazafarm-status {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #16a34a;
      font-weight: 500;
    }

    .bolopa-header-vigazafarm-status-dot {
      width: 8px;
      height: 8px;
      background: #16a34a;
      border-radius: 50%;
      animation: pulse 2s infinite;
    }

    /* User */
    .bolopa-header-vigazafarm-user {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      position: relative;
    }

    .bolopa-header-vigazafarm-user-avatar {
      width: 36px;
      height: 36px;
      background: #3b82f6;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 600;
      overflow: hidden;
      position: relative;
    }

    .bolopa-header-vigazafarm-user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      display: block;
    }

    .bolopa-header-vigazafarm-user-avatar .avatar-initial {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .bolopa-header-vigazafarm-user-info {
      display: flex;
      flex-direction: column;
      font-size: 13px;
      line-height: 1.2;
    }

    .bolopa-header-vigazafarm-user-info .bolopa-header-vigazafarm-role {
      font-size: 12px;
      color: #777;
    }

    /* Dropdown */
    .bolopa-header-vigazafarm-dropdown {
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

    .bolopa-header-vigazafarm-dropdown a {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 8px;
      font-size: 14px;
      color: #333;
      text-decoration: none;
      transition: background 0.2s;
    }

    .bolopa-header-vigazafarm-dropdown a:hover {
      background: #f1f5f9;
    }

    .bolopa-header-vigazafarm-dropdown a.bolopa-header-vigazafarm-logout {
      color: #ef4444;
      font-weight: 500;
    }

    .bolopa-header-vigazafarm-dropdown.bolopa-header-vigazafarm-show {
      display: block;
      animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Hamburger (mobile) */
    .bolopa-header-vigazafarm-hamburger {
      display: none;
      width: 42px;
      height: 42px;
      border-radius: 8px;
      background: transparent;
      border: 1px solid transparent;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #333;
      transition: color 0.2s ease;
    }

    .bolopa-header-vigazafarm-hamburger:hover {
      color: #2563eb;
    }

    .bolopa-header-vigazafarm-hamburger-icon {
      transition: transform 0.2s ease;
    }

    /* Mobile slide-in menu */
    .bolopa-header-vigazafarm-mobile-menu {
      position: fixed;
      top: 0;
      right: 0;
      width: 280px;
      height: 100vh;
      background: #fff;
      box-shadow: -8px 0 30px rgba(0,0,0,0.12);
      transform: translateX(100%);
      transition: transform 0.25s ease;
      z-index: 200;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .bolopa-header-vigazafarm-mobile-menu.bolopa-header-vigazafarm-open { transform: translateX(0); }

    .bolopa-header-vigazafarm-mobile-menu .bolopa-header-vigazafarm-close {
      align-self: flex-end;
      background: transparent;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #666;
      transition: color 0.2s ease;
    }

    .bolopa-header-vigazafarm-mobile-menu .bolopa-header-vigazafarm-close:hover {
      color: #2563eb;
    }

    .bolopa-header-vigazafarm-mobile-menu .bolopa-header-vigazafarm-menu-links a {
      display: block;
      padding: 12px 10px;
      color: #333;
      text-decoration: none;
      border-radius: 8px;
    }

    .bolopa-header-vigazafarm-mobile-menu .bolopa-header-vigazafarm-menu-links a.bolopa-header-vigazafarm-logout { color: #ef4444; font-weight: 600; }

    /* Status text below breadcrumb */
    .bolopa-header-vigazafarm-status-text {
      display: none;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: #16a34a;
      font-weight: 500;
      margin-top: 8px;
      padding-left: 3px; /* Align dot with home icon center */
    }

    .bolopa-header-vigazafarm-status-text-dot {
      width: 10px;
      height: 10px;
      background: #16a34a;
      border-radius: 50%;
      animation: pulse 2s infinite;
      flex-shrink: 0; /* Prevent dot from shrinking */
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    @media (max-width: 1024px) {
      /* Tablet styles */
      .bolopa-header-vigazafarm-header { padding: 10px 20px; }
      .bolopa-header-vigazafarm-breadcrumb { gap: 5px; font-size: 13px; }
      .bolopa-header-vigazafarm-info { gap: 15px; }
      .bolopa-header-vigazafarm-user-info { display: none; }
      .bolopa-header-vigazafarm-user-avatar { width: 32px; height: 32px; }
      .bolopa-header-vigazafarm-mobile-menu { width: 320px; padding: 24px; }
    }

    @media (max-width: 768px) {
      /* Mobile styles */
      .bolopa-header-vigazafarm-header { padding: 10px 16px; }
      .bolopa-header-vigazafarm-info { display: none; }
      .bolopa-header-vigazafarm-hamburger { display: inline-flex; position: absolute; top: 10px; right: 16px; }
      .bolopa-header-vigazafarm-breadcrumb { gap: 4px; font-size: 13px; }
      .bolopa-header-vigazafarm-breadcrumb > *:not(:first-child):not(.bolopa-header-vigazafarm-active) { display: none; }
      .bolopa-header-vigazafarm-status-text { display: flex; }
      .bolopa-header-vigazafarm-mobile-menu { width: 280px; padding: 20px; }
    }
  </style>

  <script>
    (function () {
      const headerRoot = document.querySelector('.bolopa-header-vigazafarm-header');
      if (!headerRoot) {
        return;
      }

      const dateEl = document.getElementById('bolopaHeaderDate');
      const clockEl = document.getElementById('bolopaHeaderClock');
      const mobileDateEl = document.getElementById('bolopaHeaderMobileDate');
      const userMenu = document.getElementById('bolopaHeaderUserMenu');
      const dropdownMenu = document.getElementById('bolopaHeaderDropdown');
      const hamburger = document.getElementById('bolopaHeaderHamburger');
      const mobileMenu = document.getElementById('bolopaHeaderMobileMenu');
      const mobileClose = document.getElementById('bolopaHeaderMobileClose');

      const runDateTime = () => {
        const now = new Date();
        if (dateEl) {
          dateEl.textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
          });
        }
        if (clockEl) {
          clockEl.textContent = now.toLocaleTimeString();
        }
        if (mobileDateEl && dateEl) {
          mobileDateEl.textContent = dateEl.textContent;
        }
      };

      if (dateEl || clockEl) {
        runDateTime();
        setInterval(runDateTime, 1000);
      }

      if (userMenu && dropdownMenu) {
        userMenu.addEventListener('click', (event) => {
          event.stopPropagation();
          dropdownMenu.classList.toggle('bolopa-header-vigazafarm-show');
        });

        document.addEventListener('click', (event) => {
          const isInsideUserMenu = userMenu.contains(event.target);
          const isInsideDropdown = dropdownMenu.contains(event.target);
          if (!isInsideUserMenu && !isInsideDropdown) {
            dropdownMenu.classList.remove('bolopa-header-vigazafarm-show');
          }
        });
      }

      const playHamburgerAnimation = () => {
        if (!hamburger) {
          return;
        }
        const svg = hamburger.querySelector('svg');
        const animate = svg ? svg.querySelector('animate') : null;
        if (animate) {
          animate.beginElement();
        }
      };

      const setMobileMenuAccessibility = (isOpen) => {
        if (!mobileMenu) {
          return;
        }
        mobileMenu.setAttribute('aria-hidden', String(!isOpen));
        if (isOpen) {
          mobileMenu.removeAttribute('inert');
        } else {
          mobileMenu.setAttribute('inert', '');
        }
      };

      const openMobile = () => {
        if (!mobileMenu || !hamburger) {
          return;
        }
        mobileMenu.classList.add('bolopa-header-vigazafarm-open');
        hamburger.classList.add('bolopa-header-vigazafarm-open');
        hamburger.setAttribute('aria-expanded', 'true');
        setMobileMenuAccessibility(true);
        playHamburgerAnimation();
        if (mobileDateEl && dateEl) {
          mobileDateEl.textContent = dateEl.textContent;
        }
      };

      const closeMobile = () => {
        if (!mobileMenu || !hamburger) {
          return;
        }
        mobileMenu.classList.remove('bolopa-header-vigazafarm-open');
        hamburger.classList.remove('bolopa-header-vigazafarm-open');
        hamburger.setAttribute('aria-expanded', 'false');
        const activeElement = document.activeElement;
        if (activeElement && mobileMenu.contains(activeElement) && typeof activeElement.blur === 'function') {
          activeElement.blur();
        }
        setMobileMenuAccessibility(false);
        playHamburgerAnimation();
      };

      if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', (event) => {
          event.stopPropagation();
          if (mobileMenu.classList.contains('bolopa-header-vigazafarm-open')) {
            closeMobile();
          } else {
            openMobile();
          }
        });

        if (mobileClose) {
          mobileClose.addEventListener('click', closeMobile);
        }

        document.addEventListener('click', (event) => {
          if (!mobileMenu.contains(event.target) && !hamburger.contains(event.target)) {
            closeMobile();
          }
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closeMobile();
          }
        });
      }

      const sidebar = document.querySelector('.bolopa-sidebar-vigazafarm');
      const miniMenus = document.querySelectorAll('.mini-menu');
      const groupedMenus = document.querySelectorAll('[data-group]');
      const navList = document.querySelector('.nav-list');
      const sectionText = document.querySelector('.section-text');
      const homeSection = document.querySelector('.home-section');
      const menuBtn = document.querySelector('#btn');

      headerRoot.querySelectorAll('.category-link').forEach((link) => {
        link.addEventListener('click', (event) => {
          event.preventDefault();
          const category = (link.textContent || '').trim().toLowerCase();
          if (category !== 'master' && category !== 'operasional') {
            return;
          }

          miniMenus.forEach((menu) => menu.classList.remove('active'));
          const targetIndex = category === 'operasional' ? 0 : 1;
          if (miniMenus[targetIndex]) {
            miniMenus[targetIndex].classList.add('active');
          }

          groupedMenus.forEach((group) => {
            group.style.display = group.getAttribute('data-group') === category ? 'block' : 'none';
          });

          if (navList) {
            navList.setAttribute('data-active', category);
          }

          if (sectionText) {
            sectionText.textContent = category === 'master' ? 'Master' : 'Operasional';
          }

          localStorage.setItem('activeMenu', category);

          if (sidebar && !sidebar.classList.contains('open')) {
            sidebar.classList.add('open');
            if (homeSection) {
              homeSection.style.left = '250px';
              homeSection.style.width = 'calc(100% - 250px)';
            }
            localStorage.setItem('sidebarOpen', 'true');
            if (menuBtn) {
              menuBtn.src = '{{ asset("bolopa/img/icon/line-md--menu-fold-left.svg") }}';
            }
          }
        });
      });
    })();
  </script>

</header>
