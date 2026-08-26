<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel Helpdesk</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #6777ef 0%, #4a5bd4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .helpdesk-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 32px 28px 36px;
            width: 100%;
            max-width: 440px;
        }

        .helpdesk-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .helpdesk-header .icon-wrap {
            width: 64px;
            height: 64px;
            background: #6777ef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .helpdesk-header .icon-wrap i {
            color: #fff;
            font-size: 26px;
        }

        .helpdesk-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 4px;
        }

        .helpdesk-header p {
            font-size: 0.9rem;
            color: #636e72;
            margin: 0;
        }

        .form-label-custom {
            font-weight: 600;
            font-size: 0.85rem;
            color: #2d3436;
            margin-bottom: 6px;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 14px;
            font-size: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s;
            color: #2d3436;
            background: #fafafa;
        }

        .form-control-custom:focus {
            border-color: #6777ef;
            background: #fff;
        }

        .form-control-custom.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-msg {
            color: #e74c3c;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .form-group-custom {
            margin-bottom: 20px;
        }

        /* Service selection cards */
        .service-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #2d3436;
            margin-bottom: 10px;
            display: block;
        }

        /*
         * 3 card sejajar horizontal — flexbox agar responsif di semua ukuran.
         * flex-wrap: nowrap memaksa semua card tetap satu baris.
         * Tiap card flex: 1 + min-width: 0 agar mengisi ruang secara merata
         * tanpa overflow, bahkan di layar 320px.
         */
        .service-grid {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            margin-bottom: 4px;
        }

        .service-card {
            position: relative;
            cursor: pointer;
            flex: 1;
            min-width: 0;
        }

        .service-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .service-card-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 14px 6px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: #fafafa;
            transition: all 0.2s ease;
            text-align: center;
            min-height: 86px;
            user-select: none;
            width: 100%;
        }

        .service-card-inner i {
            font-size: 20px;
            margin-bottom: 7px;
            color: #b2bec3;
            transition: color 0.2s;
        }

        .service-card-inner span {
            font-size: 0.78rem;
            font-weight: 600;
            color: #636e72;
            transition: color 0.2s;
            line-height: 1.3;
            word-break: break-word;
        }

        /* Selected state */
        .service-card input[type="radio"]:checked + .service-card-inner {
            border-color: #6777ef;
            background: #f0f2ff;
        }

        .service-card input[type="radio"]:checked + .service-card-inner i {
            color: #6777ef;
        }

        .service-card input[type="radio"]:checked + .service-card-inner span {
            color: #6777ef;
        }

        /* Hover state */
        .service-card-inner:hover {
            border-color: #a0aaef;
            background: #f7f8ff;
        }

        .service-card input[type="radio"]:checked + .service-card-inner:hover {
            border-color: #6777ef;
            background: #e8ebff;
        }

        .btn-start {
            width: 100%;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            background: #6777ef;
            color: #fff;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
            letter-spacing: 0.3px;
        }

        .btn-start:hover {
            background: #5a6fd6;
        }

        .btn-start:active {
            transform: scale(0.98);
        }

        .btn-start:disabled {
            background: #b2bec3;
            cursor: not-allowed;
        }

        .divider {
            border: none;
            border-top: 1px solid #f0f0f0;
            margin: 24px 0 22px;
        }
    </style>
</head>
<body>

<div class="helpdesk-card">

    {{-- Header --}}
    <div class="helpdesk-header">
        <div class="icon-wrap">
            <i class="fas fa-headset"></i>
        </div>
        <h1>Hotel Helpdesk</h1>
        <p>How can we help you today?</p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div style="background:#fdecea;border:1px solid #f5c6cb;border-radius:8px;padding:12px 14px;margin-bottom:20px;">
        <ul style="margin:0;padding-left:18px;color:#721c24;font-size:0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('guest.helpdesk.start') }}" method="POST" id="helpdesk-form">
        @csrf

        {{-- Guest Name --}}
        <div class="form-group-custom">
            <label class="form-label-custom" for="guest_name">Your Name</label>
            <input
                type="text"
                id="guest_name"
                name="guest_name"
                class="form-control-custom {{ $errors->has('guest_name') ? 'is-invalid' : '' }}"
                placeholder="Your Name"
                value="{{ old('guest_name') }}"
                autocomplete="name"
                required>
            @error('guest_name')
                <div class="invalid-msg">{{ $message }}</div>
            @enderror
        </div>

        {{-- Room Number --}}
        <div class="form-group-custom">
            <label class="form-label-custom" for="room_number">Room Number</label>
            <input
                type="text"
                id="room_number"
                name="room_number"
                class="form-control-custom {{ $errors->has('room_number') ? 'is-invalid' : '' }}"
                placeholder="Room Number"
                value="{{ old('room_number') }}"
                autocomplete="off"
                required>
            @error('room_number')
                <div class="invalid-msg">{{ $message }}</div>
            @enderror
        </div>

        <hr class="divider">

        {{-- Service Selection --}}
        <div class="form-group-custom">
            <label class="service-label">Choose Service</label>

            <div class="service-grid">
                @foreach($departments as $dept)
                @php
                    /*
                     * Dynamic icon mapping berdasarkan name/slug department.
                     * Tidak menggunakan ID — aman untuk semua hotel.
                     * Pencocokan dilakukan terhadap versi lowercase dari
                     * name dan slug, sehingga "Room Service" dan "room-service"
                     * keduanya cocok dengan kata kunci yang sama.
                     */
                    $haystack = strtolower($dept->name . ' ' . $dept->slug);

                    if (str_contains($haystack, 'reception') || str_contains($haystack, 'front office') || str_contains($haystack, 'receptionist')) {
                        $icon = 'fa-concierge-bell';
                    } elseif (str_contains($haystack, 'room') || str_contains($haystack, 'food') || str_contains($haystack, 'restaurant')) {
                        $icon = 'fa-utensils';
                    } elseif (str_contains($haystack, 'housekeeping') || str_contains($haystack, 'cleaning') || str_contains($haystack, 'laundry')) {
                        $icon = 'fa-broom';
                    } elseif (str_contains($haystack, 'engineering') || str_contains($haystack, 'maintenance') || str_contains($haystack, 'technician')) {
                        $icon = 'fa-tools';
                    } elseif (str_contains($haystack, 'security')) {
                        $icon = 'fa-shield-alt';
                    } elseif (str_contains($haystack, 'spa')) {
                        $icon = 'fa-spa';
                    } elseif (str_contains($haystack, 'transport') || str_contains($haystack, 'driver')) {
                        $icon = 'fa-car';
                    } elseif (str_contains($haystack, 'concierge')) {
                        $icon = 'fa-hotel';
                    } elseif (str_contains($haystack, 'reservation') || str_contains($haystack, 'booking')) {
                        $icon = 'fa-calendar-check';
                    } else {
                        $icon = 'fa-headset';
                    }
                @endphp
                <label class="service-card">
                    <input
                        type="radio"
                        name="department_id"
                        value="{{ $dept->id }}"
                        {{ old('department_id') == $dept->id ? 'checked' : '' }}
                        required>
                    <div class="service-card-inner">
                        <i class="fas {{ $icon }}"></i>
                        <span>{{ $dept->name }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            @error('department_id')
                <div class="invalid-msg">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-start" id="btn-start">
            <i class="fas fa-comments"></i>&nbsp; Start Chat
        </button>

    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script>
    // Visual feedback: keep selected card highlighted on page load (old input restore)
    document.querySelectorAll('.service-card input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // No extra JS needed — CSS :checked handles visual state
        });
    });
</script>

</body>
</html>
