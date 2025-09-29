<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>{{ config('app.name', 'Laravel') }} - Login</title>
	<link rel="icon" type="image/png" href="{{ asset('bolopa/img/icon.png') }}">

	<!-- SweetAlert2 CDN -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	@verbatim
	<style>
		/* Minimal font-face placeholder - replace with real font files if desired */
		@font-face {
			font-family: 'Poppins';
			src: url('{{ asset("bolopa/font/Poppins-Regular.ttf") }}') format('truetype');
			font-weight: 400;
			font-display: swap;
		}

		::selection { background: #2D2F36; }
		::-webkit-selection { background: #2D2F36; }
		::-moz-selection { background: #2D2F36; }

		body {
			background: #e2e2e5;
			font-family: 'Poppins', sans-serif;
			margin: 0;
			padding: 20px;
		}

		.bolopa-page {
			background: #e2e2e5;
			display: flex;
			flex-direction: column;
			height: calc(100% - 40px);
			position: absolute;
			place-content: center;
			width: calc(100% - 40px);
		}

		@media (max-width: 767px) {
			.bolopa-page { height: auto; margin-bottom: 20px; padding-bottom: 20px; }
		}

		.bolopa-container { display: flex; height: 320px; margin: 0 auto; width: 640px; }
		@media (max-width: 767px) { .bolopa-container { flex-direction: column; height: 630px; width: 320px; } }

		.bolopa-left { background: white; height: calc(100% - 40px); top: 20px; position: relative; width: 50%; }
		@media (max-width: 767px) { .bolopa-left { height: 100%; left: 20px; width: calc(100% - 40px); max-height: 270px; } }

		.bolopa-login { font-size: 50px; font-weight: 900; margin: 20px 40px 10px; display: flex; align-items: center; justify-content: center; }
		.bolopa-login img { max-width: 300px; max-height: 136px; width: auto; height: auto; }
		.bolopa-eula { color: #999; font-size: 20px; line-height: 1.5; margin: 20px 40px; text-align: center; }
		.bolopa-admin-login { color: #000; font-size: 16px; font-weight: bold; text-align: center; margin: 10px 40px; animation: glow 1s ease-in-out infinite alternate; }
		@keyframes glow { from { opacity: 0.3; color: #666; } to { opacity: 1; color: #333; } }

		.bolopa-right { background: #474A59; box-shadow: 0px 0px 40px 16px rgba(0,0,0,0.22); color: #F1F1F2; position: relative; width: 50%; }
		@media (max-width: 767px) { .bolopa-right { flex-shrink: 0; height: 100%; width: 100%; max-height: 350px; } }

		svg { position: absolute; width: 320px; }
		path { fill: none; stroke: url(#linearGradient); stroke-width: 4; stroke-dasharray: 240 1386; }

		.bolopa-form { margin: 40px; position: absolute; }
		label { color:  #c2c2c5; display: block; font-size: 14px; height: 16px; margin-top: 20px; margin-bottom: 5px; }

		input { background: transparent; border: 0; color: #f2f2f2; font-size: 20px; height: 30px; line-height: 30px; outline: none !important; width: 100%; transition: background-color 300ms ease, color 300ms ease; }
		input:focus { background: white; color: #333; }
		input::-moz-focus-inner { border: 0; }

		/* Hide browser's default password toggle icons */
		input[type="password"]::-webkit-password-toggle-button {
			display: none;
		}
		input[type="password"]::-ms-reveal {
			display: none;
		}

		.bolopa-password-container { position: relative; width: 100%; }
		.bolopa-password-toggle { position: absolute; right: 0; top: 50%; transform: translateY(-50%); cursor: pointer; width: 20px; height: 20px; opacity: 0.7; transition: opacity 300ms ease; }
		.bolopa-password-toggle:hover { opacity: 1; }
		.bolopa-password-toggle img { width: 100%; height: 100%; filter: invert(22%) sepia(87%) saturate(7468%) hue-rotate(333deg) brightness(100%) contrast(110%); }

		#submit { background: #474a59; color: #ffffff; margin: 31px auto 0 auto; padding: 16px 20px; border: none; border-radius: 25px; font-size: 16px; font-weight: bold; transition: all 300ms ease; cursor: pointer; box-shadow: 0 4px 15px rgba(71, 74, 89, 0.3); text-align: center; width: 95%; display: flex; align-items: center; justify-content: center; gap: 8px; line-height: normal; height: auto; box-sizing: border-box; }
		.bolopa-button-icon { width: 18px; height: 18px; filter: brightness(0) invert(1); }
		#submit:focus { outline: none; background: #474a59; box-shadow: 0 0 0 3px rgba(71, 74, 89, 0.3); }
		#submit:active { transform: translateY(0px); background: linear-gradient(45deg, #ff00ff, #ff0000); box-shadow: 0 2px 10px rgba(255, 0, 255, 0.3); }
	</style>
	@endverbatim

</head>
<body>

	<div class="bolopa-page">
		<div class="bolopa-container">
			<div class="bolopa-left">
		<div class="bolopa-login"><img src="{{ asset('bolopa/img/vigazafarm.gif') }}" alt="Login" /></div>
				<div class="bolopa-eula">System Management</div>
				<div class="bolopa-admin-login">Admin Login</div>
			</div>
			<div class="bolopa-right">
				<svg viewBox="0 0 320 300">
					<defs>
						<linearGradient id="linearGradient" x1="13" y1="193.49992" x2="307" y2="193.49992" gradientUnits="userSpaceOnUse">
							<stop style="stop-color:#ff00ff;" offset="0" />
							<stop style="stop-color:#ff0000;" offset="1" />
						</linearGradient>
					</defs>
					<path d="m 40,120.00016 239.99984,-3.2e-4 c 0,0 24.99263,0.79932 25.00016,35.00016
									 0.008,34.20084 -25.00016,35 -25.00016,35 h -239.99984
									 c 0,-0.0205 -25,4.01348 -25,38.5
									 0,34.48652 25,38.5 25,38.5
									 h 215
									 c 0,0 20,-0.99604 20,-25
									 0,-24.00396 -20,-25 -20,-25
									 h -190
									 c 0,0 -20,1.71033 -20,25
									 0,24.00396 20,25 20,25
									 h 168.57143" />
				</svg>

			<form method="POST" action="{{ route('mimin.store') }}" class="bolopa-form">
					@csrf

					<label for="nama_pengguna">Nama Pengguna</label>
					<input type="text" id="nama_pengguna" name="nama_pengguna" value="{{ old('nama_pengguna') }}" required autofocus>
					@if ($errors->has('nama_pengguna'))
						<div class="error" style="color:#ffdddd; margin-top:6px">{{ $errors->first('nama_pengguna') }}</div>
					@endif

					<label for="kata_sandi">Kata Sandi</label>
					<div class="bolopa-password-container">
						<input type="password" id="kata_sandi" name="kata_sandi" required>
						<div class="bolopa-password-toggle" id="togglePassword">
							  <img src="{{ asset('bolopa/img/icon/el--eye-close.svg') }}" alt="Toggle password visibility" id="eyeIcon">
						</div>
					</div>
					@if ($errors->has('password'))
						<div class="error" style="color:#ffdddd; margin-top:6px">{{ $errors->first('password') }}</div>
					@endif

                    

					<button type="submit" id="submit">
						<img src="{{ asset('bolopa/img/icon/mingcute--safe-lock-fill.svg') }}" alt="Lock icon" class="bolopa-button-icon">
						Secure Login
					</button>

				</form>

			</div>
		</div>
	</div>

	<!-- Anime.js CDN -->
	@verbatim
	<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
	<script>
		var current = null;

		// Animate on focus
		document.querySelector('#nama_pengguna').addEventListener('focus', function() {
			if (current) current.pause();
			current = anime({
				targets: 'path',
				strokeDashoffset: { value: 0, duration: 400, easing: 'easeOutQuart' },
				strokeDasharray: { value: '240 1386', duration: 400, easing: 'easeOutQuart' }
			});
		});

		document.querySelector('#kata_sandi').addEventListener('focus', function() {
			if (current) current.pause();
			current = anime({
				targets: 'path',
				strokeDashoffset: { value: -336, duration: 400, easing: 'easeOutQuart' },
				strokeDasharray: { value: '240 1386', duration: 400, easing: 'easeOutQuart' }
			});
		});

		document.querySelector('#submit').addEventListener('focus', function() {
			if (current) current.pause();
			current = anime({
				targets: 'path',
				strokeDashoffset: { value: -730, duration: 400, easing: 'easeOutQuart' },
				strokeDasharray: { value: '530 1386', duration: 400, easing: 'easeOutQuart' }
			});
		});

		// Password toggle
		document.querySelector('#togglePassword').addEventListener('click', function() {
			const passwordInput = document.querySelector('#kata_sandi');
			const eyeIcon = document.querySelector('#eyeIcon');
			if (passwordInput.type === 'password') {
				passwordInput.type = 'text';
				eyeIcon.src = './bolopa/img/icon/el--eye-open.svg';
				eyeIcon.alt = 'Hide password';
			} else {
				passwordInput.type = 'password';
				eyeIcon.src = './bolopa/img/icon/el--eye-close.svg';
				eyeIcon.alt = 'Show password';
			}
		});

		// AJAX form submission with SweetAlert
		document.querySelector('form').addEventListener('submit', function(e) {
			e.preventDefault();
			const form = this;
			const formData = new FormData(form);

			fetch(form.action, {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					Swal.fire({
						title: 'Berhasil!',
						text: data.message,
						icon: 'success',
						timer: 3000,
						timerProgressBar: true,
						showConfirmButton: true,
						confirmButtonText: 'OK'
					}).then(() => {
						window.location.replace(data.redirect);
					});
				} else {
					Swal.fire({
						title: 'Gagal!',
						text: data.message,
						icon: 'error',
						confirmButtonText: 'OK'
					});
				}
			})
			.catch(error => {
				console.error('Error:', error);
				Swal.fire({
					title: 'Error',
					text: 'Terjadi kesalahan saat login.',
					icon: 'error',
					confirmButtonText: 'OK'
				});
			});
		});
	</script>
	@endverbatim

</body>
</html>

