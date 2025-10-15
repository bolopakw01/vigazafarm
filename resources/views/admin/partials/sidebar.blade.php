<div class="bolopa-sidebar-vigazafarm">
    <div class="logo-details">
        <img src="{{ asset('bolopa/img/icon/vigazafarm logo.svg') }}" alt="Vigazafarm" class="logo-img" />
        <div class="logo_name">Vigazafarm</div>
        <img id="btn" src="{{ asset('bolopa/img/icon/line-md--menu-fold-right.svg') }}" alt="menu" />
    </div>
  @php
    // Determine which sidebar group should be active based on current route
    $currentRoute = request()->route() ? request()->route()->getName() : null;
    $activeGroup = 'operasional';
    if ($currentRoute) {
      if (request()->routeIs('admin.penetasan*') || request()->routeIs('admin.pembesaran*') || request()->routeIs('admin.produksi*')) {
        $activeGroup = 'operasional';
      } elseif (request()->routeIs('admin.kandang*') || request()->routeIs('admin.karyawan*')) {
        $activeGroup = 'master';
      }
    }
  @endphp
  <ul class="nav-list" data-active="{{ $activeGroup }}">
        <!-- mini horizontal menu: Operational & Master (Owner Only) -->
        @if(auth()->user()->peran === 'owner')
        <li class="mini-menus">
            <a href="#" class="mini-menu active" title="Operasional" data-target="operasional">
                <img src="{{ asset('bolopa/img/icon/line-md--home-md.svg') }}" alt="Operasional" />
                <span class="mini-label">Operasional</span>
            </a>
            <a href="#" class="mini-menu" title="Master" data-target="master">
                <img src="{{ asset('bolopa/img/icon/line-md--monitor-screenshot.svg') }}" alt="Master" />
                <span class="mini-label">Master</span>
            </a>
        </li>
        @endif
        <li>
            <a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/ic--outline-dashboard.svg') }}" alt="Dashboard" class="menu-icon" />
                <span class="links_name">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>
        @if(auth()->user()->peran === 'owner')
        <li class="section-label">
            <div class="section-decor">
                <span class="section-text">Operasional</span>
                <span class="section-line" aria-hidden="true"></span>
            </div>
        </li>
        @endif
        <!-- operational menu items -->
    <li data-group="operasional">
      <a href="{{ route('admin.penetasan') }}" class="menu-link {{ request()->routeIs('admin.penetasan*') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/game-icons--nest-eggs.svg') }}" alt="Penetasan" class="menu-icon" />
                <span class="links_name">Penetasan</span>
            </a>
            <span class="tooltip">Penetasan</span>
        </li>
        <li data-group="operasional">
      <a href="{{ route('admin.pembesaran') }}" class="menu-link {{ request()->routeIs('admin.pembesaran*') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/game-icons--nest-birds.svg') }}" alt="Pembesaran" class="menu-icon" />
                <span class="links_name">Pembesaran</span>
            </a>
            <span class="tooltip">Pembesaran</span>
        </li>
        <li data-group="operasional">
      <a href="{{ route('admin.produksi') }}" class="menu-link {{ request()->routeIs('admin.produksi*') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/streamline-sharp--archive-box-solid.svg') }}" alt="Produksi" class="menu-icon" />
                <span class="links_name">Produksi</span>
            </a>
            <span class="tooltip">Produksi</span>
        </li>
        @if(auth()->user()->peran === 'owner')
        <!-- master menu items -->
    <li data-group="master">
      <a href="{{ route('admin.kandang') }}" class="menu-link {{ request()->routeIs('admin.kandang*') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/mdi--home-city-outline.svg') }}" alt="Kandang" class="menu-icon" />
                <span class="links_name">Kandang</span>
            </a>
            <span class="tooltip">Kandang</span>
        </li>
        <li data-group="master">
      <a href="{{ route('admin.karyawan') }}" class="menu-link {{ request()->routeIs('admin.karyawan*') ? 'active' : '' }}">
                <img src="{{ asset('bolopa/img/icon/fluent--person-note-20-filled.svg') }}" alt="Karyawan" class="menu-icon" />
                <span class="links_name">Karyawan</span>
            </a>
            <span class="tooltip">Karyawan</span>
        </li>
        @endif
        <!-- profile/logout -->
        <li class="profile">
            <div class="profile-details">
                <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)) }}</div>
                <div class="name_job">
                    <div class="name">{{ explode(' ', auth()->user()->nama)[0] ?? 'Admin' }}</div>
                    <div class="job">{{ auth()->user()->peran === 'owner' ? 'Owner' : 'Operator' }}</div>
                </div>
            </div>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
            <a id="log_out" href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                <img src="{{ asset('bolopa/img/icon/line-md--log-out.svg') }}" alt="logout" class="logout-icon" />
            </a>
        </li>
    </ul>
