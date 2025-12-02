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
          $pageTitle = trim($title ?? '');
          $dashboardLink = \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/');
          $dashboardIcon = asset('bolopa/img/icon/bolopa-noto--house.svg');
          $imageExtensions = ['.svg', '.png', '.jpg', '.jpeg', '.webp', '.gif'];

          $normalizeIcon = function ($icon) use ($imageExtensions) {
            if (empty($icon)) {
              return null;
            }

            if (is_array($icon)) {
              $value = $icon['src'] ?? $icon['value'] ?? $icon['text'] ?? null;
              if ($value === null) {
                return null;
              }

              $type = $icon['type'] ?? ((is_string($value) && \Illuminate\Support\Str::endsWith(strtolower($value), $imageExtensions)) ? 'image' : 'text');

              return [
                'type' => $type,
                'value' => $value,
                'alt' => $icon['alt'] ?? null,
              ];
            }

            $iconString = (string) $icon;
            $lowerIconString = strtolower($iconString);
            $isImage = \Illuminate\Support\Str::endsWith($lowerIconString, $imageExtensions)
              || filter_var($iconString, FILTER_VALIDATE_URL);

            return [
              'type' => $isImage ? 'image' : 'text',
              'value' => $iconString,
              'alt' => null,
            ];
          };

            $defaultTrail = [
              ['label' => 'Backoffice', 'link' => $dashboardLink, 'icon' => ['type' => 'image', 'src' => $dashboardIcon, 'alt' => 'Backoffice']],
              ['label' => $pageTitle !== '' ? $pageTitle : 'Dashboard', 'link' => null],
            ];

            $rawBreadcrumbs = $breadcrumbs ?? $defaultTrail;

            if ($rawBreadcrumbs instanceof \Illuminate\Support\Collection) {
              if ($rawBreadcrumbs->isEmpty()) {
                $rawBreadcrumbs = $defaultTrail;
              }
            } elseif (empty($rawBreadcrumbs)) {
              $rawBreadcrumbs = $defaultTrail;
            }

            $resolvedBreadcrumbs = collect($rawBreadcrumbs)
            ->map(function ($crumb) use ($normalizeIcon, $dashboardIcon, $dashboardLink) {
              if (is_string($crumb)) {
                $crumb = [
                  'label' => $crumb,
                  'link' => null,
                  'badge' => null,
                ];
              } elseif ($crumb instanceof \Illuminate\Support\Collection) {
                $crumb = $crumb->toArray();
              }

              $icon = $crumb['icon'] ?? null;
              if (($crumb['label'] ?? '') === 'Backoffice') {
                if (empty($crumb['link'])) {
                  $crumb['link'] = $dashboardLink;
                }
                if (!$icon) {
                  $icon = ['type' => 'image', 'src' => $dashboardIcon, 'alt' => 'Backoffice'];
                }
              }

              return [
                'label' => $crumb['label'] ?? ($crumb['title'] ?? ''),
                'link' => $crumb['link'] ?? ($crumb['url'] ?? null),
                'icon' => $normalizeIcon($icon),
                'badge' => $crumb['badge'] ?? null,
              ];
            })
              ->filter(fn ($crumb) => $crumb['label'] !== '')
              ->values();
        @endphp

      <div class="bolopa-header-vigazafarm-breadcrumb-wrapper">
        @include('admin.partials.breadcrumb', ['items' => $resolvedBreadcrumbs])
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
      position: fixed;
      top: 0;
      left: 78px;
      right: 0;
      z-index: 1100;
      width: auto;
      background: rgba(255,255,255,0.98);
      padding: 12px 24px;
      border-radius: 0 0 16px 16px;
      box-shadow: 0 2px 4px rgba(15,23,42,0.08);
      backdrop-filter: blur(6px);
      transition: left 0.5s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    /* Adjust header position when sidebar is open on desktop */
    @media (min-width: 1025px) {
      .bolopa-sidebar-vigazafarm.open ~ .home-section .bolopa-header-vigazafarm-header {
        left: 250px;
      }
    }

    .bolopa-header-vigazafarm-header--shadow {
      box-shadow: 0 12px 34px rgba(15,23,42,0.12);
      background: rgba(255,255,255,0.96);
    }

    .bolopa-header-vigazafarm-header-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
    }

    .bolopa-header-vigazafarm-header-left {
      flex: 1 1 auto;
      min-width: 0;
    }

    /* Left - Breadcrumb */
    .bolopa-header-vigazafarm-breadcrumb-wrapper {
      display: flex;
      align-items: center;
      gap: 18px;
      flex: 1 1 auto;
      min-width: 0;
    }

    .bolopa-breadcrumb-pill {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 16px;
      background: linear-gradient(135deg, rgba(37,99,235,0.08), rgba(14,165,233,0.08));
      border-radius: 999px;
      border: 1px solid rgba(148,163,184,0.25);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.35);
      list-style: none;
      flex: 1 1 auto;
      min-width: 0;
    }

    .bolopa-breadcrumb-node {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: #0f172a;
      min-width: 0;
    }

    .bolopa-breadcrumb-node-link {
      display: flex;
      align-items: center;
      gap: 8px;
      color: inherit;
      text-decoration: none;
      transition: color 0.2s ease;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .bolopa-breadcrumb-node-link:hover {
      color: #2563eb;
    }

    .bolopa-breadcrumb-node-link.is-active {
      color: #2563eb;
      font-weight: 600;
      cursor: default;
    }

    .bolopa-breadcrumb-node-link.is-active.is-standalone {
      color: #111827;
    }

    .bolopa-breadcrumb-node-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #ffffff;
      color: #2563eb;
      box-shadow: 0 1px 2px rgba(37,99,235,0.2);
      flex-shrink: 0;
    }

    .bolopa-breadcrumb-node-icon img {
      width: 18px;
      height: 18px;
    }

    .bolopa-breadcrumb-node-icon.is-symbol {
      font-size: 14px;
    }

    .bolopa-breadcrumb-node.is-active .bolopa-breadcrumb-node-icon {
      background: #ffffff;
      color: #2563eb;
      box-shadow: 0 1px 2px rgba(30,64,175,0.2);
    }

    .bolopa-breadcrumb-node-badge {
      background: #dbeafe;
      color: #1d4ed8;
      border-radius: 999px;
      padding: 0 6px;
      font-size: 11px;
      font-weight: 600;
    }

    .bolopa-breadcrumb-label {
      display: inline-block;
      max-width: clamp(120px, 30vw, 220px);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .bolopa-breadcrumb-arrow {
      color: #94a3b8;
      font-size: 12px;
    }

    /* Right - Info */
    .bolopa-header-vigazafarm-info {
      display: flex;
      align-items: center;
      gap: 20px;
      font-size: 14px;
      color: #555;
      flex-shrink: 0;
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

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    /* User */
    .bolopa-header-vigazafarm-user {
      display: flex;
      align-items: center;
      gap: 8px;
        flex-shrink: 0;
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

    @media (max-width: 1024px) {
      /* Tablet styles */
      .bolopa-header-vigazafarm-header { 
        left: 0;
        right: 0;
        padding: 10px 20px; 
      }
      .bolopa-header-vigazafarm-breadcrumb { gap: 5px; font-size: 13px; }
      .bolopa-header-vigazafarm-info { gap: 15px; }
      .bolopa-header-vigazafarm-user-info { display: none; }
      .bolopa-header-vigazafarm-user-avatar { width: 32px; height: 32px; }
      .bolopa-header-vigazafarm-mobile-menu { width: 320px; padding: 24px; }

      /* Adjust header position when sidebar is closed on tablet/mobile */
      .bolopa-sidebar-vigazafarm:not(.open) ~ .home-section .bolopa-header-vigazafarm-header {
        left: 78px;
      }

      /* Adjust header position when sidebar is open on tablet/mobile */
      .bolopa-sidebar-vigazafarm.open ~ .home-section .bolopa-header-vigazafarm-header {
        left: 250px;
      }
    }

    @media (max-width: 768px) {
      /* Mobile styles */
      .bolopa-header-vigazafarm-header { 
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1100;
        padding: 10px 16px; 
      }
      .bolopa-header-vigazafarm-info { display: none; }
      .bolopa-header-vigazafarm-hamburger { display: inline-flex; position: absolute; top: 10px; right: 16px; }
      .bolopa-header-vigazafarm-breadcrumb { gap: 4px; font-size: 13px; }
      .bolopa-header-vigazafarm-breadcrumb > *:not(:first-child):not(.bolopa-header-vigazafarm-active) { display: none; }
      .bolopa-header-vigazafarm-mobile-menu { width: 280px; padding: 20px; }
    }
  </style>

  <script>
    (function () {
      const headerRoot = document.querySelector('.bolopa-header-vigazafarm-header');
      if (!headerRoot) {
        return;
      }

      const rootStyle = document.documentElement;
      const dateEl = document.getElementById('bolopaHeaderDate');
      const clockEl = document.getElementById('bolopaHeaderClock');
      const mobileDateEl = document.getElementById('bolopaHeaderMobileDate');
      const userMenu = document.getElementById('bolopaHeaderUserMenu');
      const dropdownMenu = document.getElementById('bolopaHeaderDropdown');
      const hamburger = document.getElementById('bolopaHeaderHamburger');
      const mobileMenu = document.getElementById('bolopaHeaderMobileMenu');
      const mobileClose = document.getElementById('bolopaHeaderMobileClose');
      const pageContent = document.querySelector('.page-content');

      const syncHeaderOffset = () => {
        window.requestAnimationFrame(() => {
          const headerHeight = headerRoot.getBoundingClientRect().height;
          rootStyle.style.setProperty('--bolopa-header-height', `${headerHeight}px`);
        });
      };

      syncHeaderOffset();
      window.addEventListener('resize', syncHeaderOffset);
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(syncHeaderOffset);
        resizeObserver.observe(headerRoot);
      }

      const toggleHeaderShadow = () => {
        const pageScroll = window.scrollY || window.pageYOffset || 0;
        const contentScroll = pageContent ? pageContent.scrollTop : 0;
        const hasScrolled = pageScroll > 4 || contentScroll > 4;
        headerRoot.classList.toggle('bolopa-header-vigazafarm-header--shadow', hasScrolled);
      };

      toggleHeaderShadow();
      window.addEventListener('scroll', toggleHeaderShadow, { passive: true });
      if (pageContent) {
        pageContent.addEventListener('scroll', toggleHeaderShadow, { passive: true });
      }

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
