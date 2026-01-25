const mongoose = require('mongoose');

const UserSchema = new mongoose.Schema({
    name: { type: String, required: true },
    email: { type: String, required: true, unique: true },
    password: { type: String, required: true },
    role: { type: String, enum: ['student', 'faculty', 'admin'], required: true },
    department: { type: String },
    rollNumber: { type: String }, // For students
    designation: { type: String }, // For faculty
});

module.exports = mongoose.model('User', UserSchema);