</div>

<style>
/* Google Font Link */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

/* Scoped sidebar styles to prevent conflicts with Bootstrap */
.bolopa-sidebar-vigazafarm * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins" , sans-serif;
}
.bolopa-sidebar-vigazafarm{
  position: fixed;
  left: 0;
  top: 0;
  height: 100%;
  width: 78px;
  background: #11101D;
  padding: 6px 14px;
  z-index: 99;
  transition: all 0.5s ease;
}
.bolopa-sidebar-vigazafarm.open{
  width: 250px;
}
.bolopa-sidebar-vigazafarm .logo-details{
  height: 60px;
  display: flex;
  align-items: center;
  position: relative;
}
.bolopa-sidebar-vigazafarm .logo-details::after{
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: -8px; /* place slightly below the logo area */
  height: 2px;
  /* green-tinted soft gradient for flowing highlight */
  background: linear-gradient(90deg, rgba(72,187,120,0.03) 0%, rgba(72,187,120,0.28) 45%, rgba(72,187,120,0.03) 80%);
  background-size: 400% 100%;
  background-position: 100% 0%;
  pointer-events: none;
  transition: opacity 0.3s ease;
  opacity: 1;
  /* subtle green glow bloom */
  box-shadow: 0 0 10px rgba(72,187,120,0.06);
}

@keyframes glowLine {
  0% { background-position: 400% 0%; }
  100% { background-position: -100% 0%; }
}

/* always animate the glow (both hidden and shown states) for continuous effect */
.bolopa-sidebar-vigazafarm .logo-details::after{
  animation: glowLine 6s linear infinite;
}

/* Respect prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  .bolopa-sidebar-vigazafarm .logo-details::after{ animation: none; }
}
.bolopa-sidebar-vigazafarm .logo-details .logo-img{
  height: 36px;
  width: auto;
  margin-right: 8px;
  opacity: 0;
  transition: all 0.5s ease;
}
.bolopa-sidebar-vigazafarm .logo-details .logo_name{
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  opacity: 0;
  transition: all 0.5s ease;
}
.bolopa-sidebar-vigazafarm.open .logo-details .logo_name,
.bolopa-sidebar-vigazafarm.open .logo-details .logo-img{
  opacity: 1;
}
.bolopa-sidebar-vigazafarm .logo-details #btn{
  position: absolute;
  top: 50%;
  right: 6px;
  transform: translateY(-50%);
  width: 28px;
  height: auto;
  display: block;
  object-fit: contain;
  margin-right: 4px;
  cursor: pointer;
  transition: transform 0.2s ease, opacity 0.3s ease;
  filter: invert(100%) sepia(100%) saturate(1%) hue-rotate(51deg) brightness(101%) contrast(102%);
}
.bolopa-sidebar-vigazafarm i{
  color: #fff;
  height: 60px;
  min-width: 50px;
  font-size: 28px;
  text-align: center;
  line-height: 60px;
}
/* dashboard icon removed */
.bolopa-sidebar-vigazafarm .nav-list{
  margin-top: 20px;
  height: 100%;
}

/* mini horizontal menus just under the logo separator */
.bolopa-sidebar-vigazafarm li.mini-menus{
  width: 100%;
  display: flex;
  justify-content: center; /* center the group horizontally */
  align-items: center;
  gap: 6px; /* make icons closer together */
  padding: 4px 0 8px; /* vertical spacing so icons sit under the separator */
}
.bolopa-sidebar-vigazafarm li.mini-menus .mini-menu{
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  background: transparent;
  text-decoration: none;
  transition: background 160ms ease, transform 160ms ease;
}
.bolopa-sidebar-vigazafarm li.mini-menus .mini-menu img{
  height: 16px;
  width: auto;
  display: block;
  filter: invert(100%) sepia(100%) saturate(1%) hue-rotate(51deg) brightness(101%) contrast(102%);
}

