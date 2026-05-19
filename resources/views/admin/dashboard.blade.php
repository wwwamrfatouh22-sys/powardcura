<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    :root {
        --primary: #114a9f;
        --primary-dark: #0a3d86;
        --text: #2f3a45;
        --muted: #6b7280;
        --danger: #ff3b3f;
        --shadow: 0 18px 36px rgba(34, 52, 84, 0.18);
        --radius-xl: 28px;
        --radius-lg: 22px;
        --sidebar-width: 290px;
        --soft-bg: #f7f7f7;
        --card-bg: #f8f8f8;
        --border: #d8dde6;
        --approved: #4ecb71;
        --approved-bg: #ebfbf0;
        --rejected: #ff5a52;
        --rejected-bg: #ffeceb;
        --pending: #d6a300;
        --pending-bg: #fff7d9;
    }

    * {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        margin: 0;
        font-family: 'Inter', Arial, sans-serif;
        color: var(--text);
        background: radial-gradient(circle at 72% 52%, rgba(49, 157, 255, 0.95) 0%, rgba(110, 189, 255, 0.72) 20%, rgba(196, 224, 248, 0.62) 42%, rgba(235, 240, 245, 0.95) 70%, #f2f2f2 100%);
        min-height: 100vh;
        overflow-x: hidden;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    button,
    input,
    textarea {
        font-family: inherit;
    }

    /* ── PAGE LOADER ── */
    #page-loader {
        position: fixed;
        inset: 0;
        background: #0a3d86;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 18px;
        transition: opacity .5s ease, visibility .5s ease;
    }

    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .loader-ring {
        width: 52px;
        height: 52px;
        border: 4px solid rgba(255, 255, 255, 0.15);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spinRing 0.9s linear infinite;
    }

    @keyframes spinRing {
        to {
            transform: rotate(360deg);
        }
    }

    .loader-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 2px;
        animation: loaderPulse 1.2s ease-in-out infinite;
    }

    @keyframes loaderPulse {

        0%,
        100% {
            opacity: .4
        }

        50% {
            opacity: 1
        }
    }

    /* ── LAYOUT ── */
    .layout {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: var(--sidebar-width);
        padding: 22px 18px;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1000;
        transform: translateX(-112%);
        transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-panel {
        height: 100%;
        background: rgba(247, 247, 247, 0.97);
        border-radius: 34px;
        box-shadow: 0 16px 35px rgba(0, 0, 0, 0.14);
        display: flex;
        flex-direction: column;
        padding: 28px 18px 18px;
        backdrop-filter: blur(2px);
    }

    .sidebar-top-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 26px;
        min-height: 78px;
    }

    .sidebar-top-logo img {
        max-width: 145px;
        height: auto;
        object-fit: contain;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .menu li a,
    .menu li .menu-static {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: none;
        background: transparent;
        border-radius: 18px;
        font-size: 16px;
        color: #2e3844;
        cursor: pointer;
        transition: .22s ease;
        position: relative;
        overflow: hidden;
    }

    .menu li a::before,
    .menu li .menu-static::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, 0.5) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .menu li a:hover::before,
    .menu li .menu-static:hover::before {
        transform: translateX(100%);
    }

    .menu li a:hover,
    .menu li .menu-static:hover {
        background: #eceff3;
        transform: translateX(4px);
    }

    .menu li.active a,
    .menu li.active .menu-static {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(17, 74, 159, 0.28);
    }

    .menu i {
        font-size: 21px;
        width: 24px;
        text-align: center;
        transition: .22s ease;
    }

    .menu li a:hover i,
    .menu li .menu-static:hover i {
        transform: scale(1.15);
    }

    .logout-wrap {
        margin-top: auto;
        padding-top: 18px;
    }

    .logout-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        background: var(--danger);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        padding: 16px 18px;
        cursor: pointer;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .logout-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .logout-btn:hover::before {
        transform: translateX(100%);
    }

    .logout-btn:hover {
        filter: brightness(.94);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 59, 63, .3);
    }

    /* ── MAIN AREA ── */
    .main-area {
        flex: 1;
        width: 100%;
    }

    /* ── TOPBAR ── */
    .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding: 24px 28px 8px 28px;
        animation: slideDown .6s ease both;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .topbar-left {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .menu-btn {
        font-size: 28px;
        cursor: pointer;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        transition: .25s ease;
        border: none;
        background: transparent;
    }

    .menu-btn:hover {
        background: rgba(255, 255, 255, 0.55);
        transform: scale(1.08) rotate(90deg);
    }

    .brand-block img {
        max-width: 120px;
        height: auto;
        object-fit: contain;
    }

    .topbar-right {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .search-wrap {
        position: relative;
        width: 322px;
        max-width: 100%;
    }

    .search-input {
        width: 100%;
        height: 46px;
        padding: 0 48px 0 18px;
        border: none;
        outline: none;
        border-radius: 18px;
        background: #f8f8f8;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        font-size: 15px;
        color: #374151;
        transition: .3s ease;
    }

    .search-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(34, 52, 84, 0.22);
        background: #fff;
    }

    .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 23px;
        pointer-events: none;
    }

    .icon-btn {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 16px;
        background: #f8f8f8;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        color: #4b5563;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: .25s ease;
        position: relative;
    }

    .icon-btn:hover {
        transform: translateY(-3px);
        background: #fff;
    }

    .icon-btn .notif-dot {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 9px;
        height: 9px;
        background: #ff3b3f;
        border-radius: 50%;
        border: 2px solid #f8f8f8;
        animation: notifPulse 2s ease-in-out infinite;
    }

    @keyframes notifPulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 59, 63, .4);
        }

        50% {
            transform: scale(1.15);
            box-shadow: 0 0 0 5px rgba(255, 59, 63, 0);
        }
    }

    .profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-info {
        text-align: left;
    }

    .profile-info strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
    }

    .profile-info span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 13px;
    }

    .avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #124d9d;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        cursor: pointer;
        border: none;
        transition: .25s ease;
        animation: avatarPulse 3s ease-in-out infinite;
    }

    @keyframes avatarPulse {

        0%,
        100% {
            box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        }

        50% {
            box-shadow: 0 10px 28px rgba(17, 74, 159, .45);
        }
    }

    .avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }

    /* ── CONTENT ── */
    .content {
        max-width: 1080px;
        margin: 10px auto 0;
        padding: 0 24px 36px;
    }

    .welcome {
        background: rgba(248, 248, 248, 0.96);
        border-radius: var(--radius-xl);
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        padding: 32px 34px;
        margin-bottom: 34px;
        opacity: 0;
        transform: translateY(24px);
        animation: fadeUp .7s ease .2s forwards;
        border-left: 4px solid var(--primary);
    }

    .welcome h2 {
        margin: 0 0 10px;
        font-size: 22px;
        font-weight: 700;
    }

    .welcome p {
        margin: 0;
        color: #64748b;
        font-size: 16px;
    }

    /* ── DASHBOARD GRID ── */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 34px;
    }

    .card {
        background: rgba(248, 248, 248, 0.97);
        border-radius: 26px;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        padding: 22px 24px 24px;
        min-height: 210px;
        transition: .3s ease;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(28px);
    }

    .card.visible {
        animation: cardPop .55s ease forwards;
    }

    @keyframes cardPop {
        from {
            opacity: 0;
            transform: translateY(28px) scale(.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .card::after {
        content: "";
        position: absolute;
        inset: auto -30% -45% auto;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(17, 74, 159, 0.10), transparent 68%);
        pointer-events: none;
    }

    .card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 24px 44px rgba(34, 52, 84, 0.24);
    }

    .card-icon {
        width: 66px;
        height: 62px;
        border-radius: 18px;
        background: #124d9d;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-bottom: 14px;
        box-shadow: 0 12px 22px rgba(17, 74, 159, .20);
        transition: .3s ease;
    }

    .card:hover .card-icon {
        transform: rotate(-8deg) scale(1.1);
    }

    .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        line-height: 1.4;
    }

    .card-badge {
        font-size: 13px;
        font-weight: 700;
        color: #114a9f;
        background: #e8f0ff;
        padding: 6px 10px;
        border-radius: 999px;
        transition: .25s ease;
    }

    .card:hover .card-badge {
        background: #114a9f;
        color: #fff;
    }

    .card-text {
        color: #5a6675;
        font-size: 14px;
        margin-bottom: 14px;
        min-height: 18px;
    }

    .card-number {
        font-size: 42px;
        font-weight: 800;
        color: #114a9f;
        line-height: 1;
        margin-top: 10px;
        letter-spacing: 1px;
    }

    /* ── STATUS GRID ── */
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .status-card {
        background: rgba(248, 248, 248, 0.97);
        border-radius: 26px;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        padding: 28px 32px 24px;
        transition: .3s ease;
        opacity: 0;
        transform: translateY(24px);
    }

    .status-card.visible {
        animation: fadeUp .65s ease forwards;
    }

    .status-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 44px rgba(34, 52, 84, 0.22);
    }

    .status-card h3 {
        margin: 0 0 22px;
        font-size: 20px;
        font-weight: 700;
    }

    .status-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border-radius: 14px;
        background: #f2f6fb;
    }

    .status-row span:first-child {
        font-size: 16px;
        color: #374151;
        font-weight: 500;
    }

    .status-value {
        font-size: 22px;
        font-weight: 800;
        color: #114a9f;
    }

    /* ── OVERLAY ── */
    .rating-summary-grid {
        grid-template-columns: repeat(4, 1fr);
        margin-top: -6px;
    }

    .rating-card .card-number {
        display: flex;
        align-items: baseline;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 36px;
    }

    .rating-card .card-number small {
        font-size: 16px;
        font-weight: 700;
        color: #5a6675;
    }

    .rating-highlight {
        font-size: 24px;
        font-weight: 800;
        color: #114a9f;
        line-height: 1.25;
        margin-top: 14px;
    }

    .rating-subtext {
        margin-top: 8px;
        font-size: 14px;
        color: #5a6675;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-top: 34px;
    }

    .analytics-card {
        min-height: 360px;
    }

    .analytics-span-2 {
        grid-column: span 2;
    }

    .analytics-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 22px;
    }

    .analytics-head h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .analytics-head p {
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .chart-wrap {
        position: relative;
        height: 260px;
    }

    .chart-wrap-lg {
        height: 320px;
    }

    .empty-analytics {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6b7280;
        font-size: 15px;
        background: #f2f6fb;
        border-radius: 20px;
        border: 1px dashed #d8dde6;
        padding: 20px;
    }

    .distribution-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 18px;
    }

    .distribution-item {
        background: #f2f6fb;
        border-radius: 16px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .distribution-item strong {
        font-size: 14px;
    }

    .distribution-item span {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .distribution-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .pressure-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: 18px;
    }

    .pressure-card {
        background: #f2f6fb;
        border-radius: 18px;
        padding: 16px;
        border: 1px solid #dbe4f0;
        transition: .25s ease;
    }

    .pressure-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 26px rgba(34, 52, 84, .14);
    }

    .pressure-card strong {
        display: block;
        font-size: 15px;
        margin-bottom: 8px;
    }

    .pressure-count {
        font-size: 30px;
        font-weight: 800;
        color: #114a9f;
        line-height: 1;
    }

    .pressure-meter {
        height: 8px;
        border-radius: 999px;
        background: #dbe4f0;
        overflow: hidden;
        margin: 14px 0 10px;
    }

    .pressure-meter span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #114a9f;
    }

    .pressure-high .pressure-meter span,
    .tone-high {
        background: #ff5a52;
    }

    .pressure-medium .pressure-meter span,
    .tone-medium {
        background: #d6a300;
    }

    .pressure-low .pressure-meter span,
    .tone-low {
        background: #4ecb71;
    }

    .pressure-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 800;
        color: #536273;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .pressure-pill::before {
        content: "";
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: currentColor;
    }

    .ops-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 18px;
    }

    .ops-card {
        background: #f2f6fb;
        border-radius: 18px;
        padding: 16px;
        min-height: 122px;
        border: 1px solid #dbe4f0;
    }

    .ops-card .label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .ops-card .value {
        color: #114a9f;
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
    }

    .ops-card .sub {
        color: #64748b;
        font-size: 12px;
        margin-top: 9px;
        line-height: 1.45;
    }

    .compact-list {
        display: grid;
        gap: 10px;
    }

    .compact-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        background: #f2f6fb;
        border-radius: 14px;
        padding: 12px 14px;
    }

    .compact-row strong {
        font-size: 14px;
    }

    .compact-row span {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .doctor-load-list {
        display: grid;
        gap: 14px;
    }

    .doctor-load-item {
        background: #f2f6fb;
        border-radius: 16px;
        padding: 14px 16px;
    }

    .doctor-load-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .doctor-load-top strong {
        font-size: 14px;
    }

    .doctor-load-top span {
        color: #114a9f;
        font-weight: 800;
        font-size: 13px;
        white-space: nowrap;
    }

    .load-bar {
        height: 9px;
        border-radius: 999px;
        overflow: hidden;
        background: #dbe4f0;
    }

    .load-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #114a9f, #4ecbff);
    }

    .load-meta {
        display: flex;
        justify-content: space-between;
        color: #64748b;
        font-size: 12px;
        margin-top: 8px;
        gap: 12px;
    }

    .table-card {
        margin-top: 34px;
    }

    .booking-review-card {
        margin-top: 30px;
    }

    .booking-filter-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .booking-filter-form .form-select {
        min-width: 180px;
        border-radius: 12px;
        border: 1px solid #d8dde6;
        padding: 10px 12px;
        background: #fff;
    }

    .booking-filter-form .btn {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 14px;
    }

    .booking-review-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .booking-review-table thead th {
        text-align: left;
        font-size: 13px;
        color: #6b7280;
        font-weight: 700;
        padding: 0 10px 8px;
        white-space: nowrap;
    }

    .booking-review-table tbody tr {
        background: #f2f6fb;
    }

    .booking-review-table tbody td {
        padding: 14px 10px;
        font-size: 13px;
        vertical-align: middle;
    }

    .booking-review-table tbody td:first-child {
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
    }

    .booking-review-table tbody td:last-child {
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .performance-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .performance-table thead th {
        text-align: left;
        font-size: 13px;
        color: #6b7280;
        font-weight: 700;
        padding: 0 14px 8px;
        white-space: nowrap;
    }

    .performance-table tbody tr {
        background: #f2f6fb;
    }

    .performance-table tbody td {
        padding: 16px 14px;
        font-size: 14px;
        vertical-align: middle;
    }

    .performance-table tbody td:first-child {
        border-top-left-radius: 18px;
        border-bottom-left-radius: 18px;
    }

    .performance-table tbody td:last-child {
        border-top-right-radius: 18px;
        border-bottom-right-radius: 18px;
    }

    .doctor-cell strong {
        display: block;
        font-size: 15px;
    }

    .doctor-cell span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 12px;
    }

    .feedback-cell {
        min-width: 230px;
        max-width: 280px;
    }

    .feedback-summary {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.45;
    }

    .feedback-empty {
        color: #6b7280;
        font-size: 12px;
    }

    .table-rating {
        font-size: 20px;
        font-weight: 800;
        color: #114a9f;
    }

    .table-rating small {
        font-size: 13px;
        color: #6b7280;
        font-weight: 700;
    }

    .trend-pill,
    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .trend-up {
        background: #ebfbf0;
        color: #237245;
    }

    .trend-down {
        background: #ffeceb;
        color: #be332d;
    }

    .trend-neutral {
        background: #eef2f7;
        color: #536273;
    }

    .status-excellent {
        background: #ebfbf0;
        color: #237245;
    }

    .status-good {
        background: #e8f0ff;
        color: #114a9f;
    }

    .status-average {
        background: #fff7d9;
        color: #9a6f00;
    }

    .status-low {
        background: #ffeceb;
        color: #be332d;
    }

    .status-neutral {
        background: #eef2f7;
        color: #536273;
    }

    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .18);
        opacity: 0;
        pointer-events: none;
        transition: .25s ease;
        z-index: 900;
    }

    .overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    /* ── POPUP ── */
    .popup-layer {
        position: fixed;
        inset: 0;
        z-index: 3000;
        pointer-events: none;
    }

    .popup-card {
        position: absolute;
        background: rgba(248, 248, 248, 0.98);
        border-radius: 32px;
        box-shadow: 0 22px 45px rgba(0, 0, 0, 0.22);
        opacity: 0;
        transform: translateY(16px) scale(.97);
        transition: .28s cubic-bezier(.4, 0, .2, 1);
        pointer-events: none;
    }

    .popup-card.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .profile-card {
        top: 90px;
        right: 36px;
        width: min(740px, calc(100vw - 40px));
        padding: 24px 26px 20px;
    }

    .profile-top {
        display: flex;
        gap: 22px;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .profile-avatar-large {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        background: #0b3d82;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 66px;
        flex-shrink: 0;
    }

    .profile-main {
        flex: 1;
    }

    .profile-main h2 {
        margin: 10px 0 12px;
        font-size: 28px;
        font-weight: 700;
    }

    .profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        margin-bottom: 10px;
        color: #4b5563;
        font-size: 15px;
    }

    .profile-meta span {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-subline {
        color: #374151;
        font-size: 17px;
        margin-bottom: 18px;
        padding-left: 22px;
    }

    .profile-desc {
        font-size: 15px;
        color: #3f4b58;
        line-height: 1.7;
        max-width: 610px;
        margin-bottom: 22px;
    }

    .profile-actions {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .primary-action {
        border: none;
        background: #114a9f;
        color: #fff;
        border-radius: 14px;
        height: 48px;
        padding: 0 24px;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(17, 74, 159, .24);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .primary-action::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .2), transparent);
        transform: translateX(-100%);
        transition: .4s ease;
    }

    .primary-action:hover::before {
        transform: translateX(100%);
    }

    .primary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 74, 159, .36);
    }

    .secondary-action {
        border: none;
        background: #fff;
        color: #3d4752;
        border-radius: 12px;
        min-width: 76px;
        height: 48px;
        padding: 0 18px;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 8px 14px rgba(0, 0, 0, .12);
        transition: .25s ease;
    }

    .secondary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, .16);
    }

    /* ── LEAVE CARD ── */
    .leave-card {
        top: 112px;
        right: 36px;
        width: min(520px, calc(100vw - 32px));
        padding: 26px 24px 24px;
        max-height: calc(100vh - 140px);
        overflow: auto;
    }

    .leave-head h3 {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 700;
    }

    .leave-head p {
        margin: 0 0 18px;
        font-size: 14px;
        color: #7b8190;
        line-height: 1.6;
    }

    .leave-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .leave-left {
        padding-right: 20px;
        border-right: 1px solid #d6dbe3;
    }

    .leave-right {
        padding-left: 4px;
    }

    .mini-status-card {
        background: #f2f2f2;
        border: 1px solid #d9dce2;
        border-radius: 14px;
        padding: 18px 16px;
        margin-bottom: 18px;
    }

    .mini-status-card h4 {
        margin: 0 0 12px;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.5;
    }

    .leave-date {
        color: #617191;
        font-size: 16px;
        line-height: 1.35;
        margin-bottom: 8px;
    }

    .leave-inline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 15px;
    }

    .pill {
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .pill.pending {
        color: var(--pending);
        background: var(--pending-bg);
        border-color: #f0da8d;
    }

    .pill.approved {
        color: #1f9b4b;
        background: var(--approved-bg);
        border-color: #b9ebc8;
    }

    .pill.rejected {
        color: var(--rejected);
        background: var(--rejected-bg);
        border-color: #f5c0bc;
    }

    .leave-form-group {
        margin-bottom: 14px;
    }

    .leave-form-group label {
        display: block;
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .leave-input,
    .leave-textarea {
        width: 100%;
        border: 1px solid #cfd6df;
        border-radius: 10px;
        background: #f3f3f3;
        min-height: 44px;
        padding: 10px 12px;
        font-size: 15px;
        outline: none;
        color: #2f3a45;
        transition: .25s ease;
    }

    .leave-input:focus,
    .leave-textarea:focus {
        border-color: #114a9f;
        background: #fff;
        transform: translateY(-1px);
    }

    .leave-textarea {
        min-height: 92px;
        resize: vertical;
    }

    .two-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .submit-leave-btn {
        margin-top: 10px;
        height: 44px;
        border: none;
        border-radius: 10px;
        background: #114a9f;
        color: #fff;
        padding: 0 18px;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(17, 74, 159, .2);
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .submit-leave-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .18), transparent);
        transform: translateX(-100%);
        transition: .4s ease;
    }

    .submit-leave-btn:hover::before {
        transform: translateX(100%);
    }

    .submit-leave-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 26px rgba(17, 74, 159, .32);
    }

    .history-card {
        background: #f7f7f7;
        border: 1px solid #d8dde6;
        border-radius: 16px;
        padding: 14px 14px 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, .08);
        margin-bottom: 14px;
        transition: .25s ease;
    }

    .history-card:hover {
        transform: translateX(4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
    }

    .history-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .history-top .date {
        font-size: 15px;
        line-height: 1.45;
    }

    .history-details {
        color: #617191;
        font-size: 15px;
        line-height: 1.5;
    }

    .reveal {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity .65s ease, transform .65s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(28px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width:1099px) {
        .content {
            max-width: 100%;
        }
    }

    @media (max-width:900px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .rating-summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .status-grid {
            grid-template-columns: 1fr;
        }

        .analytics-grid {
            grid-template-columns: 1fr;
        }

        .pressure-grid,
        .ops-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .analytics-span-2 {
            grid-column: span 1;
        }

        .topbar {
            flex-direction: column;
            align-items: stretch;
        }

        .topbar-right {
            justify-content: space-between;
        }

        .profile-card,
        .leave-card {
            right: 16px;
            left: 16px;
            width: auto;
        }

        .leave-grid {
            grid-template-columns: 1fr;
        }

        .leave-left {
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid #d6dbe3;
            padding-bottom: 16px;
            margin-bottom: 4px;
        }
    }

    @media (max-width:620px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .rating-summary-grid {
            grid-template-columns: 1fr;
        }

        .pressure-grid,
        .ops-grid {
            grid-template-columns: 1fr;
        }

        .topbar {
            padding: 18px 16px 8px 16px;
        }

        .content {
            padding: 0 16px 28px;
        }

        .welcome,
        .card,
        .status-card {
            border-radius: 22px;
        }

        .search-wrap {
            width: 100%;
        }

        .topbar-right {
            flex-direction: column;
            align-items: stretch;
        }

        .profile {
            justify-content: space-between;
        }

        .profile-top {
            flex-direction: column;
        }

        .profile-subline {
            padding-left: 0;
        }

        .profile-actions {
            flex-wrap: wrap;
        }

        .two-inputs {
            grid-template-columns: 1fr 1fr;
        }

        .leave-card {
            top: 88px;
        }

        .distribution-list {
            grid-template-columns: 1fr;
        }
    }
        .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    </style>
</head>

<body>

    <div id="page-loader">
        <div class="loader-ring"></div>
        <div class="loader-label">NUH ADMIN</div>
    </div>

    <div class="layout">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-panel">
                <div class="sidebar-top-logo">
                    <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">
                </div>

                <ul class="menu">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                    <li class="{{ request()->routeIs('admin.appointments*') ? 'active' : '' }}"><a href="{{ route('admin.appointments') }}"><i class="bi bi-calendar-event"></i>Appointments</a></li>
                    <li class="{{ request()->routeIs('admin.doctors*') ? 'active' : '' }}"><a href="{{ route('admin.doctors') }}"><i class="bi bi-heart-pulse"></i>Doctors</a></li>
                    <li class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}"><a href="{{ route('admin.patients') }}"><i class="bi bi-people"></i>Patients</a></li>
                    <li class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}"><a href="{{ route('admin.staff') }}"><i class="bi bi-person-badge"></i>Staff</a></li>
                    <li class="{{ request()->routeIs('admin.rooms*') ? 'active' : '' }}"><a href="{{ route('admin.rooms') }}"><i class="bi bi-building"></i>Rooms</a></li>
                    <li class="{{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><a href="{{ route('admin.departments') }}"><i class="bi bi-hospital"></i>Departments</a></li>
                </ul>

                <a href="{{ url('/') }}" class="logout-btn"
                    style="display:flex;align-items:center;justify-content:center;">
                    Log out
                </a>
            </div>
        </aside>

        <div class="overlay" id="overlay"></div>

        <main class="main-area">

            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn" id="menuBtn" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="brand-block">
                        <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">

                    </div>
                </div>

                <div class="topbar-right">
                    <div class="search-wrap">
                        <input class="search-input" type="text" placeholder="Search patients...">
                        <i class="bi bi-search search-icon"></i>
                    </div>

                    <button class="icon-btn" type="button" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="notif-dot"></span>
                    </button>
                    <a href="{{ route('admin.settings') }}" class="icon-btn settings-link" title="Settings" aria-label="Settings">
                        <i class="bi bi-gear-fill"></i>
                    </a>

                    <div class="profile">
                        <div class="profile-info">
                            <strong>Admin Robert</strong>
                            <span>Administrator</span>
                        </div>
                        <button class="avatar" type="button" id="profileBtn">R</button>
                    </div>
                </div>
            </header>

            <section class="content">
                @php
                $ratingSummary = $ratingDashboard['summary'] ?? [];
                $ratingCharts = $ratingDashboard['charts'] ?? [];
                $ratingsRows = $ratingDashboard['ratingsRows'] ?? collect();
                $topRatedDoctor = $ratingSummary['topRatedDoctor'] ?? null;
                $daysPressure = $hospitalAnalytics['daysPressure'] ?? [];
                $queuePressure = $hospitalAnalytics['queuePressure'] ?? [];
                $weeklyPressureChart = [
                    'labels' => collect($daysPressure['weekly'] ?? [])->pluck('label')->values(),
                    'values' => collect($daysPressure['weekly'] ?? [])->pluck('count')->values(),
                ];
                @endphp

                <div class="welcome">
                    <h2>Welcome to Hospital Dashboard</h2>
                    <p>Overview of your hospital management system</p>
                </div>

                <div class="dashboard-grid" id="dashGrid">

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-people"></i></div>
                            <div class="card-badge">Patients</div>
                        </div>
                        <div class="card-title">Total Patients</div>
                        <div class="card-text">All registered patients</div>
                        <div class="card-number">{{ $patientsCount }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="card-badge">Doctors</div>
                        </div>
                        <div class="card-title">Total Doctors</div>
                        <div class="card-text">All active doctors</div>
                        <div class="card-number">{{ $doctorsCount }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="card-badge">Visits</div>
                        </div>
                        <div class="card-title">Total Appointments</div>
                        <div class="card-text">All scheduled appointments</div>
                        <div class="card-number" id="appointmentsCountValue">{{ $appointmentsCount }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-building"></i></div>
                            <div class="card-badge">Rooms</div>
                        </div>
                        <div class="card-title">Total Rooms</div>
                        <div class="card-text">All hospital rooms</div>
                        <div class="card-number">{{ $roomsCount }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-door-open"></i></div>
                            <div class="card-badge">Open</div>
                        </div>
                        <div class="card-title">Available Rooms</div>
                        <div class="card-text">Rooms currently available</div>
                        <div class="card-number">{{ $availableRooms }}</div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-calendar-date"></i></div>
                            <div class="card-badge">Today</div>
                        </div>
                        <div class="card-title">Today Appointments</div>
                        <div class="card-text">Appointments for today</div>
                        <div class="card-number" id="todayAppointmentsValue">{{ $todayAppointments }}</div>
                    </div>

                </div>

                <div class="dashboard-grid rating-summary-grid">
                    <div class="card rating-card reveal">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-star-half"></i></div>
                            <div class="card-badge">Ratings</div>
                        </div>
                        <div class="card-title">Average Doctor Rating</div>
                        <div class="card-text">Live average across all submitted doctor reviews</div>
                        <div class="card-number">
                            {{ $ratingSummary['averageDoctorRating'] !== null ? number_format($ratingSummary['averageDoctorRating'], 1) : '0.0' }}
                            <small>/ 5</small>
                        </div>
                    </div>

                    <div class="card rating-card reveal">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-trophy"></i></div>
                            <div class="card-badge">Top Rated</div>
                        </div>
                        <div class="card-title">Top Rated Doctor</div>
                        <div class="card-text">Highest average rating based on submitted reviews</div>
                        <div class="rating-highlight">{{ $topRatedDoctor?->name ?? 'No reviews yet' }}</div>
                        <div class="rating-subtext">
                            {{ $topRatedDoctor ? number_format((float) $topRatedDoctor->avg_rating, 1) . ' / 5' : 'Waiting for the first review' }}
                        </div>
                    </div>

                    <div class="card rating-card reveal">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-exclamation-diamond"></i></div>
                            <div class="card-badge">Attention</div>
                        </div>
                        <div class="card-title">Needs Attention</div>
                        <div class="card-text">Doctors currently averaging below 3.5 stars</div>
                        <div class="card-number">{{ $ratingSummary['needsAttentionCount'] ?? 0 }}</div>
                    </div>

                    <div class="card rating-card reveal">
                        <div class="card-head">
                            <div class="card-icon"><i class="bi bi-chat-square-text"></i></div>
                            <div class="card-badge">Reviews</div>
                        </div>
                        <div class="card-title">Total Reviews</div>
                        <div class="card-text">Submitted doctor rating records in the database</div>
                        <div class="card-number">{{ $ratingSummary['totalReviews'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="status-grid">
                    <div class="status-card reveal">
                        <h3>System Status</h3>
                        <div class="status-list">
                            <div class="status-row">
                                <span>Room Occupancy</span>
                                <span class="status-value">{{ $occupiedRooms }}</span>
                            </div>
                            <div class="status-row">
                                <span>Active Doctors</span>
                                <span class="status-value">{{ $doctorsCount }}</span>
                            </div>
                            <div class="status-row">
                                <span>Registered Patients</span>
                                <span class="status-value">{{ $patientsCount }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-card reveal">
                        <h3>Operational Status</h3>
                        <div class="status-list">
                            <div class="status-row">
                                <span>Scheduled Appointments</span>
                                <span class="status-value"
                                    id="scheduledAppointmentsValue">{{ $appointmentsCount }}</span>
                            </div>
                            <div class="status-row">
                                <span>Available Rooms</span>
                                <span class="status-value">{{ $availableRooms }}</span>
                            </div>
                            <div class="status-row">
                                <span>Occupied Rooms</span>
                                <span class="status-value">{{ $occupiedRooms }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="analytics-grid">
                    <div class="status-card reveal analytics-card analytics-span-2">
                        <div class="analytics-head">
                            <div>
                                <h3>Days Pressure Analysis</h3>
                                <p>Appointment volume by weekday from real booking dates.</p>
                            </div>
                            <div class="card-badge">Live Data</div>
                        </div>
                        <div class="chart-wrap chart-wrap-lg">
                            <canvas id="weeklyPressureChart"></canvas>
                            <div class="empty-analytics" id="weeklyPressureEmpty" style="display:none;">
                                Weekly pressure will appear once appointments have dates.
                            </div>
                        </div>
                        <div class="distribution-list">
                            <div class="distribution-item">
                                <strong>Most Busy Day</strong>
                                <span>{{ ($daysPressure['mostBusyDay']['label'] ?? 'N/A') }} - {{ $daysPressure['mostBusyDay']['count'] ?? 0 }}</span>
                            </div>
                            <div class="distribution-item">
                                <strong>Least Busy Day</strong>
                                <span>{{ ($daysPressure['leastBusyDay']['label'] ?? 'N/A') }} - {{ $daysPressure['leastBusyDay']['count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-card reveal analytics-card analytics-span-2">
                        <div class="analytics-head">
                            <div>
                                <h3>Hourly Pressure</h3>
                                <p>Peak booking windows from appointment time data.</p>
                            </div>
                            <div class="card-badge">Heatmap</div>
                        </div>
                        <div class="pressure-grid">
                            @foreach(($daysPressure['hourlySlots'] ?? collect()) as $slot)
                            <div class="pressure-card pressure-{{ $slot['tone'] }}">
                                <strong>{{ $slot['label'] }}</strong>
                                <div class="pressure-count">{{ $slot['count'] }}</div>
                                <div class="pressure-meter"><span style="width: {{ max(4, $slot['percent']) }}%"></span></div>
                                <div class="pressure-pill">{{ $slot['pressure'] }} pressure</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="status-card reveal analytics-card analytics-span-2">
                        <div class="analytics-head">
                            <div>
                                <h3>Queue Pressure Analysis</h3>
                                <p>Current waiting load from today's pending and confirmed appointments.</p>
                            </div>
                            <div class="card-badge">{{ $queuePressure['estimatedPressure'] ?? 'Low' }}</div>
                        </div>
                        <div class="ops-grid">
                            <div class="ops-card">
                                <div class="label">Busy Now</div>
                                <div class="value">{{ $queuePressure['busyNow'] ?? 0 }}</div>
                                <div class="sub">Current waiting patients</div>
                            </div>
                            <div class="ops-card">
                                <div class="label">Average Waiting</div>
                                <div class="value">{{ $queuePressure['averageWaitMinutes'] ?? 0 }}m</div>
                                <div class="sub">Based on expected appointment time</div>
                            </div>
                            <div class="ops-card">
                                <div class="label">Queue Overload</div>
                                <div class="value">{{ $queuePressure['queueOverload']['count'] ?? 0 }}</div>
                                <div class="sub">{{ $queuePressure['queueOverload']['department'] ?? 'No waiting queue' }}</div>
                            </div>
                            <div class="ops-card">
                                <div class="label">Pressure Score</div>
                                <div class="value">{{ $queuePressure['pressureScore'] ?? 0 }}%</div>
                                <div class="sub">Estimated waiting pressure</div>
                            </div>
                        </div>
                        <div class="distribution-list">
                            <div>
                                <h4 style="margin:0 0 10px;font-size:15px;">Fast Departments</h4>
                                <div class="compact-list">
                                    @forelse(($queuePressure['fastDepartments'] ?? collect()) as $department)
                                    <div class="compact-row">
                                        <strong>{{ $department['department'] }}</strong>
                                        <span>{{ $department['average_wait'] }}m avg</span>
                                    </div>
                                    @empty
                                    <div class="empty-analytics" style="min-height:88px;">No fast queue departments right now.</div>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <h4 style="margin:0 0 10px;font-size:15px;">Delayed Doctors</h4>
                                <div class="compact-list">
                                    @forelse(($queuePressure['delayedDoctors'] ?? collect()) as $doctor)
                                    <div class="compact-row">
                                        <strong>{{ $doctor['doctor'] }}</strong>
                                        <span>{{ $doctor['average_wait'] }}m avg</span>
                                    </div>
                                    @empty
                                    <div class="empty-analytics" style="min-height:88px;">No delayed doctors right now.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="analytics-grid">
                    <div class="status-card reveal analytics-card analytics-span-2">
                        <div class="analytics-head">
                            <div>
                                <h3>Doctors Rating Overview</h3>
                                <p>Average rating per reviewed doctor.</p>
                            </div>
                            <div class="card-badge">Overview</div>
                        </div>
                        <div class="chart-wrap chart-wrap-lg">
                            <canvas id="doctorRatingsOverviewChart"></canvas>
                            <div class="empty-analytics" id="doctorRatingsOverviewEmpty" style="display:none;">
                                Ratings will appear here once doctor reviews are submitted.
                            </div>
                        </div>
                    </div>

                    <div class="status-card reveal analytics-card">
                        <div class="analytics-head">
                            <div>
                                <h3>Ratings Distribution</h3>
                                <p>Doctor performance grouped by rating band.</p>
                            </div>
                            <div class="card-badge">Distribution</div>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="doctorRatingsDistributionChart"></canvas>
                            <div class="empty-analytics" id="doctorRatingsDistributionEmpty" style="display:none;">
                                No distribution is available until ratings are submitted.
                            </div>
                        </div>
                        <div class="distribution-list">
                            @foreach(($ratingCharts['distribution']['labels'] ?? []) as $index => $label)
                            <div class="distribution-item">
                                <strong>
                                    <span class="distribution-dot"
                                        style="background: {{ ['#4ecb71', '#114a9f', '#d6a300', '#ff5a52'][$index] ?? '#114a9f' }}"></span>
                                    {{ $label }}
                                </strong>
                                <span>{{ ($ratingCharts['distribution']['percentages'][$index] ?? 0) }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="status-card reveal analytics-card">
                        <div class="analytics-head">
                            <div>
                                <h3>Rating Trend</h3>
                                <p>Rated doctors only, ordered by average score.</p>
                            </div>
                            <div class="card-badge">Trend</div>
                        </div>
                        <div class="chart-wrap">
                            <canvas id="doctorRatingsTrendChart"></canvas>
                            <div class="empty-analytics" id="doctorRatingsTrendEmpty" style="display:none;">
                                Rating trend will appear once doctor reviews are submitted.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="status-card visible table-card" id="doctors-ratings-table-section">
                    <div class="analytics-head">
                        <div>
                            <h3>Doctors Ratings Table</h3>
                            <p>Doctor rating averages, review volume, latest feedback, trend, and status.</p>
                        </div>
                        <div class="card-badge">Ratings</div>
                    </div>

                    <div class="table-wrap">
                        <table class="performance-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Avg Rating</th>
                                    <th>Reviews Count</th>
                                    <th>Latest Feedback</th>
                                    <th>Trend</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratingsRows as $row)
                                <tr>
                                    <td class="doctor-cell">
                                        <strong>{{ $row['name'] }}</strong>
                                        <span>{{ $row['reviews_count'] ? 'Reviewed doctor' : 'No reviews submitted yet' }}</span>
                                    </td>
                                    <td>{{ $row['department'] }}</td>
                                    <td class="table-rating">
                                        {{ number_format((float) $row['avg_rating'], 1) }}
                                        <small>/ 5</small>
                                    </td>
                                    <td>{{ $row['reviews_count'] }}</td>
                                    <td class="feedback-cell">
                                        @if($row['latest_feedback_rating'] !== null)
                                        @php($feedbackComment = $row['latest_feedback_comment'] ?: 'No comment provided.')
                                        <div class="feedback-summary" title="{{ $feedbackComment }}">
                                            {{ 'Rating ' . number_format((float) $row['latest_feedback_rating'], 1) . ' - "' . \Illuminate\Support\Str::limit($feedbackComment, 72) . '"' }}
                                        </div>
                                        @else
                                        <span class="feedback-empty">No reviews yet</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="trend-pill trend-{{ $row['trend']['direction'] }}">
                                            {{ $row['trend']['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill status-{{ $row['status']['tone'] }}">
                                            {{ $row['status']['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-analytics">Doctor ratings data will appear here when doctors are available.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="status-card reveal booking-review-card">
                    <div class="analytics-head">
                        <div>
                            <h3>Website Feedback After Booking</h3>
                            <p>Patient feedback about website and booking experience (separate from doctor ratings).</p>
                        </div>
                        <div class="card-badge">Bookings</div>
                    </div>

                    <form method="GET" action="{{ route('admin.dashboard') }}" class="booking-filter-form">
                        <select name="payment_status" class="form-select">
                            <option value="">All Payment Statuses</option>
                            @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'paid' => 'Paid', 'failed' => 'Failed', 'canceled' => 'Canceled'] as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected(request('payment_status')===$statusValue)>
                                {{ $statusLabel }}</option>
                            @endforeach
                        </select>

                        <select name="rating" class="form-select">
                            <option value="">All Ratings</option>
                            @for($r = 5; $r >= 1; $r--)
                            <option value="{{ $r }}" @selected(request('rating')==(string) $r)>{{ $r }} Star</option>
                            @endfor
                        </select>

                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Reset</a>
                    </form>

                    <div class="table-wrap">
                        <table class="booking-review-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Booking Ref</th>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Website Rating</th>
                                    <th>Feedback</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($websiteRatings as $item)
                                <tr>
                                    <td>{{ $item->patient->full_name ?? trim((($item->appointment->first_name ?? '') . ' ' . ($item->appointment->last_name ?? ''))) ?: 'N/A' }}
                                    </td>
                                    <td>#{{ $item->appointment_id ?? '-' }}</td>
                                    <td>{{ $item->appointment->doctor->name ?? 'N/A' }}</td>
                                    <td>{{ $item->appointment->department->name_en ?? optional(optional($item->appointment)->doctor)->department->name_en ?? 'N/A' }}
                                    </td>
                                    <td>{{ $item->appointment?->date ? \Carbon\Carbon::parse($item->appointment->date)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td>{{ $item->appointment->time ?? '-' }}</td>
                                    <td>{{ $item->rating }}/5</td>
                                    <td>{{ $item->comment ?? '-' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $item->appointment->payment_status ?? 'pending')) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-analytics">No booking-review records available yet.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $websiteRatings->links() }}
                    </div>
                </div>

            </section>
        </main>
    </div>

    <div class="popup-layer">

        <div class="popup-card profile-card" id="profileCard">
            <div class="profile-top">
                <div class="profile-avatar-large">R</div>
                <div class="profile-main">
                    <h2>Administrator Robert</h2>
                    <div class="profile-meta">
                        <span><i class="bi bi-clock"></i> 4 years experience</span>
                        <span><i class="bi bi-geo-alt"></i> NUH, Bani Suef</span>
                    </div>
                    <div class="profile-subline">Male | 45 Years | EGY</div>
                    <div class="profile-desc">Administrator Robert demonstrates high professionalism, consistently
                        performing duties with accuracy and strong teamwork.</div>
                    <div class="profile-actions">
                        <button class="primary-action" type="button" id="leaveRequestBtn">
                            <i class="bi bi-calendar-event"></i>
                            Leave Requests
                        </button>
                        <button class="secondary-action" type="button" id="closeProfileBtn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="popup-card leave-card" id="leaveCard">
            <div class="leave-head">
                <h3>Leave Request Form</h3>
                <p>Submit a new leave request or view your current and past leave requests.</p>
            </div>
            <div class="leave-grid">
                <div class="leave-left">
                    <div class="mini-status-card">
                        <h4>Current Leave <br>Request Status</h4>
                        <div class="leave-date">Dec 20, 2025 -<br>Dec 27, 2025</div>
                        <div class="leave-inline">
                            <span>Duration: 8 days</span>
                            <span class="pill pending">Pending</span>
                        </div>
                    </div>

                    <form>
                        <div class="leave-form-group">
                            <label>Administrator Name</label>
                            <input type="text" class="leave-input" value="Robert">
                        </div>
                        <div class="leave-form-group">
                            <label>Department</label>
                            <input type="text" class="leave-input" value="Administration">
                        </div>
                        <div class="two-inputs">
                            <div class="leave-form-group">
                                <label>Start Date</label>
                                <input type="text" class="leave-input" placeholder="DD/MM/YYYY">
                            </div>
                            <div class="leave-form-group">
                                <label>End Date</label>
                                <input type="text" class="leave-input" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="leave-form-group">
                            <label>Reason for Leave</label>
                            <textarea class="leave-textarea"
                                placeholder="Please provide a reason for your leave request..."></textarea>
                        </div>
                        <button type="button" class="submit-leave-btn">Submit Request</button>
                    </form>
                </div>

                <div class="leave-right">
                    <h3 style="margin:0 0 16px;font-size:15px;font-weight:500;">Leave Request History</h3>

                    <div class="history-card">
                        <div class="history-top">
                            <div class="date">Nov 15,<br>2025 - Nov<br>20, 2025</div>
                            <span class="pill approved">Approved</span>
                        </div>
                        <div class="history-details">Duration: 6 days<br>Reason: Medical Conference</div>
                    </div>

                    <div class="history-card">
                        <div class="history-top">
                            <div class="date">Oct 10,<br>2025 - Oct<br>12, 2025</div>
                            <span class="pill rejected">Rejected</span>
                        </div>
                        <div class="history-details">Duration: 3 days<br>Reason: Personal</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    window.addEventListener('load', () => {
        setTimeout(() => document.getElementById('page-loader').classList.add('hidden'), 700);
    });

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
    }

    menuBtn.addEventListener('click', toggleSidebar);

    const profileBtn = document.getElementById('profileBtn');
    const profileCard = document.getElementById('profileCard');
    const leaveCard = document.getElementById('leaveCard');
    const leaveRequestBtn = document.getElementById('leaveRequestBtn');
    const closeProfileBtn = document.getElementById('closeProfileBtn');

    function openProfileCard() {
        overlay.classList.add('show');
        profileCard.classList.add('show');
        leaveCard.classList.remove('show');
    }

    function openLeaveCard() {
        overlay.classList.add('show');
        leaveCard.classList.add('show');
    }

    function closeAllPopups() {
        profileCard.classList.remove('show');
        leaveCard.classList.remove('show');
    }

    overlay.addEventListener('click', () => {
        closeSidebar();
        closeAllPopups();
        overlay.classList.remove('show');
    });

    profileBtn.addEventListener('click', e => {
        e.stopPropagation();
        profileCard.classList.contains('show') ?
            (closeAllPopups(), overlay.classList.remove('show')) :
            openProfileCard();
    });

    leaveRequestBtn.addEventListener('click', e => {
        e.stopPropagation();
        openLeaveCard();
    });

    closeProfileBtn.addEventListener('click', () => {
        closeAllPopups();
        overlay.classList.remove('show');
    });

    document.addEventListener('click', e => {
        if (
            !profileCard.contains(e.target) &&
            !leaveCard.contains(e.target) &&
            !profileBtn.contains(e.target) &&
            !menuBtn.contains(e.target)
        ) {
            closeAllPopups();
        }
    });

    const cards = document.querySelectorAll('.dashboard-grid .card');
    const cardObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const idx = Array.from(cards).indexOf(e.target);
                e.target.style.animationDelay = (idx * 0.08) + 's';
                e.target.classList.add('visible');
                cardObserver.unobserve(e.target);
            }
        });
    }, {
        threshold: 0.15
    });

    cards.forEach(c => cardObserver.observe(c));

    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                revealObserver.unobserve(e.target);
            }
        });
    }, {
        threshold: 0.2
    });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    const topbar = document.querySelector('.topbar');
    window.addEventListener('scroll', () => {
        topbar.style.transition = 'box-shadow .3s ease';
        topbar.style.boxShadow = window.scrollY > 10 ?
            '0 6px 24px rgba(34,52,84,0.15)' :
            'none';
    });

    let menuOpen = false;
    menuBtn.addEventListener('click', () => {
        menuOpen = !menuOpen;
        menuBtn.querySelector('i').className = menuOpen ? 'bi bi-x-lg' : 'bi bi-list';
    });

    async function refreshAppointmentMetrics() {
        const appointmentsCountEl = document.getElementById('appointmentsCountValue');
        const scheduledAppointmentsEl = document.getElementById('scheduledAppointmentsValue');
        const todayAppointmentsEl = document.getElementById('todayAppointmentsValue');

        if (!appointmentsCountEl || !scheduledAppointmentsEl || !todayAppointmentsEl) {
            return;
        }

        try {
            const response = await fetch('/api/appointments');
            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const result = await response.json();
            const appointments = Array.isArray(result.data) ? result.data : [];

            const today = new Date().toISOString().slice(0, 10);
            const todayCount = appointments.filter(item => item.date === today).length;

            appointmentsCountEl.textContent = appointments.length;
            scheduledAppointmentsEl.textContent = appointments.length;
            todayAppointmentsEl.textContent = todayCount;
        } catch (error) {
            console.error('Failed to refresh appointment metrics:', error);
        }
    }

    refreshAppointmentMetrics();
    setInterval(refreshAppointmentMetrics, 30000);

    const ratingOverviewData = @json($ratingCharts['overview'] ?? ['labels' => [], 'values' => [], 'colors' => []]);
    const ratingDistributionData = @json($ratingCharts['distribution'] ?? ['labels' => [], 'counts' => [],
        'percentages' => []
    ]);
    const ratingTrendData = @json($ratingCharts['trend'] ?? ['labels' => [], 'values' => []]);
    const weeklyPressureData = @json($weeklyPressureChart);

    function toggleChartEmptyState(canvasId, emptyId, hasData) {
        const canvas = document.getElementById(canvasId);
        const empty = document.getElementById(emptyId);

        if (!canvas || !empty) {
            return;
        }

        canvas.style.display = hasData ? 'block' : 'none';
        empty.style.display = hasData ? 'none' : 'flex';
    }

    function initRatingCharts() {
        const hasOverviewData = Array.isArray(ratingOverviewData.values) && ratingOverviewData.values.length > 0;
        const hasDistributionData = Array.isArray(ratingDistributionData.counts) && ratingDistributionData.counts.some(
            value => Number(value) > 0);
        const hasTrendData = Array.isArray(ratingTrendData.values) && ratingTrendData.values.some(value => value !==
            null);

        toggleChartEmptyState('doctorRatingsOverviewChart', 'doctorRatingsOverviewEmpty', hasOverviewData);
        toggleChartEmptyState('doctorRatingsDistributionChart', 'doctorRatingsDistributionEmpty', hasDistributionData);
        toggleChartEmptyState('doctorRatingsTrendChart', 'doctorRatingsTrendEmpty', hasTrendData);

        if (typeof Chart === 'undefined') {
            return;
        }

        if (hasOverviewData) {
            new Chart(document.getElementById('doctorRatingsOverviewChart'), {
                type: 'bar',
                data: {
                    labels: ratingOverviewData.labels,
                    datasets: [{
                        data: ratingOverviewData.values,
                        backgroundColor: ratingOverviewData.colors,
                        borderRadius: 12,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        x: {
                            min: 0,
                            max: 5,
                            grid: {
                                color: '#dbe4f0'
                            },
                            ticks: {
                                color: '#5a6675'
                            },
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#2f3a45'
                            },
                        }
                    }
                }
            });
        }

        if (hasDistributionData) {
            new Chart(document.getElementById('doctorRatingsDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: ratingDistributionData.labels,
                    datasets: [{
                        data: ratingDistributionData.counts,
                        backgroundColor: ['#4ecb71', '#114a9f', '#d6a300', '#ff5a52'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        },
                    }
                }
            });
        }

        if (hasTrendData) {
            new Chart(document.getElementById('doctorRatingsTrendChart'), {
                type: 'line',
                data: {
                    labels: ratingTrendData.labels,
                    datasets: [{
                        label: 'Average Rating',
                        data: ratingTrendData.values,
                        borderColor: '#114a9f',
                        backgroundColor: 'rgba(17, 74, 159, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#114a9f',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 5,
                            grid: {
                                color: '#dbe4f0'
                            },
                            ticks: {
                                color: '#5a6675'
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#5a6675'
                            },
                        }
                    }
                }
            });
        }
    }

    function initHospitalAnalyticsCharts() {
        const hasWeeklyPressure = Array.isArray(weeklyPressureData.values) && weeklyPressureData.values.some(value =>
            Number(value) > 0);

        toggleChartEmptyState('weeklyPressureChart', 'weeklyPressureEmpty', hasWeeklyPressure);

        if (typeof Chart === 'undefined') {
            return;
        }

        if (hasWeeklyPressure) {
            new Chart(document.getElementById('weeklyPressureChart'), {
                type: 'bar',
                data: {
                    labels: weeklyPressureData.labels,
                    datasets: [{
                        label: 'Appointments',
                        data: weeklyPressureData.values,
                        backgroundColor: '#114a9f',
                        borderRadius: 14,
                        borderSkipped: false,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#dbe4f0'
                            },
                            ticks: {
                                color: '#5a6675',
                                precision: 0
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#2f3a45'
                            },
                        }
                    }
                }
            });
        }
    }

    initHospitalAnalyticsCharts();
    initRatingCharts();
    </script>

</body>

</html>
