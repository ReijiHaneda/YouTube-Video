const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const port = 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Dummy database (replace with actual database setup)
let users = [];

// POST endpoint to handle login requests
app.post('/login', (req, res) => {
    const { emailOrMobile, password } = req.body;

    // Basic validation (replace with actual validation)
    if (!emailOrMobile || !password) {
        return res.status(400).json({ error: 'Email/Mobile and password are required' });
    }

    // Simulate storing user data (replace with database storage)
    users.push({ emailOrMobile, password });
    console.log('New login:', emailOrMobile, password);

    res.status(200).json({ message: 'Login data received successfully' });
});

// Start server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
