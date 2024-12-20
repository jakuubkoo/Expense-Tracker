import Navbar from "./navbar";
import {useContext} from "react";
import {UserContext} from "../Utils/UserContext";

const PageLayout = ({ login = false, children }) => {

    const { userData, stats, loading } = useContext(UserContext);

    if (loading) return <p>Loading...</p>;

    return (
        <div className='bg-[#1E1E1E] h-screen'>
            <Navbar login={login} userData={userData} />

            {children}
        </div>
    )
};

export default PageLayout;
