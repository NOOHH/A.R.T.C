<!DOCTYPE html>
<html>
<head>
    <title>CACHE TEST - SmartPrep Dashboard</title>
    <style>
        body { 
            background: red; 
            color: white; 
            font-size: 24px; 
            text-align: center; 
            padding: 50px; 
            font-family: Arial, sans-serif;
        }
        .test-box {
            background: #1e40af;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="test-box">
        <h1>🚨 CACHE TEST - CHANGES ARE WORKING! 🚨</h1>
        <p>If you can see this RED background, the changes are being applied!</p>
        <p>User: {{ Auth::user()->name }}</p>
        <p>Time: {{ now() }}</p>
        <p>Active Websites: {{ $activeWebsites->count() }}</p>
        
        <hr>
        
        <h2>Navigation Test</h2>
        <p><a href="{{ route('dashboard.customize-website') }}" style="color: yellow; font-size: 18px;">🎨 CUSTOMIZE WEBSITE LINK 🎨</a></p>
        
        <hr>
        
        <h3>Instructions:</h3>
        <ol style="text-align: left;">
            <li>If you see this page, our changes ARE working</li>
            <li>Press Ctrl+Shift+R for hard refresh</li>
            <li>Clear browser cache completely</li>
            <li>Try incognito/private browsing</li>
        </ol>
    </div>
</body>
</html>