/* when sidebar is closed: hide labels and ensure icons are perfectly centered */
.bolopa-sidebar-vigazafarm:not(.open) li.mini-menus .mini-menu{
  width: 32px;
  padding: 0;
}
.bolopa-sidebar-vigazafarm:not(.open) li.mini-menus .mini-menu .mini-label{
  display: none;
}
.bolopa-sidebar-vigazafarm:not(.open) li.mini-menus .mini-menu img{
  margin: 0 auto;
}
.bolopa-sidebar-vigazafarm li.mini-menus .mini-menu .mini-label{
  margin-left: 8px;
  color: #fff;
  font-size: 14px;
  white-space: nowrap;
  opacity: 0;
  max-width: 0;
  overflow: hidden;
  transition: opacity 0.25s ease, max-width 0.25s ease;
}

/* hide menu items by group - show only those matching ul[data-active] */
.nav-list > li[data-group]{
  display: none;
}
.nav-list[data-active="operasional"] > li[data-group="operasional"],
.nav-list[data-active="master"] > li[data-group="master"],
.nav-list[data-active="all"] > li[data-group]{
  display: block;
}

/* active mini-menu visual */
.mini-menu.active{
  background: rgba(72,187,120,0.12);
  border-radius: 6px;
}

.bolopa-sidebar-vigazafarm.open li.mini-menus .mini-menu{
  width: auto;
  padding: 4px 6px; /* reduce horizontal padding so icon and text sit closer */
}
.bolopa-sidebar-vigazafarm.open li.mini-menus .mini-menu .mini-label{
  margin-left: 4px; /* tighter gap between icon and text */
  opacity: 1;
  max-width: 160px;
}

/* hover/active feedback for mini-menu */
.bolopa-sidebar-vigazafarm li.mini-menus .mini-menu:hover{
  background: rgba(255,255,255,0.04);
  transform: scale(1.08);
}
.bolopa-sidebar-vigazafarm li.mini-menus .mini-menu:active{
  transform: scale(0.98);
}

/* keep animations off for users who prefer reduced motion */
@media (prefers-reduced-motion: reduce) {
  .bolopa-sidebar-vigazafarm li.mini-menus .mini-menu,
  .bolopa-sidebar-vigazafarm li.mini-menus .mini-menu img{
    transition: none;
    transform: none;
  }
}

/* small nudges to correct visual centering */

.bolopa-sidebar-vigazafarm li{
  position: relative;
  margin: 8px 0;
  list-style: none;
}
.bolopa-sidebar-vigazafarm li .tooltip{
  position: absolute;
  top: -20px;
  left: calc(100% + 15px);
  z-index: 3;
  background: #fff;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 15px;
  font-weight: 400;
  opacity: 0;
  white-space: nowrap;
  pointer-events: none;
  transition: 0s;
}
.bolopa-sidebar-vigazafarm li:hover .tooltip{
  opacity: 1;
  pointer-events: auto;
  transition: all 0.4s ease;
  top: 50%;
  transform: translateY(-50%);
}
.bolopa-sidebar-vigazafarm.open li .tooltip{
  display: none;
}
/* search-related styles removed */
.bolopa-sidebar-vigazafarm li a{
  display: flex;
  height: 100%;
  width: 100%;
  border-radius: 12px;
  align-items: center;
  text-decoration: none;
  transition: all 0.4s ease;
  background: #11101D;
}
.bolopa-sidebar-vigazafarm .menu-link{
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  transition: background 160ms ease, transform 160ms ease;
}
.bolopa-sidebar-vigazafarm .menu-icon{
  height: 24px;
  width: auto;
  display: block;
  filter: invert(100%) sepia(100%) saturate(1%) hue-rotate(51deg) brightness(101%) contrast(102%);
  transition: transform 160ms ease, filter 160ms ease;
}

/* section label / decorative line under Dashboard */
.bolopa-sidebar-vigazafarm li.section-label{
  display: block;
  padding: 6px 12px;
}
.bolopa-sidebar-vigazafarm li.section-label .section-decor{
  display: flex;
  align-items: center;
  gap: 8px;
}
.bolopa-sidebar-vigazafarm li.section-label .section-text{
  color: rgba(255,255,255,0.72); /* muted white */
  font-size: 13px;
  opacity: 0.92;
  font-weight: 500;
}
.bolopa-sidebar-vigazafarm li.section-label .section-line{
  flex: 1;
  height: 2px;
  background: rgba(255,255,255,0.08);
  border-radius: 2px;
}

