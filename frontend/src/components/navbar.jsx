import { useState } from "react";

const Navbar = ({ login, userData }) => {
    const [isOpen, setIsOpen] = useState(false);

    const toggleMenu = () => {
        setIsOpen(!isOpen);
    };

    return (
        <nav className="bg-gray-800 text-white shadow-lg">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-16">
                    <div className="flex items-center">
                        <a href="/" className="text-2xl font-bold">
                            Expense-Tracker
                        </a>
                    </div>
                    {!login && (
                        <>
                            <div className="hidden md:flex space-x-4 items-center">
                                <a href="#" className="hover:text-gray-400">Home</a>
                                <a href="#" className="hover:text-gray-400">About</a>
                                <a href="#" className="hover:text-gray-400">Services</a>
                                <a href="#" className="hover:text-gray-400">Contact</a>
                            </div>
                            <div className="hidden md:flex items-center">
                                <p className="hover:text-gray-400">Hello, {userData.firstName}</p>
                            </div>
                            <div className="md:hidden flex items-center">
                                <button onClick={toggleMenu} className="focus:outline-none">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2"
                                              d={isOpen ? "M6 18L18 6M6 6l12 12" : "M4 6h16M4 12h16m-7 6h7"}></path>
                                    </svg>
                                </button>
                            </div>
                        </>
                    )}
                </div>
            </div>

            {/* Mobile menu */}
            {isOpen && !login && (
                <div className="md:hidden">
                    <a href="#" className="block px-4 py-2 hover:bg-gray-700">
                        Home
                    </a>
                    <a href="#" className="block px-4 py-2 hover:bg-gray-700">
                        About
                    </a>
                    <a href="#" className="block px-4 py-2 hover:bg-gray-700">
                        Services
                    </a>
                    <a href="#" className="block px-4 py-2 hover:bg-gray-700">
                        Contact
                    </a>
                </div>
            )}
        </nav>
    );
};

export default Navbar;
