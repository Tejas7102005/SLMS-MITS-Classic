import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import Navbar from './Navbar';

const FacultyDashboard = () => {
    const [leaves, setLeaves] = useState([]);
    const navigate = useNavigate();

    const fetchLeaves = async () => {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.get('http://localhost:5000/api/leave/pending', {
                headers: { 'x-auth-token': token }
            });
            setLeaves(res.data);
        } catch (err) {
            console.error(err);
            if (err.response && err.response.status === 403) {
                alert('Access Denied');
                navigate('/');
            }
        }
    };

    useEffect(() => {
        if (!localStorage.getItem('token')) navigate('/');
        fetchLeaves();
    }, []);

    const handleAction = async (id, status) => {
        const comment = prompt(`Enter reason for ${status} (Optional):`);
        try {
            const token = localStorage.getItem('token');
            await axios.put(`http://localhost:5000/api/leave/${id}/action`,
                { status, facultyComment: comment },
                { headers: { 'x-auth-token': token } }
            );
            alert(`Leave ${status} successfully`);
            fetchLeaves();
        } catch (err) {
            console.error(err);
            alert('Error updating leave status');
        }
    };

    // Logout handled by Navbar

    return (
        <div className="min-h-screen bg-secondary">
            <Navbar role="Faculty" />
            <div className="max-w-6xl mx-auto p-8">

                <div className="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 className="text-xl font-semibold mb-4 text-green-600">Register New Student</h2>
                    <form onSubmit={async (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        const data = Object.fromEntries(formData.entries());
                        try {
                            const token = localStorage.getItem('token');
                            await axios.post('http://localhost:5000/api/auth/add-student', data, {
                                headers: { 'x-auth-token': token }
                            });
                            alert('Student Added Successfully');
                            e.target.reset();
                        } catch (err) {
                            console.error(err);
                            alert(err.response?.data?.msg || 'Error adding student');
                        }
                    }} className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="text" name="name" placeholder="Name" className="px-4 py-2 border rounded-md" required />
                            <input type="text" name="rollNumber" placeholder="Roll Number" className="px-4 py-2 border rounded-md" required />
                            <input type="email" name="email" placeholder="Email" className="px-4 py-2 border rounded-md" required />
                            <input type="text" name="department" placeholder="Department" className="px-4 py-2 border rounded-md" required />
                            <input type="password" name="password" placeholder="Password" className="px-4 py-2 border rounded-md" required />
                        </div>
                        <button type="submit" className="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Register Student</button>
                    </form>
                </div>

                <div className="bg-white p-6 rounded-lg shadow-md">
                    <h2 className="text-xl font-semibold mb-4">Pending Leave Requests</h2>
                    {leaves.length === 0 ? <p className="text-gray-500">No pending requests.</p> : (
                        <div className="overflow-x-auto">
                            <table className="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student</th>
                                        <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                                        <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                                        <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dates</th>
                                        <th className="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {leaves.map(leave => (
                                        <tr key={leave._id}>
                                            <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <div className="flex items-center">
                                                    <div>
                                                        <p className="text-gray-900 whitespace-no-wrap">{leave.studentId?.name || 'Unknown'}</p>
                                                        <p className="text-gray-600 whitespace-no-wrap text-xs">{leave.studentId?.email}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{leave.studentId?.department || 'N/A'}</td>
                                            <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">{leave.reason}</td>
                                            <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                {new Date(leave.startDate).toLocaleDateString()} - {new Date(leave.endDate).toLocaleDateString()}
                                            </td>
                                            <td className="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <button onClick={() => handleAction(leave._id, 'Approved')} className="bg-green-500 text-white px-3 py-1 rounded mr-2 hover:bg-green-600">Approve</button>
                                                <button onClick={() => handleAction(leave._id, 'Rejected')} className="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default FacultyDashboard;
