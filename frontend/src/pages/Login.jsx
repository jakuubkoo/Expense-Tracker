import '../App.css';
import PageLayout from "../components/PageLayout";
import { useState } from "react";
import axios from 'axios';
import { useNavigate } from "react-router-dom";

function Login() {

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState(null);

    const navigate = useNavigate();

    const handleSubmit = async () => {
        // Check if the email or password is empty
        if (!email || !password) {
            setError('Both email and password are required.');
            return;
        }

        try {
            const response = await axios.post('http://localhost:8000/api/login_check', {
                email: email,
                password: password
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                withCredentials: true // Send cookies if needed
            });


            const token = response.data.token;

            // Save the token in localStorage (or use cookies if preferred)
            localStorage.setItem('jwtToken', token);

            // Redirect to the dashboard
            navigate('/dashboard');

        } catch (err) {
            console.error(err);
            setError('Invalid email or password. Please try again.');
        }
    }

    return (
        <>
            <PageLayout login={true}>

                <div className='flex flex-col justify-center items-center mt-24 space-x-2'>

                    <p className='text-5xl font-bold'>
                        Login
                    </p>

                    {error && <p className="text-red-500 mt-6">{error}</p>} {/* Display error if any */}

                    <div className='flex flex-col space-y-4 mt-8'>
                        <input
                            onChange={e => setEmail(e.target.value)}
                            value={email}
                            type='email'
                            className='border border-gray-800 rounded-lg p-2'
                            placeholder='example@example.com'
                            required
                        />
                        <input
                            onChange={e => setPassword(e.target.value)}
                            value={password}
                            type='password'
                            className='border border-gray-800 rounded-lg p-2'
                            placeholder='**********'
                            required
                        />

                        <button onClick={handleSubmit} type={"submit"} className='bg-blue-600 p-2 text-white font-bold text-[20px]'>
                            LOGIN
                        </button>
                    </div>

                </div>

            </PageLayout>
        </>
    );
}

export default Login;