/* when sidebar is closed: hide text and show only the line centered */
.bolopa-sidebar-vigazafarm:not(.open) li.section-label .section-text{
  display: none;
}
.bolopa-sidebar-vigazafarm:not(.open) li.section-label .section-decor{
  justify-content: center;
}
.bolopa-sidebar-vigazafarm:not(.open) li.section-label .section-line{
  width: 24px;
  flex: none;
  height: 2px;
  margin: 0 auto;
}

/* hover feedback for menu-link: color change only (no scale) */
.bolopa-sidebar-vigazafarm .menu-link:hover{
  background: rgba(255,255,255,0.03);
}
.bolopa-sidebar-vigazafarm .menu-link:hover .menu-icon{
  /* icons are white by default (via base filter); on hover make them appear dark */
  filter: invert(0%) sepia(0%) saturate(100%) brightness(0.9) contrast(1);
}
.bolopa-sidebar-vigazafarm .menu-link:active .menu-icon{
  filter: invert(0%) sepia(0%) saturate(100%) brightness(0.85) contrast(1);
}

/* active menu link styling */
.bolopa-sidebar-vigazafarm .menu-link.active{
  background: rgba(72, 187, 120, 0);
  border-radius: 12px;
}
.bolopa-sidebar-vigazafarm .menu-link.active .links_name{
  color: #48BB78;
}
.bolopa-sidebar-vigazafarm .menu-link.active .menu-icon{
  filter: invert(36%) sepia(73%) saturate(476%) hue-rotate(81deg) brightness(95%) contrast(92%);
}

.bolopa-sidebar-vigazafarm .menu-link,
  .bolopa-sidebar-vigazafarm .menu-icon{
    transition: none;
    transform: none;
  }
}
.bolopa-sidebar-vigazafarm li a:hover{
  background: #FFF;
}
.bolopa-sidebar-vigazafarm li a .links_name{
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: 0.4s;
}
.bolopa-sidebar-vigazafarm.open li a .links_name{
  opacity: 1;
  pointer-events: auto;
}
.bolopa-sidebar-vigazafarm li a:hover .links_name,
.bolopa-sidebar-vigazafarm li a:hover i{
  transition: all 0.5s ease;
  color: #11101D;
}

/* override for .menu-link hover: prefer color/tint change rather than dark-on-white inversion */
.bolopa-sidebar-vigazafarm .menu-link:hover .links_name{
  color: #48BB78; /* green on hover */
  transition: color 260ms ease;
}
.bolopa-sidebar-vigazafarm .menu-link:hover .menu-icon{
  /* tint SVG icon to green (approximate via CSS filter) */
  filter: invert(36%) sepia(73%) saturate(476%) hue-rotate(81deg) brightness(95%) contrast(92%);
  transition: filter 260ms ease;
}
.bolopa-sidebar-vigazafarm .menu-link:active .menu-icon{
  /* slightly darker green when active */
  filter: invert(30%) sepia(75%) saturate(520%) hue-rotate(82deg) brightness(88%) contrast(90%);
}
.bolopa-sidebar-vigazafarm li i{
  height: 50px;
  line-height: 50px;
  font-size: 18px;
  border-radius: 12px;
}
.bolopa-sidebar-vigazafarm li.profile{
  position: fixed;
  height: 60px;
  width: 78px;
  left: 0;
  bottom: -15px;
  padding: 5px 14px;
  background: #1d1b31;
  transition: all 0.5s ease;
  overflow: hidden;
}
.bolopa-sidebar-vigazafarm.open li.profile{
  width: 250px;
}
.bolopa-sidebar-vigazafarm li .profile-details{
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
}
.bolopa-sidebar-vigazafarm li img{
  height: 45px;
  width: 45px;
  object-fit: cover;
  border-radius: 6px;
  margin-right: 10px;
}

/* text-based circular avatar for profile */
.bolopa-sidebar-vigazafarm .profile .profile-avatar{
  height: 45px;
  width: 45px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(180deg,#2b2a3d,#1f1e2b);
  color: #fff;
  font-weight: 600;
  margin-right: 8px;
  font-size: 18px;
}
.bolopa-sidebar-vigazafarm li.profile .name,
.bolopa-sidebar-vigazafarm li.profile .job{
  font-size: 15px;
  font-weight: 400;
  color: #fff;
  white-space: nowrap;
}
.bolopa-sidebar-vigazafarm li.profile .job{
  font-size: 12px;
}
.bolopa-sidebar-vigazafarm .profile #log_out{
  position: absolute;
  top: 50%;
  right: -5px;
  transform: translateY(-50%);
  background: #1d1b31;
  width: 100%;
  height: 60px;
  border-radius: 0px;
  transition: all 0.5s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}
