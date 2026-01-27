<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .container {
            padding: 20px;
        }
        
        h1 {
            font-size: 100px;
            margin-bottom: 20px;
        }
        
        p {
            font-size: 24px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        a {
            display: inline-block;
            padding: 12px 30px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        a:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Page Not Found</p>
        <p style="font-size: 16px; margin-bottom: 40px;">The page you're looking for doesn't exist.</p>
        <a href="/">Go Back Home</a>
    </div>
</body>
</html>
