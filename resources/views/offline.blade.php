<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - EXON Klinika</title>
    <link rel="icon" type="image/png" href="/images/icon-192.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .offline-container {
            text-align: center;
            color: white;
            padding: 2rem;
            max-width: 500px;
        }

        .offline-icon {
            font-size: 120px;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(0.95); }
        }

        .offline-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .offline-message {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .retry-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .retry-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .retry-btn:active {
            transform: translateY(0);
        }

        .network-status {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .network-status.online {
            background: rgba(40, 167, 69, 0.2);
            border: 2px solid rgba(40, 167, 69, 0.5);
        }

        .tips {
            margin-top: 2rem;
            text-align: left;
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .tips ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tips li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .tips li:before {
            content: "✓";
            position: absolute;
            left: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">
            <i class="bi bi-wifi-off"></i>
        </div>
        
        <h1 class="offline-title">İnternet Əlaqəsi Yoxdur</h1>
        
        <p class="offline-message">
            Sistem ilə əlaqə qurmaq mümkün olmadı. Zəhmət olmasa internet əlaqənizi yoxlayın və yenidən cəhd edin.
        </p>

        <button class="retry-btn" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-2"></i>
            Yenidən Cəhd Et
        </button>

        <div class="network-status" id="networkStatus">
            <i class="bi bi-exclamation-circle me-2"></i>
            <span id="statusText">Əlaqə yoxlanılır...</span>
        </div>

        <div class="tips">
            <strong><i class="bi bi-lightbulb me-2"></i>Tövsiyələr:</strong>
            <ul>
                <li>Wi-Fi və ya mobil datanızın açıq olduğunu yoxlayın</li>
                <li>Təyyarə rejimini söndürün</li>
                <li>Router-i yenidən başladın</li>
                <li>Başqa səhifə açmağa cəhd edin</li>
            </ul>
        </div>
    </div>

    <script>
        // Check network status
        function updateNetworkStatus() {
            const statusDiv = document.getElementById('networkStatus');
            const statusText = document.getElementById('statusText');

            if (navigator.onLine) {
                statusDiv.classList.add('online');
                statusText.innerHTML = '<i class="bi bi-check-circle me-2"></i>İnternet əlaqəsi bərpa olundu! Səhifə yenilənir...';
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                statusDiv.classList.remove('online');
                statusText.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>Hələ də offline...';
            }
        }

        // Listen for online/offline events
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);

        // Initial check
        setTimeout(updateNetworkStatus, 1000);

        // Periodic check every 3 seconds
        setInterval(() => {
            if (navigator.onLine) {
                // Try to fetch a small resource to verify real connectivity
                fetch('/manifest.json', { cache: 'no-store' })
                    .then(() => {
                        updateNetworkStatus();
                    })
                    .catch(() => {
                        // Still offline
                    });
            }
        }, 3000);
    </script>
</body>
</html>
