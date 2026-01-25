const mongoose = require('mongoose');

const LeaveSchema = new mongoose.Schema({
    studentId: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    reason: { type: String, required: true },
    startDate: { type: Date, required: true },
    endDate: { type: Date, required: true },
    status: { type: String, enum: ['Pending', 'Approved', 'Rejected'], default: 'Pending' },
    facultyComment: { type: String },
}, { timestamps: true });

module.exports = mongoose.model('Leave', LeaveSchema);
