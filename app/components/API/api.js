import axios from 'axios';
import * as SecureStore from "expo-secure-store";
import {router} from "expo-router";
import {Alert} from "react-native";

const BASE_URL = "https://26cc-2a02-8308-4013-1900-a8ee-3936-4129-e712.ngrok-free.app"

const api = axios.create({
    baseURL: BASE_URL, // Set your API base URL
    timeout: 10000, // Adjust timeout as needed
});

export const signIn = async (email, password) => {
    try {
        // Make API request to login endpoint
        const response = await axios.post(BASE_URL + '/api/login_check', {
            email: email,
            password: password
        });

        // Assuming the response contains a token upon successful login
        const token = response.data.token;

        // Save the token
        await SecureStore.setItemAsync('token', token);

        return response.status
    } catch (error) {
        Alert.alert('Error', error.message);
    }
}

export const signUp = async (firstName, lastName, email, password, passwordConfirmation) => {
    try {
        // Make API request to login endpoint
        const response = await axios.post(BASE_URL + '/api/v1/auth/register', {
            firstName: firstName,
            lastName: lastName,
            email: email,
            password: password,
            passwordConfirmation: passwordConfirmation,
        });

        return response.status
    } catch (error) {
        Alert.alert('Error', error.message);
    }
}

export async function getCurrentUser() {
    try {
        // Make API request to profile endpoint
        const token = await SecureStore.getItemAsync('token');
        const response = await api.get('/api/user', { headers: {"Authorization" : `Bearer ${token}`} });

        if (response.status === 200) {
            return response.data;
        } else if (response.status === 401) {
            // Handle 401 Unauthorized response
            return null;
        } else {
            // Handle other non-200 responses
            throw new Error(`Unexpected response status: ${response.status}`);
        }
    } catch (error) {
        // Check if the error is a network error or something else
        if (error.response && error.response.status !== 401) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx
            console.error('Response error:', error.response.data);
        } else if (error.request && error.response.status !== 401) {
            // The request was made but no response was received
            console.error('Request error:', error.request);
        } else {
            // Something happened in setting up the request that triggered an Error
            if(error.response.status !== 401)
                console.error('Error:', error.message);
        }

        // Alert for errors other than 401
        if (error.response && error.response.status !== 401) {
            Alert.alert('Error', error.message);
        }
    }
}


