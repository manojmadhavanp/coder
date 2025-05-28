<div>
    <h2>Manager Login</h2>
    <form id="loginForm">
        <div>
            <label for="identifier">Email or Mobile:</label>
            <input type="text" id="identifier" name="identifier" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <div id="errorMessage" style="color: red; margin-top: 10px;"></div>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const identifier = document.getElementById('identifier').value;
    const password = document.getElementById('password').value;
    const errorMessageDiv = document.getElementById('errorMessage');
    errorMessageDiv.textContent = ''; // Clear previous errors

    // Basic client-side validation (optional, good practice)
    if (!identifier || !password) {
        errorMessageDiv.textContent = 'Please enter both identifier and password.';
        return;
    }

    try {
        const response = await fetch('/manage/login', { // This endpoint will be created later
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // If you have CSRF tokens or other headers, add them here
            },
            body: JSON.stringify({ identifier, password }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            if (data.token) {
                localStorage.setItem('manage_auth_token', data.token);
                // Assuming the backend sends a redirect_url or we hardcode it
                // For now, redirecting to a placeholder '/manage/dashboard'
                window.location.href = data.redirect_url || '/manage/dashboard';
            } else {
                // This case implies success: true but no token, which is unusual for login
                errorMessageDiv.textContent = 'Login successful, but no authorization token was received. Please contact support.';
                console.warn('Login success response did not contain a token.');
            }
        } else {
            // Use the message from the backend if available, otherwise a generic one
            errorMessageDiv.textContent = data.message || 'Login failed. Please check your credentials and try again.';
        }
    } catch (error) {
        console.error('Login request error:', error);
        errorMessageDiv.textContent = 'An error occurred while trying to log in. Please ensure you are connected to the network and try again.';
    }
});
</script>
