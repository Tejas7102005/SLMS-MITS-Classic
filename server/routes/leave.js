const express = require('express');
const router = express.Router();
const auth = require('../middleware/auth');
const Leave = require('../models/Leave');

// Apply for Leave (Student)
router.post('/apply', auth, async (req, res) => {
    try {
        if (req.user.role !== 'student') {
            return res.status(403).json({ msg: 'Only students can apply for leave' });
        }
        const { reason, startDate, endDate } = req.body;
        const newLeave = new Leave({
            studentId: req.user.id,
            reason,
            startDate,
            endDate
        });
        const leave = await newLeave.save();
        res.json(leave);
    } catch (err) {
        console.error(err.message);
        res.status(500).send('Server Error');
    }
});

// Get My Leaves (Student)
router.get('/my-leaves', auth, async (req, res) => {
    try {
        const leaves = await Leave.find({ studentId: req.user.id }).sort({ createdAt: -1 });
        res.json(leaves);
    } catch (err) {
        console.error(err.message);
        res.status(500).send('Server Error');
    }
});

// Get Pending Leaves (Faculty)
router.get('/pending', auth, async (req, res) => {
    try {
        if (req.user.role !== 'faculty') {
            return res.status(403).json({ msg: 'Access denied' });
        }
        const leaves = await Leave.find({ status: 'Pending' }).populate('studentId', 'name email department');
        res.json(leaves);
    } catch (err) {
        console.error(err.message);
        res.status(500).send('Server Error');
    }
});

// Approve/Reject Leave (Faculty)
router.put('/:id/action', auth, async (req, res) => {
    try {
        if (req.user.role !== 'faculty') {
            return res.status(403).json({ msg: 'Access denied' });
        }
        const { status, facultyComment } = req.body;

        let leave = await Leave.findById(req.params.id);
        if (!leave) return res.status(404).json({ msg: 'Leave not found' });

        leave.status = status;
        if (facultyComment) leave.facultyComment = facultyComment;

        await leave.save();
        res.json(leave);
    } catch (err) {
        console.error(err.message);
        res.status(500).send('Server Error');
    }
});

module.exports = router;
