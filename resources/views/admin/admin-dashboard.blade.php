<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f7;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #f5f7fa;
            border-right: 1.5px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 32px 0 0 0;
        }
        .sidebar .logo-row {
            display: flex;
            align-items: center;
            margin-left: 18px;
            margin-bottom: 36px;
        }
        .sidebar .logo-row img {
            height: 70px;
            margin-right: 10px;
        }
        .sidebar .brand-text {
            font-size: 1.1rem;
            font-weight: bold;
            color: #222;
            line-height: 1.2;
        }
        .sidebar nav ul, .sidebar .bottom-links {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
        }
        .sidebar nav ul li, .sidebar .bottom-links li {
            padding: 16px 0 16px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #222;
            font-size: 1.05em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .sidebar nav ul li.active {
            font-weight: 700;
        }
        .sidebar .bottom-links {
            margin-top: auto;
        }
        .sidebar .bottom-links li {
            padding: 12px 0 12px 32px;
        }
        .sidebar .bottom-links li.logout {
            color: #d32f2f;
            font-weight: bold;
        }
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f5f5f7;
            padding: 32px 32px 0 32px;
        }
        .topbar {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
        }
        .searchbar {
            background: #ede6ef;
            border-radius: 18px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            width: 420px;
        }
        .searchbar input {
            border: none;
            background: transparent;
            outline: none;
            font-size: 1.1em;
            width: 100%;
            font-family: inherit;
        }
        .topbar .icon {
            font-size: 2em;
            margin-right: 18px;
            color: #222;
        }
        .action-btns {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }
        .action-btns button {
            background: #fff;
            border: 2px solid #5c2f91;
            border-radius: 22px;
            padding: 7px 16px;
            font-size: 1.2em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .content-row {
            display: flex;
            gap: 32px;
        }
        .course-list {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .course-card {
            background: #edeffe;
            border: 3px solid #5c2f91;
            border-radius: 32px;
            padding: 28px 32px;
            margin-bottom: 0;
        }
        .course-card .title {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 6px;
        }
        .course-card .progress {
            font-size: 0.98em;
            color: #444;
        }
        .pending-panel {
            flex: 1;
            background: #edeffe;
            border: 3px solid #5c2f91;
            border-radius: 24px;
            padding: 24px 28px;
            min-width: 320px;
            max-width: 350px;
        }
        .pending-panel .panel-title {
            font-weight: bold;
            font-size: 1.1em;
            border-bottom: 2px solid #222;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .pending-panel .student {
            font-size: 1em;
            color: #222;
        }
        @media (max-width: 900px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; }
            .main { padding: 16px; }
            .content-row { flex-direction: column; }
            .pending-panel { max-width: 100%; min-width: 0; margin-top: 24px; }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo-row">
            <img src='{{ asset('images/logo.png') }}' alt='Logo'>
            <div class="brand-text">Ascendo Review<br>and Training Center</div>
        </div>
        <nav>
            <ul>
                <li class="active"><span>&#128200;</span> Dashboard</li>
                <li><span>&#128100;</span> Student Registration</li>
                <li><span>&#128221;</span> Enrollment</li>
                <li><span>&#128451;</span> Programs</li>
                <li><span>&#128101;</span> Professors</li>
            </ul>
        </nav>
        <div style="flex: 1;"></div>
        <ul class="bottom-links">
            <li><span>&#10067;</span> Help</li>
            <li><span>&#9881;&#65039;</span> Settings</li>
            <li class="logout"><span>&#8634;</span> Logout</li>
        </ul>
    </aside>
    <!-- Main Content -->
    <div class="main">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="searchbar">
                <span style="font-size: 1.3em; margin-right: 10px;">&#9776;</span>
                <input type="text" placeholder="Search">
                <span style="font-size: 1.2em; color: #888; margin-left: 8px;">&#128269;</span>
            </div>
            <div style="flex: 1;"></div>
            <span class="icon">&#128172;</span>
            <span class="icon">&#128100;</span>
        </div>
        <!-- Action Buttons -->
        <div class="action-btns">
            <button><span>&#10133;</span></button>
            <button><span>&#9998;</span></button>
            <button><span>&#128465;</span></button>
            <button><span>&#128465;</span></button>
        </div>
        <!-- Course Cards and Pending Registration -->
        <div class="content-row">
            <!-- Course Cards -->
            <div class="course-list">
                <div class="course-card">
                    <div class="title">Fundamentals of Engineering</div>
                    <div class="progress">0% complete</div>
                </div>
                <div class="course-card">
                    <div class="title">Fundamentals of Engineering</div>
                    <div class="progress">0% complete</div>
                </div>
                <div class="course-card">
                    <div class="title">Fundamentals of Engineering</div>
                    <div class="progress">0% complete</div>
                </div>
            </div>
            <!-- Pending Student Registration -->
            <div class="pending-panel">
                <div class="panel-title">Pending Student Registration</div>
                <div class="student">John Doe</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
