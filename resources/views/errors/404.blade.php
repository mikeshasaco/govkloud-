<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | GovKloud</title>
    <link rel="icon" type="image/png" sizes="32x32"
        href="https://govkloudstorage.blob.core.windows.net/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16"
        href="https://govkloudstorage.blob.core.windows.net/assets/favicon-16x16.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0f1a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Subtle background glow */
        body::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(210, 180, 140, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .container {
            text-align: center;
            position: relative;
            z-index: 1;
            padding: 2rem;
        }

        .logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 2rem;
            filter: drop-shadow(0 8px 30px rgba(210, 180, 140, 0.2));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #D2B48C, #C4A77D);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 0.75rem;
        }

        .error-message {
            font-size: 1rem;
            color: #94a3b8;
            max-width: 400px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, #D2B48C, #C4A77D);
            color: #0a0f1a;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(210, 180, 140, 0.25);
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(210, 180, 140, 0.4);
        }

        .btn-back:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="https://govkloudstorage.blob.core.windows.net/assets/govkloud-logo.png"
             alt="GovKloud" class="logo">

        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
        </p>

        <a href="{{ url('/') }}" class="btn-back">
            ← Go Back Home
        </a>
    </div>
</body>

</html>
