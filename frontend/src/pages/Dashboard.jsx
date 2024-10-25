import '../App.css';
import PageLayout from "../components/PageLayout";
import {useContext} from "react";
import {UserContext} from "../Utils/UserContext";

function Dashboard() {

    const { userData, stats, loading } = useContext(UserContext);

    if (loading) return <p>Loading...</p>;



    return (
        <>
            <PageLayout>

                <div className='flex flex-col justify-center items-center space-x-2'>

                    <div className="flex flex-col justify-center items-center mt-16 space-x-2">

                    </div>

                </div>

            </PageLayout>
        </>
    );
}

export default Dashboard;