.bolopa-sidebar-vigazafarm .logout-icon{
  height: 30px;
  width: auto;
  display: inline-block;
  filter: invert(100%) sepia(100%) saturate(1%) hue-rotate(51deg) brightness(101%) contrast(102%);
  transition: transform 0.25s ease;
  transform-origin: center;
}
.bolopa-sidebar-vigazafarm.open .profile #log_out{
  width: 50px;
  background: none;
}

.bolopa-sidebar-vigazafarm.open .logout-icon{
  transform: scaleX(-1);
}
</style>

<script>
let sidebar = document.querySelector(".bolopa-sidebar-vigazafarm");
let closeBtn = document.querySelector("#btn");
let navList = document.querySelector('.nav-list');
let miniButtons = document.querySelectorAll('.mini-menu');
let sectionText = document.querySelector('.section-text');

const menuIcon = '{{ asset("bolopa/img/icon/line-md--menu-fold-right.svg") }}';
const menuIconAlt = '{{ asset("bolopa/img/icon/line-md--menu-fold-left.svg") }}';

// Function to save sidebar state
function saveSidebarState() {
  const isOpen = sidebar.classList.contains("open");
  localStorage.setItem('sidebarOpen', isOpen);
}

// Function to save active menu state
function saveActiveMenuState(activeMenu) {
  localStorage.setItem('activeMenu', activeMenu);
}

// Function to load sidebar state
function loadSidebarState() {
  const isOpen = localStorage.getItem('sidebarOpen') === 'true';
  if (isOpen) {
    sidebar.classList.add("open");
  }
  menuBtnChange();
}

// Function to load active menu state and set based on current route
function loadActiveMenuState() {
  // Get current route name from PHP
  const currentRoute = '{{ request()->route()->getName() }}';

  // Determine which menu should be active based on route
  let activeMenu = 'operasional'; // default

  if (currentRoute === 'admin.kandang' || currentRoute === 'admin.karyawan') {
    activeMenu = 'master';
  } else if (currentRoute === 'admin.penetasan' || currentRoute === 'admin.pembesaran' || currentRoute === 'admin.produksi') {
    activeMenu = 'operasional';
  } else {
    // Load from localStorage if available, otherwise use default
    activeMenu = localStorage.getItem('activeMenu') || 'operasional';
  }

  // Set active menu
  setActiveMenu(activeMenu);
}

// Function to set active menu
function setActiveMenu(target) {
  // update visual active mini button
  miniButtons.forEach(b => b.classList.remove('active'));
  const targetBtn = document.querySelector(`.mini-menu[data-target="${target}"]`);
  if (targetBtn) {
    targetBtn.classList.add('active');
  }

  // set active group on nav-list
  if (navList) navList.setAttribute('data-active', target);

  // update section label text if present
  if (sectionText) {
    sectionText.textContent = (target === 'operasional') ? 'Operasional' : 'Master';
  }

  // Save state
  saveActiveMenuState(target);
}

// Function to update home section based on sidebar state
function updateHomeSection() {
  const homeSection = document.querySelector('.home-section');
  if (sidebar.classList.contains("open")) {
    homeSection.style.left = '250px';
    homeSection.style.width = 'calc(100% - 250px)';
  } else {
    homeSection.style.left = '78px';
    homeSection.style.width = 'calc(100% - 78px)';
  }
}

closeBtn.addEventListener("click", ()=>{
  sidebar.classList.toggle("open");
  menuBtnChange();
  updateHomeSection(); // Update home section styling
  saveSidebarState(); // Save state when toggled
});

// following are the code to change sidebar button(optional)
function menuBtnChange() {
 if(sidebar.classList.contains("open")){
   closeBtn.src = menuIconAlt;
 } else {
   closeBtn.src = menuIcon;
 }
}

// initialize mini-menu handlers for switching groups
miniButtons.forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    const target = btn.getAttribute('data-target');
    if(!target) return;
    setActiveMenu(target);
  });
});

// Initialize states on page load
document.addEventListener('DOMContentLoaded', function() {
  loadSidebarState();
  loadActiveMenuState();
  updateHomeSection(); // Update home section styling based on loaded state
});
</script>
