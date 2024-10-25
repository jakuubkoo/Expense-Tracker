import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'
import Login from "./pages/Login";
import Dashboard from "./pages/Dashboard";
import {UserProvider} from "./Utils/UserContext";

/**
 * Component defining the routing structure of the application.
 */
export default function AppRouter() {
    return (
        <UserProvider>
            <Router>
                <Routes>

                    <Route exact path="/login" element={<Login/>}/>
                    <Route exact path="/dashboard" element={<Dashboard/>}/>

                    {/*<Route path="*" element={<NotFoundComponent/>}/>*/}
                </Routes>
            </Router>
        </UserProvider>
    )
}
