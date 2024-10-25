import React, { createContext, useState, useEffect } from 'react';
import axios from "axios";

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
    const [userData, setUserData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState(null);
    const loginToken = localStorage.getItem('jwtToken');

    // UserContext.js
    useEffect(() => {
        const fetchUserData = async () => {
            if (loginToken) {
                try {
                    const response = await axios.post('http://localhost:8000/api/user/status', null, {
                        headers: {
                            'Accept': '*/*',
                            'Authorization': `Bearer ${loginToken}`,
                        },
                    });

                    const data = await response.data;
                    console.log("Fetched data:", data); // Log the fetched data

                    if (response.status === 200) {
                        setUserData({
                            firstName: data.user_status.firstName,
                            lastName: data.user_status.lastName,
                            email: data.user_status.email,
                            roles: data.user_status.roles,
                        });
                        setStats(data.stats);
                    }
                } catch (error) {
                    console.log('Error fetching user data:', error);
                } finally {
                    console.log("userData:", userData); // Check state after setting it
                    setLoading(false);
                }
            } else {
                setLoading(false);
            }
        };
        fetchUserData();
    }, [loginToken]);


    return (
        <UserContext.Provider value={{ userData, stats, loading }}>
            {children}
        </UserContext.Provider>
    );
};
