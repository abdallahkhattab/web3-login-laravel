<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetaMask Login</title>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
</head>
<body>

    <button onclick="connectMetaMask()">Login with MetaMask</button>

    <script>
        async function connectMetaMask() {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const web3 = new Web3(window.ethereum);
                    const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
                    const address = accounts[0];

                    // Send address to Laravel backend for authentication
                    const response = await fetch('/metamask-auth', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ address })
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert('Login successful');
                        window.location.href = "/dashboard";
                    } else {
                        alert('Login failed');
                    }

                } catch (error) {
                    console.error(error);
                    alert('MetaMask connection failed');
                }
            } else {
                alert('MetaMask is not installed');
            }
        }
    </script>

</body>
</html>
