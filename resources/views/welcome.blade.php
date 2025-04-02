<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaMask Login</title>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex justify-center items-center h-screen bg-gray-900 text-white">
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-4">Login with MetaMask</h1>
        <button id="connectBtn" onclick="connectMetaMask()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Connect Wallet
        </button>
        <div id="statusMessage" class="mt-4 text-sm"></div>
    </div>

    <script>
    const statusElement = document.getElementById('statusMessage');
    const connectButton = document.getElementById('connectBtn');

    function updateStatus(message, isError = false) {
        statusElement.innerHTML = message;
        statusElement.className = 'mt-4 text-sm ' + (isError ? 'text-red-400' : 'text-green-400');
    }

    async function connectMetaMask() {
        // Clear previous messages
        updateStatus('Connecting to MetaMask...');
        connectButton.disabled = true;
        
        try {
            if (typeof window.ethereum === 'undefined') {
                throw new Error("MetaMask is not installed. Please install it: https://metamask.io/download/");
            }

            const web3 = new Web3(window.ethereum);
            
            // Request account access
            updateStatus('Requesting account access...');
            const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            const address = accounts[0].toLowerCase();
            updateStatus(`Connected with address: ${address.substring(0, 6)}...${address.substring(38)}`);

            // Get CSRF token from Laravel meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Step 1: Request nonce from the backend
            updateStatus('Getting authentication nonce...');
            let nonceResponse = await fetch('/metamask-get-nonce', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ address })
            });

            // Check for errors and validate response is JSON
            const contentType = nonceResponse.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response. Check server logs.');
            }

            let nonceData = await nonceResponse.json();
            
            if (!nonceResponse.ok) {
                throw new Error(nonceData.error || 'Failed to get nonce');
            }
            
            let nonce = nonceData.nonce;
            updateStatus('Nonce received. Please sign the message in MetaMask...');

            // Step 2: Sign nonce with MetaMask
            let signature = await web3.eth.personal.sign(nonce, address, "");
            updateStatus('Message signed. Verifying signature...');

            // Step 3: Send the signed message to the backend for verification
            let authResponse = await fetch('/metamask-auth', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ address, signature })
            });

            // Check for errors and validate response is JSON
            const authContentType = authResponse.headers.get('content-type');
            if (!authContentType || !authContentType.includes('application/json')) {
                const errorText = await authResponse.text();
                console.error('Server returned non-JSON response:', errorText);
                throw new Error('Server returned non-JSON response. Check server logs.');
            }

            let authData = await authResponse.json();
            
            if (!authResponse.ok) {
                throw new Error(authData.error || 'Authentication failed');
            }

            if (authData.success) {
                updateStatus('Authentication successful! Redirecting to dashboard...');
                setTimeout(() => {
                    window.location.href = "/dashboard";
                }, 1000);
            } else {
                throw new Error(authData.error || 'Authentication failed for unknown reason');
            }
        } catch (error) {
            console.error("MetaMask login error:", error);
            updateStatus('Error: ' + error.message, true);
        } finally {
            connectButton.disabled = false;
        }
    }
    </script>
</body>
</html>