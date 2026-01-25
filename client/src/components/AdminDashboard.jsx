import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import Navbar from './Navbar';

const AdminDashboard = () => {
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        department: '',
        designation: 'Coordinator'
    });

    useEffect(() => {
        const role = localStorage.getItem('role');
        if (role !== 'admin') {
            navigate('/');
        }
    }, []);

    const { name, email, password, department, designation } = formData;

    const onChange = e => setFormData({ ...formData, [e.target.name]: e.target.value });

    const onSubmit = async e => {
        e.preventDefault();
        try {
            const token = localStorage.getItem('token');
            await axios.post('http://localhost:5000/api/auth/add-faculty', formData, {
                headers: { 'x-auth-token': token }
            });
            alert('Faculty Added Successfully');
            setFormData({
                name: '',
                email: '',
                password: '',
                department: '',
                designation: 'Coordinator'
            });
        } catch (err) {
            console.error(err);
            alert(err.response?.data?.msg || 'Error adding faculty');
        }
    };

    // Logout handled by Navbar

    return (
        <div className="min-h-screen bg-secondary">
            <Navbar role="Admin" />
            <div className="max-w-4xl mx-auto p-8">

                <div className="bg-white p-6 rounded-lg shadow-md">
                    <h2 className="text-xl font-semibold mb-4 text-blue-600">Register New Faculty</h2>
                    <form onSubmit={onSubmit} className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-gray-700">Name</label>
                                <input type="text" name="name" value={name} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                            <div>
                                <label className="block text-gray-700">Email</label>
                                <input type="email" name="email" value={email} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                            <div>
                                <label className="block text-gray-700">Password</label>
                                <input type="password" name="password" value={password} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                            <div>
                                <label className="block text-gray-700">Department</label>
                                <input type="text" name="department" value={department} onChange={onChange} className="w-full px-4 py-2 border rounded-md" required />
                            </div>
                            <div>
                                <label className="block text-gray-700">Designation</label>
                                <select name="designation" value={designation} onChange={onChange} className="w-full px-4 py-2 border rounded-md">
                                    <option value="Coordinator">Coordinator</option>
                                    <option value="HOD">HOD</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" className="w-full px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Register Faculty</button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default AdminDashboard;
