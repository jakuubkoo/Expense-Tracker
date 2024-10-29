import React, { createContext, useState, useEffect } from 'react';
import axios from "axios";

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
    const [userData, setUserData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState(null);
    const loginToken = localStorage.getItem('jwtToken');

    const fetchUserData = async () => {
        if (loginToken) {
            try {
                const response = await axios.get('http://localhost:8000/api/user', {
                    headers: {
                        'Accept': '*/*',
                        'Authorization': `Bearer ${loginToken}`,
                    },
                });

                const data = await response.data;
                console.log("Fetched data:", data); // Log the fetched data

                if (response.status == 200) {
                    setUserData({
                        firstName: data.firstName,
                        lastName: data.lastName,
                        email: data.email,
                        expenses: data.expenses,
                    });
                    setStats(data.stats);
                }
            } catch (error) {
                console.log('Error fetching user data:', error);
            } finally {
                setLoading(false);
            }
        } else {
            setLoading(false);
        }
    };

    console.log("userData:", userData); // Check state after setting it

    // UserContext.js
    useEffect(() => {
        fetchUserData();
    }, [loginToken]);

    // Refresh function to call after adding expense
    const refreshUserData = async () => {
        await fetchUserData();
    };


    return (
        <UserContext.Provider value={{ userData, stats, loading, refreshUserData }}>
            {children}
        </UserContext.Provider>
    );
};
