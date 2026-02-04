import React from 'react';
import { useNavigate } from 'react-router-dom';

import ThemeToggle from './ThemeToggle';

const Navbar = ({ role }) => {
    const navigate = useNavigate();

    const handleLogout = () => {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        navigate('/');
    };

    return (
        <nav className="bg-primary dark:bg-gray-900 text-white shadow-lg transition-colors duration-200">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    <div className="flex items-center">
                        <img
                            src="/mits_logo.png"
                            alt="MITS Logo"
                            className="h-10 w-auto mr-3 bg-white rounded-full p-1"
                        />
                        <span className="font-bold text-lg tracking-wide text-white">MITS Gwalior SLMS</span>
                    </div>
                    <div className="flex items-center space-x-4">
                        <span className="text-sm font-medium opacity-90 capitalize">
                            {role} Portal
                        </span>
                        <ThemeToggle />
                        <button
                            onClick={handleLogout}
                            className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition duration-150 ease-in-out shadow-sm"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
