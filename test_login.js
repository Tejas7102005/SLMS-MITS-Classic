async function testLogin() {
    try {
        console.log('Attempting login with student@test.com...');
        const response = await fetch('http://localhost:5000/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: 'student@test.com',
                password: '123456'
            })
        });

        if (response.ok) {
            const data = await response.json();
            console.log('Login Successful!');
            console.log('Token:', data.token ? 'Received' : 'Missing');
            console.log('User Role:', data.user.role);
        } else {
            console.error('Login Failed with status:', response.status);
            const text = await response.text();
            console.error('Error Data:', text);
        }
    } catch (err) {
        console.error('Request Failed:', err.message);
        console.error('Cause:', err.cause);
    }
}

testLogin();
