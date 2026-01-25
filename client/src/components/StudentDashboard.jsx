import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import Navbar from './Navbar';

const StudentDashboard = () => {
    const [leaves, setLeaves] = useState([]);
    const [formData, setFormData] = useState({
        reason: '',
        startDate: '',
        endDate: ''
    });
    const navigate = useNavigate();

    const fetchLeaves = async () => {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.get('http://localhost:5000/api/leave/my-leaves', {
                headers: { 'x-auth-token': token }
            });
            setLeaves(res.data);
        } catch (err) {
            console.error(err);
            if (err.response && err.response.status === 401) navigate('/');
        }
    };

    useEffect(() => {
        if (!localStorage.getItem('token')) navigate('/');
        fetchLeaves();
    }, []);

    const { reason, startDate, endDate } = formData;

    const onChange = e => setFormData({ ...formData, [e.target.name]: e.target.value });

    const onSubmit = async e => {
        e.preventDefault();
        try {
            const token = localStorage.getItem('token');
            await axios.post('http://localhost:5000/api/leave/apply', formData, {
                headers: { 'x-auth-token': token }
            });
            alert('Leave Applied Successfully');
            fetchLeaves();
            setFormData({ reason: '', startDate: '', endDate: '' });
        } catch (err) {
            console.error(err);
            alert('Error applying for leave');
        }
    };

    // Logout is now handled by Navbar component

    return (
        <div className="min-h-screen bg-secondary">
            <Navbar role="Student" />
            <div className="max-w-4xl mx-auto p-8">

                <div className="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 className="text-xl font-semibold mb-4">Apply for Leave</h2>
                    <form onSubmit={onSubmit} className="space-y-4">
                        <div>
                            <label className="block text-gray-700">Reason</label>
                            <textarea name="reason" value={reason} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-gray-700">Start Date</label>
                                <input type="date" name="startDate" value={startDate} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                            <div>
                                <label className="block text-gray-700">End Date</label>
                                <input type="date" name="endDate" value={endDate} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                        </div>
                        <button type="submit" className="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Apply Leave</button>
                    </form>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-md">
                    <h2 className="text-xl font-semibold mb-4">My Leave History</h2>
                    <div className="overflow-x-auto">
                        <table className="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                                    <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Start Date</th>
                                    <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">End Date</th>
                                    <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                {leaves.map(leave => (
                                    <tr key={leave._id}>
                                        <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{leave.reason}</td>
                                        <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{new Date(leave.startDate).toLocaleDateString()}</td>
                                        <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{new Date(leave.endDate).toLocaleDateString()}</td>
                                        <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <span className={`relative inline-block px-3 py-1 font-semibold leading-tight rounded-full
                                                ${leave.status === 'Approved' ? 'text-green-800 bg-green-100' :
                                                    leave.status === 'Rejected' ? 'text-red-800 bg-red-100' : 'text-yellow-800 bg-yellow-100'}`}>
                                                <span className="relative">{leave.status}</span>
                                            </span>
                                        </td>
                                        <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{leave.facultyComment || '-'}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
