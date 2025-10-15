<footer class="bolopa-footer">
  <div class="bolopa-footer-content">
    <span class="bolopa-desktop">
      <img src="{{ asset('bolopa/img/icon/si--copyright-alt-duotone.svg') }}" width="10" height="10" style="margin-right: 4px; filter: invert(95%) sepia(85%) saturate(2%) hue-rotate(150deg) brightness(105%) contrast(101%); vertical-align: middle;">
      2025 Vigaza Farm | Sistem Operasional Ternak Burung Puyuh
      <img src="{{ asset('bolopa/img/icon/game-icons--liberty-wing.svg') }}" width="15" height="15" style="margin-left: 4px; filter: invert(95%) sepia(85%) saturate(2%) hue-rotate(150deg) brightness(105%) contrast(101%);">
    </span>
    <span class="bolopa-mobile">
      <img src="{{ asset('bolopa/img/icon/si--copyright-alt-duotone.svg') }}" width="10" height="10" style="margin-right: 4px; filter: invert(95%) sepia(85%) saturate(2%) hue-rotate(150deg) brightness(105%) contrast(101%); vertical-align: middle;">
      2025 Vigaza Farm
      <img src="{{ asset('bolopa/img/icon/game-icons--liberty-wing.svg') }}" width="15" height="15" style="margin-left: 4px; filter: invert(95%) sepia(85%) saturate(2%) hue-rotate(150deg) brightness(105%) contrast(101%);">
    </span>
  </div>
</footer>

<style>
  .bolopa-footer {
    background: #1d1b31;
    color: #f1f5f9;
    padding: 20px 5px 12px 5px;
    font-size: 14px;
    /* keep footer visible at bottom of the home-section */
    position: sticky;
    bottom: 0;
    z-index: 50;
    width: 100%;
  }

  .bolopa-footer-content {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    text-align: center;
  }

  .bolopa-footer a {
    color: #60a5fa;
    text-decoration: none;
    margin: 0 8px;
  }

  .bolopa-footer a:hover {
    text-decoration: underline;
  }

  /* Responsive design */
  .bolopa-desktop {
    display: inline;
  }

  .bolopa-mobile {
    display: none;
  }

  /* Mobile */
  @media (max-width: 767px) {
    .bolopa-desktop {
      display: none;
    }

    .bolopa-mobile {
      display: inline;
    }
  }
</style>