const mongoose = require('mongoose');
const dotenv = require('dotenv');
const bcrypt = require('bcryptjs');
const User = require('./models/User');

dotenv.config();

mongoose.connect(process.env.MONGO_URI)
    .then(() => console.log('MongoDB Connected'))
    .catch(err => {
        console.error('MongoDB Connection Error:', err);
        process.exit(1);
    });

const seedUsers = async () => {
    try {
        await User.deleteMany({}); // Clear existing users
        console.log('Cleared existing users');

        const salt = await bcrypt.genSalt(10);
        const password = await bcrypt.hash('123456', salt); // Hash strict password

        const student = new User({
            name: 'John Student',
            email: 'student@test.com',
            password: password,
            role: 'student',
            department: 'CS'
        });

        const faculty = new User({
            name: 'Dr. Faculty',
            email: 'faculty@test.com',
            password: password,
            role: 'faculty',
            department: 'CS',
            designation: 'HOD'
        });

        const admin = new User({
            name: 'Super Admin',
            email: 'admin@test.com',
            password: password,
            role: 'admin'
        });

        await student.save();
        await faculty.save();
        await admin.save();

        console.log('Database seeded successfully!');
        console.log('Student: student@test.com / 123456');
        console.log('Faculty: faculty@test.com / 123456');
        console.log('Admin:   admin@test.com   / 123456');
        process.exit();
    } catch (err) {
        console.error('Seeding Error:', err);
        process.exit(1);
    }
};

seedUsers();
