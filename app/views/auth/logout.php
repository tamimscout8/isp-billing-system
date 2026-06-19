<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logout-container {
            width: 100%;
            max-width: 400px;
        }

        .logout-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            text-align: center;
        }

        .logout-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .logout-box h1 {
            color: #28a745;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .logout-box p {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .logout-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .logout-info {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-box">
            <div class="logout-icon">👋</div>
            <h1>Logout Successful</h1>
            <p>You have been successfully logged out from ISP Billing System.</p>
            
            <div class="logout-buttons">
                <a href="?page=login" class="btn btn-primary">Login Again</a>
                <a href="?page=login" class="btn btn-secondary">Go Home</a>
            </div>

            <div class="logout-info">
                ✓ Your session has been securely closed. Your account information is safe.
            </div>
        </div>
    </div>
</body>
</html>
